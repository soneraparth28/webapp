<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mail;
use App\Helper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AdminSettings;
use Fahim\PaypalIPN\PaypalIPNListener;
use Illuminate\Support\Facades\Validator;
use App\Models\PaymentGateways;
use App\Models\Transactions;
use Laravel\Cashier\Exceptions\IncompletePayment;
use App\Models\Notifications;
use App\Models\Conversations;
use App\Models\Messages;
use App\Models\Updates;
use App\Models\PayPerViews;
use Yabacon\Paystack;
use App\Http\Controllers\Traits;

class PayPerViewController extends Controller
{
    use Traits\Functions;

    public function __construct(Request $request, AdminSettings $settings) {
        $this->request = $request;
        $this->settings = $settings::first();
    }
    public function send()
  {
    if ($this->request->isMessage) {
      // Find Message
      $media = Messages::whereId($this->request->id)
            ->wherePrice($this->request->amount)
            ->whereToUserId(auth()->user()->id)
            ->firstOrFail();

      // Verify that the user has not purchased the content
      if (PayPerViews::whereUserId(auth()->user()->id)->whereMessagesId($this->request->id)->first()) {
        return response()->json([
          "success" => false,
          "errors" => ['error' => __('general.already_purchased_content')]
        ]);
      }

    } else {
      // Find Post
      $media = Updates::whereId($this->request->id)
            ->wherePrice($this->request->amount)
            ->where('user_id', '<>', auth()->user()->id)
            ->firstOrFail();

      // Verify that the user has not purchased the content
      if (PayPerViews::whereUserId(auth()->user()->id)->whereUpdatesId($this->request->id)->first()) {
        return response()->json([
          "success" => false,
          "errors" => ['error' => __('general.already_purchased_content')]
        ]);
      }
    }

    $messages = [
      'payment_gateway_ppv.required' => trans('general.choose_payment_gateway')
    ];

  //<---- Validation
  $validator = Validator::make($this->request->all(), [
      'payment_gateway_ppv' => 'required'
      ], $messages);

    if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ]);
        }

    switch ($this->request->payment_gateway_ppv) {
      case 'wallet':
        return $this->sendWallet();
        break;

      case 1:
        return $this->sendPayPal();
        break;

      case 2:
        return $this->sendStripe();
        break;

      case 4:
        return $this->sendCCbill();
        break;

      case 5:
        return $this->sendPaystack();
        break;
    }


    return response()->json([
      'success' => true,
      'insertBody' => '<i></i>'
    ]);

  } // End method Send

  /**
	 *  Send  Wallet
	 *
	 * @return Response
	 */
   protected function sendWallet()
   {
     $amount = $this->request->amount;

     if (auth()->user()->wallet < Helper::amountGross($amount)) {
       return response()->json([
         "success" => false,
         "errors" => ['error' => __('general.not_enough_funds')]
       ]);
     }

     // Check if it is a Message or Post
     $media = $this->request->isMessage ? Messages::find($this->request->id) : Updates::find($this->request->id);

     // Admin and user earnings calculation
     $earnings = $this->earningsAdminUser($media->user()->custom_fee, $amount, null, null);

     // Insert Transaction
     $this->transaction(
         'ppv_'.str_random(25),
         auth()->user()->id,
         0,
         $media->user()->id,
         $amount,
         $earnings['user'],
         $earnings['admin'],
         'Wallet', 'ppv',
         $earnings['percentageApplied'],
         auth()->user()->taxesPayable()
     );

     // Add Earnings to User
     $media->user()->increment('balance', $earnings['user']);

     // Subtract user funds
     auth()->user()->decrement('wallet', Helper::amountGross($amount));

      // Check if is sent by message
      if ($this->request->isMessage) {
        // $user_id, $updates_id, $messages_id
        $this->payPerViews(auth()->user()->id, 0, $this->request->id);
        $url = url('messages', $media->user()->id);

        // Send Email Creator
        if ($media->user()->email_new_ppv == 'yes') {
          $this->notifyEmailNewPPV($media->user(), auth()->user()->username, $media->message, 'message');
        }

        // Send Notification - destination, author, type, target
        Notifications::send($media->user()->id, auth()->user()->id, '6', $this->request->id);

        // Get message to show live
        $message = Messages::whereId($this->request->id)->get();

        $data = view('includes.messages-chat', [
       			'messages' => $message,
       			'allMessages' => 0,
       			'counter' => 0
       			])->render();

        $msgId = $this->request->id;

      } else {
        // $user_id, $updates_id, $messages_id
        $this->payPerViews(auth()->user()->id, $this->request->id, 0);
        $url = url($media->user()->username, 'post').'/'.$this->request->id;

        // Send Email Creator
        if ($media->user()->email_new_ppv == 'yes') {
          $this->notifyEmailNewPPV($media->user(), auth()->user()->username, $media->description, 'post');
        }

        // Send Notification - destination, author, type, target
        Notifications::send($media->user()->id, auth()->user()->id, '7', $this->request->id);
      }

     return response()->json([
       "success" => true,
       "url" => $url,
       "data" => $data ?? false,
       "msgId" => $msgId ?? false,
       "wallet" => Helper::userWallet()
     ]);

   } // End sendWallet


  /**
	 *  Send  PayPal
	 *
	 * @return Response
	 */
  protected function sendPayPal()
  {
    // Get Payment Gateway
    $payment = PaymentGateways::whereId(1)->whereName('PayPal')->firstOrFail();

    // Check if it is a Message or Post
    $media = $this->request->isMessage ? Messages::find($this->request->id) : Updates::find($this->request->id);

    // Verify environment Sandbox or Live
    if ($payment->sandbox == 'true') {
      $action = "https://www.sandbox.paypal.com/cgi-bin/webscr";
      } else {
      $action = "https://www.paypal.com/cgi-bin/webscr";
      }

      if ($this->request->isMessage) {
        $urlSuccess = url('messages', $media->user()->id);
        $urlCancel  = url('messages', $media->user()->id);
        $isMessage  = true;
      } else {
        $urlSuccess = url($media->user()->username, 'post').'/'.$this->request->id;
        $urlCancel  = url($media->user()->username);
        $isMessage = false;
      }

      $urlPaypalIPN = url('paypal/ppv/ipn');

      return response()->json([
                  'success' => true,
                  'insertBody' => '<form id="form_pp" name="_xclick" action="'.$action.'" method="post"  style="display:none">
                  <input type="hidden" name="cmd" value="_xclick">
                  <input type="hidden" name="return" value="'.$urlSuccess.'">
                  <input type="hidden" name="cancel_return"   value="'.$urlCancel.'">
                  <input type="hidden" name="notify_url" value="'.$urlPaypalIPN.'">
                  <input type="hidden" name="currency_code" value="'.$this->settings->currency_code.'">
                  <input type="hidden" name="amount" id="amount" value="'.Helper::amountGross($this->request->amount).'">
                  <input type="hidden" name="no_shipping" value="1">
                  <input type="hidden" name="custom" value="id='.$this->request->id.'&amount='.$this->request->amount.'&sender='.auth()->user()->id.'&m='.$isMessage.'&taxes='.auth()->user()->taxesPayable().'">
                  <input type="hidden" name="item_name" value="'.__('general.unlock_content').' @'.$media->user()->username.'">
                  <input type="hidden" name="business" value="'.$payment->email.'">
                  <input type="submit">
                  </form> <script type="text/javascript">document._xclick.submit();</script>',
              ]);
      }
      protected function generatePaymentResponse($intent)
  {
    # Note that if your API version is before 2019-02-11, 'requires_action'
    # appears as 'requires_source_action'.
    if (isset($intent->status) && $intent->status == 'requires_action' &&
        $intent->next_action->type == 'use_stripe_sdk') {
      # Tell the client to handle the action
      return response()->json([
        'requires_action' => true,
        'payment_intent_client_secret' => $intent->client_secret,
      ]);
    } else if (isset($intent->status) && $intent->status == 'succeeded') {
      # The payment didn’t need any additional actions and completed!
      # Handle post-payment fulfillment

      // Insert DB
      //========== Processor Fees
      $amount = $this->request->amount;
      $payment = PaymentGateways::whereName('Stripe')->first();

      // Check if it is a Message or Post
      $media = $this->request->isMessage ? Messages::find($this->request->id) : Updates::find($this->request->id);

      // Admin and user earnings calculation
      $earnings = $this->earningsAdminUser($media->user()->custom_fee, $this->request->amount, $payment->fee, $payment->fee_cents);

      // Insert Transaction
      $this->transaction(
          'ppv_'.str_random(25),
          auth()->user()->id,
          0,
          $media->user()->id,
          $this->request->amount,
          $earnings['user'],
          $earnings['admin'],
          'Stripe',
          'ppv',
          $earnings['percentageApplied'],
          auth()->user()->taxesPayable()
        );

      // Add Earnings to User
      $media->user()->increment('balance', $earnings['user']);

       // Check if is sent by message
       if ($this->request->isMessage) {
         // $user_id, $updates_id, $messages_id
         $this->payPerViews(auth()->user()->id, 0, $this->request->id);
         $url = url('messages', $media->user()->id);

         // Send Email Creator
         if ($media->user()->email_new_ppv == 'yes') {
           $this->notifyEmailNewPPV($media->user(), auth()->user()->username, $media->message, 'message');
         }

         // Send Notification - destination, author, type, target
         Notifications::send($media->user()->id, auth()->user()->id, '6', $this->request->id);

         // Get message to show live
         $message = Messages::whereId($this->request->id)->get();

         $data = view('includes.messages-chat', [
        			'messages' => $message,
        			'allMessages' => 0,
        			'counter' => 0
        			])->render();

         $msgId = $this->request->id;

       } else {
         // $user_id, $updates_id, $messages_id
         $this->payPerViews(auth()->user()->id, $this->request->id, 0);
         $url = url($media->user()->username, 'post').'/'.$this->request->id;

         // Send Email Creator
         if ($media->user()->email_new_ppv == 'yes') {
           $this->notifyEmailNewPPV($media->user(), auth()->user()->username, $media->description, 'post');
         }

         // Send Notification - destination, author, type, target
         Notifications::send($media->user()->id, auth()->user()->id, '7', $this->request->id);
       }

      return response()->json([
        "success" => true,
        "url" => $url,
        "data" => $data ?? false,
        "msgId" => $msgId ?? false,
        "wallet" => Helper::userWallet()
      ]);
    } else {
      # Invalid status
      http_response_code(500);
      return response()->json(['error' => 'Invalid PaymentIntent status']);
    }
  }
  protected function sendStripe()
  {
        // Get Payment Gateway
        $payment = PaymentGateways::whereName('Stripe')->firstOrFail();

        // Check if it is a Message or Post
        $media = $this->request->isMessage ? Messages::find($this->request->id) : Updates::find($this->request->id);

      	$cents  = $this->settings->currency_code == 'JPY' ? Helper::amountGross($this->request->amount) : (Helper::amountGross($this->request->amount)*100);
      	$amount = (int)$cents;
      	$currency_code = $this->settings->currency_code;
      	$description = __('general.unlock_content').' @'.$media->user()->username;

        \Stripe\Stripe::setApiKey($payment->key_secret);

        $intent = null;
        try {
          if (isset($this->request->payment_method_id)) {
            # Create the PaymentIntent
            $intent = \Stripe\PaymentIntent::create([
              'payment_method' => $this->request->payment_method_id,
              'amount' => $amount,
              'currency' => $currency_code,
              "description" => $description,
              'confirmation_method' => 'manual',
              'confirm' => true
            ]);
          }
          if (isset($this->request->payment_intent_id)) {
            $intent = \Stripe\PaymentIntent::retrieve(
              $this->request->payment_intent_id
            );
            $intent->confirm();
          }
          return $this->generatePaymentResponse($intent);
        } catch (\Stripe\Exception\ApiErrorException $e) {
          # Display error on client
          return response()->json([
            'error' => $e->getMessage()
          ]);
        }
  }
}
