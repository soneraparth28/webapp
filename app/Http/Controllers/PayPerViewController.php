<?php

namespace App\Http\Controllers;

use App\Models\LiveComments;
use Mail;
use App\Helper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AdminSettings;
use Illuminate\Support\Facades\Validator;
use App\Models\PaymentGateways;
use App\Models\Transactions;
use Laravel\Cashier\Exceptions\IncompletePayment;
use App\Models\Notifications;
use App\Models\Conversations;
use App\Models\Messages;
use App\Models\Updates;
use App\Models\PayPerViews;
use App\Models\MollieContentPurchaseRelations;
use Yabacon\Paystack;
use App\Models\Countries;
use Illuminate\Support\Facades\Log;
use App\Models\TaxRates;


class PayPerViewController extends Controller
{
  use Traits\Functions;

  public function __construct(?Request $request, ?AdminSettings $settings) {
    $this->request = $request;
    $this->settings = $settings::first();
  }

  /**
	 *  Send Request
	 *
	 * @return Response
	 */
  public function send() {

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
//            ->where('user_id', '<>', auth()->user()->id)
            ->firstOrFail();

      // Verify that the user has not purchased the content
      if (PayPerViews::whereUserId(auth()->user()->id)->whereUpdatesId($this->request->id)->first()) {
        return response()->json([
          "success" => false,
          "errors" => ['error' => __('general.already_purchased_content')]
        ]);
      }
    }
    self::createRevolutWebhookURLIfNotExist();

      //self::createCostumerIfNotExist();
      self::createRevolutCustomerIfNotExist();

    // get mollie key
    //$mollie_gateway = PaymentGateways::where('name', 'Mollie')->first()->toArray();
    //$mollie_api_key = $mollie_gateway['key'];

   // $mollie_customer_id = User::where('id',auth()->id())->pluck('mollie_customer_id')->first();

   $revolut_gateway = PaymentGateways::where('name', 'Revolut')->first()->toArray();
   $revolut_api_key = $revolut_gateway['key_secret'];
   $revolut_sandbox = $revolut_gateway['sandbox'];
   $revolut_customer_id = User::where('id',auth()->id())->pluck('revolut_customer_id')->first();


    // create dummy id to track new payment after redirection
    $uniqid = uniqid();


    //include_once base_path()."/mollie-api/vendor/autoload.php";

    //\Mollie::api()->setApiKey($mollie_api_key);

    // calculate amount after adding vat amount to be charged
    $logged_user_country_id = User::where('id',auth()->id())->pluck('countries_id')->first();

    $vat_added_amount = $media->price;

      $logged_user_country_code = !empty($logged_user_country_id) ? Countries::where('id', $logged_user_country_id)->pluck('country_code')->first() : "DK";
      $tax_percentage = TaxRates::where(['name' => 'VAT', 'country' => $logged_user_country_code])->pluck('percentage')->first();

      $vat_added_amount += ($vat_added_amount*$tax_percentage)/100;
      $vat_added_amount = floatval($vat_added_amount) * 100;
      //$vat_added_amount = number_format((float)$vat_added_amount, 2, '.', '');

      $description = $media->course === "no" ? "content_purchase_".rand(1000,9999)."@".$media->id :
          "Course access purchase @" . $media->id;

    /*$payment = \Mollie::api()->payments->create([
          "amount" => [
            "currency" => "EUR",
            "value" => $vat_added_amount // You must send the correct number of decimals, thus we enforce the use of strings
          ],
          "customerId" => $mollie_customer_id,
          "description" => $description,
          "redirectUrl" => route('mollie-content-purchase-callback').'?id='.$uniqid,
          "metadata" => [
                "purchased_by" => auth()->user()->id,
                "media_id" => $media->id,
                "course" => $media->course
          ],
    ]);*/

    $curl = curl_init();

    if($revolut_sandbox == 'true')
      $url = 'https://sandbox-merchant.revolut.com/api/1.0/orders';
    else
      $url = 'https://merchant.revolut.com/api/1.0/orders';

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
        "amount": '.$vat_added_amount.',
        "currency": "EUR",
        "email": "'.auth()->user()->email.'",
        "metadata": {
          "purchased_by": '.auth()->user()->id.',
          "media_id": '.$media->id.',
          "course": "'.$media->course.'"
        }
      }',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Revolut-Api-Version: 2023-09-01',
        'Authorization: Bearer '.$revolut_api_key
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $order = json_decode($response, true);

    /*$mollie_content_purchase_relations = new MollieContentPurchaseRelations();
    $mollie_content_purchase_relations->transaction_id = $order['id'];
    $mollie_content_purchase_relations->unique_id = $uniqid;
    $mollie_content_purchase_relations->save();*/

    return response()->json([
      'success' => true,
      'public_id' => $order['public_id'],
      'url' => route('revolut-content-purchase-callback').'?order_id='.$order['public_id'],
      'name' => auth()->user()->name,
      'mode' => $revolut_sandbox == 'true' ? 'sandbox' : 'prod'
    ]);
  } // End method Send
  
  
   public function send_demo() {

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
        
        $media_id = auth()->id();
        
//       // Find Post
//       $media = Updates::whereId($this->request->id)
//             ->wherePrice($this->request->amount)
// //            ->where('user_id', '<>', auth()->user()->id)
//             ->firstOrFail();

      // Verify that the user has not purchased the content
    //   if (PayPerViews::whereUserId(auth()->user()->id)->whereUpdatesId($this->request->id)->first()) {
    //     return response()->json([
    //       "success" => false,
    //       "errors" => ['error' => __('general.already_purchased_content')]
    //     ]);
    //   }
    }
    self::createRevolutWebhookURLIfNotExist();

      //self::createCostumerIfNotExist();
      self::createRevolutCustomerIfNotExist();

    // get mollie key
    //$mollie_gateway = PaymentGateways::where('name', 'Mollie')->first()->toArray();
    //$mollie_api_key = $mollie_gateway['key'];

   // $mollie_customer_id = User::where('id',auth()->id())->pluck('mollie_customer_id')->first();

   $revolut_gateway = PaymentGateways::where('name', 'Revolut')->first()->toArray();
   $revolut_api_key = $revolut_gateway['key_secret'];
   $revolut_sandbox = $revolut_gateway['sandbox'];
   $revolut_customer_id = User::where('id',auth()->id())->pluck('revolut_customer_id')->first();


    // create dummy id to track new payment after redirection
    $uniqid = uniqid();
    $vat_added_amount = $this->request->amount;
    $vat_added_amount = $vat_added_amount*100;


    //include_once base_path()."/mollie-api/vendor/autoload.php";

    //\Mollie::api()->setApiKey($mollie_api_key);

    // calculate amount after adding vat amount to be charged
    // $logged_user_country_id = User::where('id',auth()->id())->pluck('countries_id')->first();

    // $vat_added_amount = $media->price;

    //   $logged_user_country_code = !empty($logged_user_country_id) ? Countries::where('id', $logged_user_country_id)->pluck('country_code')->first() : "DK";
    //   $tax_percentage = TaxRates::where(['name' => 'VAT', 'country' => $logged_user_country_code])->pluck('percentage')->first();

    //   $vat_added_amount += ($vat_added_amount*$tax_percentage)/100;
    //   $vat_added_amount = floatval($vat_added_amount) * 100;
    //   //$vat_added_amount = number_format((float)$vat_added_amount, 2, '.', '');

    //   $description = $media->course === "no" ? "content_purchase_".rand(1000,9999)."@".$media->id :
    //       "Course access purchase @" . $media->id;

    /*$payment = \Mollie::api()->payments->create([
          "amount" => [
            "currency" => "EUR",
            "value" => $vat_added_amount // You must send the correct number of decimals, thus we enforce the use of strings
          ],
          "customerId" => $mollie_customer_id,
          "description" => $description,
          "redirectUrl" => route('mollie-content-purchase-callback').'?id='.$uniqid,
          "metadata" => [
                "purchased_by" => auth()->user()->id,
                "media_id" => $media->id,
                "course" => $media->course
          ],
    ]);*/

    $curl = curl_init();

    if($revolut_sandbox == 'true')
      $url = 'https://sandbox-merchant.revolut.com/api/1.0/orders';
    else
      $url = 'https://merchant.revolut.com/api/1.0/orders';

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
        "amount": '.$vat_added_amount.',
        "currency": "EUR",
        "email": "'.auth()->user()->email.'",
        "metadata": {
          "purchased_by": '.auth()->user()->id.',
          "media_id": '.$media_id.',
          "course": "yes"
        }
      }',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Revolut-Api-Version: 2023-09-01',
        'Authorization: Bearer '.$revolut_api_key
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $order = json_decode($response, true);
    /*$mollie_content_purchase_relations = new MollieContentPurchaseRelations();
    $mollie_content_purchase_relations->transaction_id = $order['id'];
    $mollie_content_purchase_relations->unique_id = $uniqid;
    $mollie_content_purchase_relations->save();*/

    return response()->json([
      'success' => true,
      'public_id' => $order['public_id'],
      'url' => route('revolut-content-purchase-callback').'?order_id='.$order['public_id'],
      'name' => auth()->user()->name,
      'mode' => $revolut_sandbox == 'true' ? 'sandbox' : 'prod'
    ]);
  } // End method Send


    public static function createCostumerIfNotExist($userId = null): void {
        if(empty($userId)) $user = auth()->user();
        else $user = User::where("id", $userId)->firstOrFail();

        $mollie_gateway = PaymentGateways::where('name', 'Mollie')->first()->toArray();
        $mollie_api_key = $mollie_gateway['key'];
        $mollie_customer_id = User::where('id',$user->id)->pluck('mollie_customer_id')->first();

        // if empty then create customer on mollie
        if(empty($mollie_customer_id)){
            include_once base_path()."/mollie-api/vendor/autoload.php";

            \Mollie::api()->setApiKey($mollie_api_key);

            $customer = \Mollie::api()->customers->create([
                "name" => $user->name,
                "email" => $user->email,
            ]);


            $mollie_customer_id = $customer->id;

            User::where('id',auth()->id())->update(['mollie_customer_id'=>$mollie_customer_id]);
        }
    }
  
    public static function createRevolutCustomerIfNotExist($userId = null): void {
      if(empty($userId)) $user = auth()->user();
      else $user = User::where("id", $userId)->firstOrFail();

      $revolut_gateway = PaymentGateways::where('name', 'Revolut')->first()->toArray();
      $revolut_api_key = $revolut_gateway['key_secret'];
      $revolut_sandbox = $revolut_gateway['sandbox'];
      $revolut_customer_id = User::where('id',$user->id)->pluck('revolut_customer_id')->first();
      // if empty then create customer on mollie
      if(empty($revolut_customer_id)){

          $curl = curl_init();

          if($revolut_sandbox == 'true')
            $url = 'https://sandbox-merchant.revolut.com/api/1.0/customers';
          else
            $url = 'https://merchant.revolut.com/api/1.0/customers';

          curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
            "email": "'.$user->email.'"
          }',
            CURLOPT_HTTPHEADER => array(
              'Content-Type: application/json',
              'Accept: application/json',
              'Authorization: Bearer '.$revolut_api_key
            ),
          ));

          $response = curl_exec($curl);

          curl_close($curl);

          $customer = json_decode($response, true);
          //echo "<Pre>";print_r($customer);die;

          $revolut_customer_id = $customer['id'];

          User::where('id',auth()->id())->update(['revolut_customer_id'=>$revolut_customer_id]);
      }
  }

  public function createRevolutWebhookURLIfNotExist()
  {
      $revolut_gateway = PaymentGateways::where('name', 'Revolut')->first()->toArray();
      $revolut_api_key = $revolut_gateway['key_secret'];
      $revolut_sandbox = $revolut_gateway['sandbox'];

      if($revolut_sandbox == 'true')
        $url = 'https://sandbox-merchant.revolut.com/api/1.0/webhooks';
      else
        $url = 'https://merchant.revolut.com/api/1.0/webhooks';

      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
          'Accept: application/json',
          'Authorization: Bearer '.$revolut_api_key
        ),
      ));

      $response = json_decode(curl_exec($curl), true);

      curl_close($curl);
      if(empty($response))
      {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
          "url": "'.route('revolut-content-purchase-callback').'",
          "events": [
            "ORDER_COMPLETED",
            "ORDER_AUTHORISED"
          ]
        }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer '.$revolut_api_key
          ),
        ));

        $response = json_decode(curl_exec($curl), true);

        curl_close($curl);
        //echo "<pre>";print_r($response);
      }
     // die;
  }


  public function mollieContentPurchaseCallBack(Request $request){
    if(!empty($request->id)){

      $relation = MollieContentPurchaseRelations::where('unique_id', $request->id)->first();

      // get mollie key
      $mollie_gateway = PaymentGateways::where('name', 'Mollie')->first()->toArray();
      $mollie_api_key = $mollie_gateway['key'];

      require base_path()."/mollie-api/vendor/autoload.php";

      \Mollie::api()->setApiKey($mollie_api_key);

      $payment = \Mollie::api()->payments->get($relation->transaction_id);

      $payment_array = json_decode(json_encode($payment), true);

      if(!$payment->isPaid()){
        $url = url('/');
        return redirect()->to($url);
      }

        $metaData = $payment->metadata;
        $itemType = 'ppv';
        if(isset($metaData->type)){
            $itemType = $metaData->type === "livestream" ? "live" : ($metaData->type === "tip" ? "tip" : "ppv");
        }
        
        $txnId = is_array($payment_array) && array_key_exists("id", $payment_array) ? $payment_array["id"] : $itemType . '_'.str_random(25);

      if(isset($metaData->type) && ($metaData->type === "livestream" || $metaData->type === "tip")) {
          $metaData->txnId = $txnId;
          $metaData->gateway = "Mollie";
          self::handleTransaction(["request" => $this->request, "settings" => $this->settings, "data" => $metaData]);
          return redirect()->to($metaData->redirect_uri);
      }


      $media_id = $metaData->media_id;

      // Check if it is a Message or Post
       $media = Updates::find($media_id);



       $amount = $media->price;
       $addonCourses = $media->course === "yes" && AddonsController::userHasAddon("courses", $media->user()->id);
       $customFee = $addonCourses ? 2 : $media->user()->custom_fee;

       // Admin and user earnings calculation
       $earnings = $this->earningsAdminUser($customFee, $amount, null, null, $addonCourses);

       // vat tax calculation
        $tax_rates_id = '';

        $logged_user_country_id = auth()->user()->countries_id;

      $logged_user_country_code = !empty($logged_user_country_id) ? Countries::where('id', $logged_user_country_id)->pluck('country_code')->first() : "DK";
      $tax_rates_id = TaxRates::where(['name' => 'VAT', 'country' => $logged_user_country_code])->pluck('id')->first();

       // Insert Transaction
       $this->transaction(
           $txnId,
           auth()->user()->id,
           0,
           $media->user()->id,
           $amount,
           $earnings['user'],
           $earnings['admin'],
           'Mollie', 'ppv',
           $earnings['percentageApplied'],
           $tax_rates_id
       );

       // Add Earnings to User
       $media->user()->increment('balance', $earnings['user']);

      $this->payPerViews(auth()->user()->id, $media->id, 0);
      $url = url($media->user()->username, 'post').'/'.$media->id;

      // Send Email Creator
      if ($media->user()->email_new_ppv == 'yes') {
        $this->notifyEmailNewPPV($media->user(), auth()->user()->username, $media->description, 'post');
      }

      // Send Notification - destination, author, type, target
      Notifications::send($media->user()->id, auth()->user()->id, '7', $media->id);


      return redirect($url);


    }

  return redirect(url("/"));
  }
  public function revolutContentPurchaseCallBack(Request $request){
    // get the raw POST data
    $rawData = file_get_contents("php://input");
    // this returns null if not valid json
    $input= json_decode($rawData, true);
    Log::info($input);
    if(!empty($input['order_id']) && $input['event'] == 'ORDER_COMPLETED'){
        // get revolut key
        $revolut_gateway = PaymentGateways::where('name', 'Revolut')->first()->toArray();
        $revolut_api_key = $revolut_gateway['key_secret'];
        $revolut_sandbox = $revolut_gateway['sandbox'];

        if($revolut_sandbox == 'true'){
          $url = 'https://sandbox-merchant.revolut.com/api/1.0/orders/'.$input['order_id'];
        }
        else{
        $url = 'https://merchant.revolut.com/api/1.0/orders/'.$input['order_id'];
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Revolut-Api-Version: 2023-09-01',
            'Authorization: Bearer '.$revolut_api_key
          ),
        ));
        $payment = json_decode(curl_exec($curl),true);
        Log::info($payment);

        curl_close($curl);
        if($payment['payments'][0]['state']!='CAPTURED'){
          $url = url('/');
          return redirect()->to($url);
        }

        $metaData = $payment['metadata'];
        //$itemType = 'ppv';
        if(isset($metaData['type'])){
            $itemType = $metaData['type'] === "livestream" ? "live" : ($metaData['type'] === "tip" ? "tip" : "ppv");
        }
        $txnId = is_array($payment) ? $payment['payments'][0]['id'] : $itemType . '_'.str_random(25);

        if(isset($metaData['type']) && ($metaData['type'] === "livestream" || $metaData['type'] === "tip")) {

            $metaData->txnId = $txnId;
            $metaData->gateway = "Revolut";
            self::handleTransaction(["request" => $this->request, "settings" => $this->settings, "data" => $metaData]);
            return redirect()->to($metaData['redirect_uri']);
            $media_id = $metaData['media_id'];

            // Check if it is a Message or Post
            $media = Updates::find($media_id);
            $user = User::where('revolut_customer_id',$payment['customer_id'])->first();
            $amount = $media->price;
            $addonCourses = $media->course === "yes" && AddonsController::userHasAddon("courses", $media->user()->id);

            $customFee = $addonCourses ? 2 : $media->user()->custom_fee;

            // Admin and user earnings calculation
            $earnings = $this->earningsAdminUser($customFee, $amount, null, null, $addonCourses);

            // vat tax calculation

            $tax_rates_id = '';
            $logged_user_country_id = $user->countries_id;
            $logged_user_country_code = !empty($logged_user_country_id) ? Countries::where('id', $logged_user_country_id)->pluck('country_code')->first() : "DK";
            $tax_rates_id = TaxRates::where(['name' => 'VAT', 'country' => $logged_user_country_code])->pluck('id')->first();


            // Insert Transaction
             $this->transaction(
                 $txnId,
                 $user->id,
                 0,
                 $media->user()->id,
                 $amount,
                 $earnings['user'],
                 $earnings['admin'],
                 'Revolut', 'ppv',
                 $earnings['percentageApplied'],
                 $tax_rates_id
             );

            // Add Earnings to User
            $media->user()->increment('balance', $earnings['user']);
            $this->payPerViews($user->id, $media->id, 0);
            $url = url($media->user()->username, 'post').'/'.$media->id;

            // Send Email Creator
            if ($media->user()->email_new_ppv == 'yes') {
              $this->notifyEmailNewPPV($media->user(), $user->username, $media->description, 'post');
            }

            // Send Notification - destination, author, type, target
            Notifications::send($media->user()->id, $user->id, '7', $media->id);


            return redirect($url);
        }
        if(isset($metaData['type']) && $metaData['type'] === "subscription"){

          $interval = $metaData['interval'];
          $user_id = $metaData['purchased_by'];
          app('App\Http\Controllers\SubscriptionsController')->startRevolutSubscriptionLocally($txnId, $interval, $user_id);

        }
    }

    //return redirect(url("/"));
    return response()->json(['message' => 'Webhook processed successfully'], 200);
  }

  public function revolutContentPurchaseCallBack_old(){

    // get the raw POST data
    $rawData = file_get_contents("php://input");

    // this returns null if not valid json
    $input= json_decode($rawData, true);

    if(!empty($input['order_id']) && $input['event'] == 'ORDER_COMPLETED'){

      // get revolut key
      $revolut_gateway = PaymentGateways::where('name', 'Revolut')->first()->toArray();
      $revolut_api_key = $revolut_gateway['key_secret'];

      $revolut_sandbox = $revolut_gateway['sandbox'];

      if($revolut_sandbox == 'true')
        $url = 'https://sandbox-merchant.revolut.com/api/1.0/orders/'.$input['order_id'];
      else
        $url = 'https://merchant.revolut.com/api/1.0/orders/'.$input['order_id'];

     // $payment = \Mollie::api()->payments->get($relation->transaction_id);

     /// $payment_array = json_decode(json_encode($payment), true);

      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
          'Accept: application/json',
          'Revolut-Api-Version: 2023-09-01',
          'Authorization: Bearer '.$revolut_api_key
        ),
      ));

      $payment = json_decode(curl_exec($curl),true);

      curl_close($curl);
      //echo "<pre>";print_r($payment);die;

      if($payment['payments'][0]['state']!='CAPTURED'){
        $url = url('/');
        return redirect()->to($url);
      }

        $metaData = $payment['metadata'];
        $itemType = 'ppv';
        if(isset($metaData['type'])){
            $itemType = $metaData['type'] === "livestream" ? "live" : ($metaData['type'] === "tip" ? "tip" : "ppv");
        }
        
        $txnId = is_array($payment) ? $payment['payments'][0]['id'] : $itemType . '_'.str_random(25);

      if(isset($metaData['type']) && ($metaData['type'] === "livestream" || $metaData['type'] === "tip")) {
          $metaData->txnId = $txnId;
          $metaData->gateway = "Revolut";
          self::handleTransaction(["request" => $this->request, "settings" => $this->settings, "data" => $metaData]);
          return redirect()->to($metaData['redirect_uri']);
      }


      $media_id = $metaData['media_id'];

      // Check if it is a Message or Post
       $media = Updates::find($media_id);

       $user = User::where('revolut_customer_id',$payment['customer_id'])->first();


       $amount = $media->price;
       $addonCourses = $media->course === "yes" && AddonsController::userHasAddon("courses", $media->user()->id);
       $customFee = $addonCourses ? 2 : $media->user()->custom_fee;

       // Admin and user earnings calculation
       $earnings = $this->earningsAdminUser($customFee, $amount, null, null, $addonCourses);

       // vat tax calculation
        $tax_rates_id = '';

        $logged_user_country_id = $user->countries_id;

      $logged_user_country_code = !empty($logged_user_country_id) ? Countries::where('id', $logged_user_country_id)->pluck('country_code')->first() : "DK";
      $tax_rates_id = TaxRates::where(['name' => 'VAT', 'country' => $logged_user_country_code])->pluck('id')->first();

       // Insert Transaction
       $this->transaction(
           $txnId,
           $user->id,
           0,
           $media->user()->id,
           $amount,
           $earnings['user'],
           $earnings['admin'],
           'Revolut', 'ppv',
           $earnings['percentageApplied'],
           $tax_rates_id
       );

       // Add Earnings to User
       $media->user()->increment('balance', $earnings['user']);

      $this->payPerViews($user->id, $media->id, 0);
      $url = url($media->user()->username, 'post').'/'.$media->id;

      // Send Email Creator
      if ($media->user()->email_new_ppv == 'yes') {
        $this->notifyEmailNewPPV($media->user(), $user->username, $media->description, 'post');
      }

      // Send Notification - destination, author, type, target
      Notifications::send($media->user()->id, $user->id, '7', $media->id);


      return redirect($url);


    }

    return redirect(url("/"));
  }

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
      } // sendPayPal

      /**
       * PayPal IPN
       *
       * @return void
       */
      public function paypalPPVIpn()
      {
        $ipn = new PaypalIPNListener();

  			$ipn->use_curl = false;

        $payment = PaymentGateways::find(1);

  			if ($payment->sandbox == 'true') {
  				// SandBox
  				$ipn->use_sandbox = true;
  				} else {
  				// Real environment
  				$ipn->use_sandbox = false;
  				}

  	    $verified = $ipn->processIpn();

  			$custom  = $_POST['custom'];
  			parse_str($custom, $data);

  			$payment_status = $_POST['payment_status'];
  			$txn_id         = $_POST['txn_id'];

  	    if ($verified) {
  				if ($payment_status == 'Completed') {

            // Check if it is a Message or Post
            $media = $data['m'] ? Messages::find($data['id']) : Updates::find($data['id']);

            // Admin and user earnings calculation
            $earnings = $this->earningsAdminUser($media->user()->custom_fee, $data['amount'], $payment->fee, $payment->fee_cents);

  	          // Check outh POST variable and insert in DB
  						$verifiedTxnId = Transactions::where('txn_id', $txn_id)->first();

  			if (! isset($verifiedTxnId)) {

          // Insert Transaction
          $this->transaction(
              $txn_id,
              $data['sender'],
              0,
              $media->user()->id,
              $data['amount'],
              $earnings['user'],
              $earnings['admin'],
              'PayPal',
              'ppv',
              $earnings['percentageApplied'],
              $data['taxes']
            );

          // Add Earnings to User
          $media->user()->increment('balance', $earnings['user']);

          // User Sender
          $buyer = User::find($data['sender']);

          //============== Check if is sent by message
          if ($data['m']) {
            // $user_id, $updates_id, $messages_id
            $this->payPerViews($data['sender'], 0, $data['id']);

            // Send Email Creator
            if ($media->user()->email_new_ppv == 'yes') {
              $this->notifyEmailNewPPV($media->user(), $buyer->username, $media->message, 'message');
            }

            // Send Notification - destination, author, type, target
            Notifications::send($media->user()->id, $data['sender'], '6', $data['id']);
          } else {
            // $user_id, $updates_id, $messages_id
            $this->payPerViews($data['sender'], $data['id'], 0);

            // Send Email Creator
            if ($media->user()->email_new_ppv == 'yes') {
              $this->notifyEmailNewPPV($media->user(), $buyer->username, $media->description, 'post');
            }

            // Send Notification - destination, author, type, target
            Notifications::send($media->user()->id, $data['sender'], '7', $data['id']);
          }

  			}// <--- Verified Txn ID

  	      } // <-- Payment status
  	    } else {
  	    	//Some thing went wrong in the payment !
  	    }

      }//<----- End Method paypalIpn()

  /**
	 *  Send  Stripe
	 *
	 * @return Response
	 */
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
  } // End Method sendStripe

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
  }// End generatePaymentResponse

  public function sendPaystack()
  {

    $payment = PaymentGateways::whereName('Paystack')->whereEnabled(1)->firstOrFail();
    $paystack = new Paystack($payment->key_secret);
    $amount = Helper::amountGross($this->request->amount);

    if (isset($this->request->trxref)) {
      try {
        $tranx = $paystack->transaction->verify([
          'reference' => $this->request->trxref,
        ]);
      } catch (\Exception $e) {
        return response()->json([
          "success" => false,
          'errors' => ['error' => $e->getMessage()]
        ]);
      }

      if ('success' === $tranx->data->status) {
        // Verify transaction
        $verifyTxnId = Transactions::where('txn_id', $tranx->data->reference)->first();

      if ( ! isset($verifyTxnId)) {

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
            'Paystack',
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

        } // end verifyTxnId

        return response()->json([
          "success" => true,
          'instantPayment' => true,
          "url" => $url,
          "data" => $data ?? false,
          "msgId" => $msgId ?? false,
          "wallet" => Helper::userWallet()
        ]);
      } else {
        return response()->json([
            'success' => false,
            'errors' => ['error' => $tranx->data->gateway_response],
        ]);
      }


    } else {
      return response()->json([
          'success' => true,
          'insertBody' => "<script type='text/javascript'>var handler = PaystackPop.setup({
            key: '".$payment->key."',
            email: '".auth()->user()->email."',
            amount: ".($amount*100).",
            currency: '".$this->settings->currency_code."',
            ref: '".Helper::genTranxRef()."',
            callback: function(response) {
              var input = $('<input type=hidden name=trxref />').val(response.reference);
              $('#formSendPPV').append(input);
              $('#ppvBtn').trigger('click');
            },
            onClose: function() {
                alert('Window closed');
            }
          });
          handler.openIframe();</script>"
      ]);
    }
  }// end method

  public function sendCCbill()
	{
		// Get Payment Gateway
		$payment = PaymentGateways::whereName('CCBill')->firstOrFail();

		$currencyCodes = [
			'AUD' => 036,
			'CAD' => 124,
			'JPY' => 392,
			'GBP' => 826,
			'USD' => 840,
			'EUR' => 978
		];

    $formPrice = number_format(Helper::amountGross($this->request->amount), 2);

		$formInitialPeriod = 2;
		$currencyCode = array_key_exists($this->settings->currency_code, $currencyCodes) ? $currencyCodes[$this->settings->currency_code] : 840;

		// Hash
		$hash = md5($formPrice . $formInitialPeriod . $currencyCode . $payment->ccbill_salt);

		$input['clientAccnum']  = $payment->ccbill_accnum;
		$input['clientSubacc']  = $payment->ccbill_subacc;
		$input['currencyCode']  = $currencyCode;
		$input['formDigest']    = $hash;
		$input['initialPrice']  = $formPrice;
		$input['initialPeriod'] = $formInitialPeriod;
		$input['type']          = 'ppv';
		$input['isMessage']     = $this->request->isMessage ?? null;
		$input['media']         = $this->request->id;
		$input['user']          = auth()->user()->id;
    $input['priceOriginal'] = $this->request->amount;
    $input['taxes']         = auth()->user()->taxesPayable();

		// Base url
		$baseURL = 'https://api.ccbill.com/wap-frontflex/flexforms/' . $payment->ccbill_flexid;

		// Build redirect url
		$inputs = http_build_query($input);
		$redirectUrl = $baseURL . '?' . $inputs;

		return response()->json([
								'success' => true,
								'url' => $redirectUrl,
						]);

	}// End Method





    public static function handleTransaction($details): mixed {
        $ppvController = new static($details["request"], $details["settings"]);
        $details = json_decode(json_encode($details["data"]), true);

        switch (strtolower($details["gateway"])) {
            default: return false;

            case "wallet":
                $totalPriceVat = $details["is_incl_vat"] ? $details["amount"] : Helper::setTax($details["amount"], $details["taxes"]);
                $totalPriceNoVat = $details["amount_ex_vat"];

                $seller = User::where("id", $details["seller_id"])->firstOrFail();
                $buyer = User::where("id", $details["buyer_id"])->firstOrFail();

                if ($buyer->wallet < $totalPriceVat) {
                    return response()->json([
                        "success" => false,
                        "errors" => ['error' => __('general.not_enough_funds')]
                    ]);
                }
                $earnings = $ppvController->earningsAdminUser($seller->custom_fee, $totalPriceNoVat, null, null);
                $buyer->decrement("wallet", $totalPriceVat);
                $seller->increment("balance", $earnings['user']);

            case "card": case "mollie": case "revolut":
                $seller = User::where("id", $details["seller_id"])->firstOrFail();
                $buyer = User::where("id", $details["buyer_id"])->firstOrFail();
                $details["taxes"] = $buyer->isTaxable();

                $totalPriceNoVat = $details["amount"];
                $earnings = $ppvController->earningsAdminUser($seller->custom_fee, $totalPriceNoVat, null, null);
                $seller->increment('balance', $earnings['user']);

                if ($details["type"] === "tip") {
                    if($details["is_livestream"]) LiveStreamingsController::addTipCommentToLiveStream($details["livestream_id"], $buyer->id, $details["amount"]);
                    else {
                        if($seller->email_new_tip == 'yes') $ppvController->notifyEmailNewTip($seller, $buyer->username, $details["amount"]);
                        Notifications::send($seller->id, $buyer->id, '5', $buyer->id);
                    }
                }
                elseif($details["type"] === "livestream") LiveStreamingsController::addAudienceUserToLiveStream($details["livestream_id"], $buyer->id);
        }

        if((Transactions::where("txn_id", $details["txnId"])->get())->count() !== 0) return false;

        //== Insert Transaction
        $ppvController->transaction(
            $details["txnId"],
            $buyer->id,
            $details["subscription_id"],
            $seller->id,
            $totalPriceNoVat,
            $earnings['user'],
            $earnings['admin'],
            $details["gateway"],
            $details["type"],
            $earnings['percentageApplied'],
            $buyer->taxesPayable()
        );
        return true;
    }



    public static function makePayment($details) {
        $details = $details["data"];
        $buyer = User::where("id", $details["buyer_id"])->firstOrFail();
        $totalPriceVat = $details["is_incl_vat"] ? $details["amount"] : Helper::setTax($details["amount"], $details["taxes"]);

       /* include_once base_path()."/mollie-api/vendor/autoload.php";
        self::createCostumerIfNotExist($buyer->id);

        // get mollie key
        $mollie_gateway = PaymentGateways::where('name', 'Mollie')->first()->toArray();
        $mollie_api_key = $mollie_gateway['key'];
        $mollie_customer_id = User::where('id',$buyer->id)->pluck('mollie_customer_id')->first();

        $uniqid = uniqid();
        \Mollie::api()->setApiKey($mollie_api_key);

        $payment = \Mollie::api()->payments->create([
            "amount" => [
                "currency" => "EUR",
                "value" => number_format($totalPriceVat, 2, '.', '')
            ],
            "customerId" => $mollie_customer_id,
            "description" => $details["description"],
            "redirectUrl" => route('mollie-content-purchase-callback').'?id='.$uniqid,
            "metadata" => $details["metadata"],
        ]);


        $mollie_content_purchase_relations = new MollieContentPurchaseRelations();
        $mollie_content_purchase_relations->transaction_id = $payment->id;
        $mollie_content_purchase_relations->unique_id = $uniqid;
        $mollie_content_purchase_relations->save();

        return response()->json([
            'success' => true,
            'url' => $payment->getCheckoutUrl()
        ]);*/

        self::createRevolutCustomerIfNotExist();

      $revolut_gateway = PaymentGateways::where('name', 'Revolut')->first()->toArray();
      $revolut_api_key = $revolut_gateway['key_secret'];
      $revolut_sandbox = $revolut_gateway['sandbox'];
      $revolut_customer_id = User::where('id',auth()->id())->pluck('revolut_customer_id')->first();


        // create dummy id to track new payment after redirection
        $uniqid = uniqid();

        $totalPriceVat = floatval($totalPriceVat) * 100;

      

        $curl = curl_init();

        if($revolut_sandbox == 'true')
          $url = 'https://sandbox-merchant.revolut.com/api/1.0/orders';
        else
          $url = 'https://merchant.revolut.com/api/1.0/orders';

        $request_post = '{
          "amount": '.$totalPriceVat.',
          "currency": "EUR",
          "description": "'.$details["description"].'",
          "email": "'.auth()->user()->email.'",
          "metadata": '.json_encode($details["metadata"]).'
        }';

        //echo "<Pre>";print_r($request_post);die;

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $request_post,
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Revolut-Api-Version: 2023-09-01',
            'Authorization: Bearer '.$revolut_api_key
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $order = json_decode($response, true);

//echo "<Pre>";print_r($order);die;
        return response()->json([
          'success' => true,
          'token' => $order['public_id'],
          'url' => route('revolut-content-purchase-callback').'?order_id='.$order['public_id'],
          'name' => auth()->user()->name,
          'mode' => $revolut_sandbox == 'true' ? 'sandbox' : 'prod'
        ]);
    }











}
