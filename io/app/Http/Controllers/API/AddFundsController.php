<?php /** @noinspection ALL */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Functions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Helper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\AdminSettings;
use App\Models\Notifications;
use Fahim\PaypalIPN\PaypalIPNListener;
use Illuminate\Support\Facades\Validator;
use App\Models\PaymentGateways;
use App\Models\Transactions;
use Laravel\Cashier\Exceptions\IncompletePayment;
use KingFlamez\Rave\Facades\Rave as Flutterwave;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AdminDepositPending;
use App\Models\Conversations;
use App\Models\Messages;
use App\Models\Deposits;
use Yabacon\Paystack;
use Mollie\Api\MollieApiClient;
use Razorpay\Api\Api;

class AddFundsController extends Controller
{
    use Functions;
    public function __construct(Request $request, AdminSettings $settings) {
        $this->request = $request;
        $this->settings = $settings::first();
    }

    public function wallet(): JsonResponse
    {
        if ($this->settings->disable_wallet == 'on') {
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'Error Occured.',
            ];
            abort(response()->json($response , 404));
        }
        $data = Deposits::whereUserId(auth()->user()->id)->orderBy('id', 'desc')->paginate(20);

        $equivalent_money = Helper::equivalentMoney($this->settings->wallet_format);
        $response = [
            'success' => true,
            'data' => [
                'deposites' => $data,
                'equivalent_money' => $equivalent_money,
            ],
            'message' => 'My Wallet Data.',
        ];
        return response()->json($response , 200);
//        return view('users.wallet', ['data' => $data, 'equivalent_money' => $equivalent_money]);
    }
    public function addfunds()
    {
        if ($this->settings->disable_wallet == 'on') {
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'Error Occured.',
            ];
            abort(response()->json($response , 404));
        }
        $data = Deposits::whereUserId(auth()->user()->id)->orderBy('id', 'desc')->paginate(20);

        $equivalent_money = Helper::equivalentMoney($this->settings->wallet_format);
        // $response = [
        //     'success' => true,
        //     'data' => [
        //         'deposites' => $data,
        //         'equivalent_money' => $equivalent_money,
        //     ],
        //     'message' => 'My Wallet Data.',
        // ];
        // return response()->json($response , 200);
        return view('users.walletapi', ['data' => $data, 'equivalent_money' => $equivalent_money]);
    }
    public function paymentProcess(): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => [
                'payment_process' => true,
            ],
            'message' => 'Payment Proccess Data.',
        ];
        return response()->json($response , 200);
//        return redirect('my/wallet')->with(['payment_process' => true]);
    }
    public function mercadoPagoProcess(Request $request): JsonResponse
    {
        try {
            // if payment not approved
            if ($request->status != 'approved') {
                $response = [
                    'success' => false,
                    'data' => null,
                    'message' => 'Payment Failed.',
                ];
                return response()->json($response , 400);
//                throw new \Exception('Payment failed');
            }

            if ($request->has('external_reference')) {
                $external = $request->external_reference;
                // parse_str($external, $data);
            } else {
                $response = [
                    'success' => false,
                    'data' => null,
                    'message' => 'An error has occurred missing External Reference.',
                ];
                return response()->json($response , 400);
//                throw new \Exception('An error has occurred missing External Reference');
            }

            // Validate Amount
            if ($request->amountOriginal < $this->settings->min_deposits_amount) {
                $response = [
                    'success' => false,
                    'data' => null,
                    'message' => 'An error has occurred amount not invalid.',
                ];
                return response()->json($response , 400);
//                throw new \Exception('An error has occurred amount not invalid');
            }

            // Insert Deposit
            $this->deposit(
                $request->userId,
                'mp_'.str_random(25),
                $request->amountOriginal,
                'Mercadopago',
                $request->userTaxes ?? null
            );

            // Add Funds to User
            User::find($request->userId)->increment('wallet', $request->amountOriginal);
            $response = [
                'success' => true,
                'data' => [
                    'redirect_url' => 'my/wallet',
                ],
                'message' => 'Funds Added to user.',
            ];
            return response()->json($response , 200);
//            return redirect('my/wallet');

        } catch(\Exception $e) {
            $response = [
                'success' => false,
                'data' => [
                    'redirect_url' => 'my/wallet',
                ],
                'message' => $e->getMessage(),
            ];
            return response()->json($response , 400);
//            return redirect('my/wallet')->withErrorMessage($e->getMessage());
        }
    }
    public function flutterwaveCallback(): JsonResponse
    {
        $status = request()->status;

        //if payment is successful
        if ($status ==  'successful') {

            $transactionID = Flutterwave::getTransactionIDFromCallback();
            $data = Flutterwave::verifyTransaction($transactionID);

            $verifyTxnId = Deposits::where('txn_id', $data['data']['tx_ref'])->first();

            if ($data['data']['status'] == "successful"
                && $data['data']['amount'] >= $data['data']['meta']['amountFinal']
                && $data['data']['currency'] == $this->settings->currency_code
                && ! $verifyTxnId
            ) {
                // Insert Deposit
                $this->deposit(
                    $data['data']['meta']['user'],
                    $data['data']['tx_ref'],
                    $data['data']['meta']['amountFinal'],
                    'Flutterwave',
                    $data['data']['meta']['taxes'] ?? null
                );

                // Add Funds to User
                User::find($data['data']['meta']['user'])->increment('wallet', $data['data']['meta']['amountFinal']);
            }

        } // end payment is successful
        $response = [
            'success' => true,
            'data' => [
                'redirect_url' => 'my/wallet',
            ],
            'message' => 'Payment Successfull.'
        ];
        return response()->json($response , 200);
    }
    public function coinPaymentsIPN(Request $request)
    {
        // Get Payment Gateway
        $payment = PaymentGateways::whereName('Coinpayments')->firstOrFail();

        $merchantId = $payment->key;
        $ipnSecret = $payment->key_secret;

        $currency = $this->settings->currency_code;

        // Find user
        $user = User::findOrFail($request->user);

        // Validations...
        if (! isset($_POST['ipn_mode']) || $_POST['ipn_mode'] != 'hmac') {
            exit;
        }

        if (! isset($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC'])) {
            exit;
        }

        $getRequest = file_get_contents('php://input');

        if ($getRequest === FALSE || empty($getRequest)) {
            exit;
        }

        if (! isset($_POST['merchant']) || $_POST['merchant'] != trim($merchantId)) {
            exit;
        }

        $hmac = hash_hmac("sha512", $getRequest, trim($ipnSecret));
        if (!hash_equals($hmac, $_SERVER['HTTP_HMAC'])) {
            exit;
        }

        // Variables
        $ipn_type = $_POST['ipn_type'];
        $txn_id = $_POST['txn_id'];
        $item_name = $_POST['item_name'];
        $currency1 = $_POST['currency1'];
        $currency2 = $_POST['currency2'];
        $status = intval($_POST['status']);

        // Check Button payment
        if ($ipn_type != 'button') {
            exit;
        }

        // Check currency
        if ($currency1 != $currency) {
            exit;
        }

        if ($status >= 100 || $status == 2) {
            try {

                // Insert Deposit
                $this->deposit($user->id, $txn_id, $request->amountOriginal, 'Coinpayments', $request->taxes);

                // Add Funds to User
                $user->increment('wallet', $request->amountOriginal);

            } catch (\Exception $e) {
                Log::info($e->getMessage());
            }
        } // status >= 100

    }
    public function send() {

    // Validate Payment Gateway
    Validator::extend('check_payment_gateway', function($attribute, $value, $parameters) {
      return PaymentGateways::whereName($value)->first();
    });

    // Currency Position
    if ($this->settings->currency_position == 'right') {
      $currencyPosition =  2;
    } else {
      $currencyPosition =  null;
    }

    $messages = array (
      'amount.min' => trans('general.amount_minimum'.$currencyPosition, ['symbol' => $this->settings->currency_symbol, 'code' => $this->settings->currency_code]),
      'amount.max' => trans('general.amount_maximum'.$currencyPosition, ['symbol' => $this->settings->currency_symbol, 'code' => $this->settings->currency_code]),
      'payment_gateway.check_payment_gateway' => trans('general.payments_error'),
      'image.required_if' => trans('general.please_select_image'),
  );

  //<---- Validation
  $validator = Validator::make($this->request->all(), [
      'amount' => 'required|integer|min:'.$this->settings->min_deposits_amount.'|max:'.$this->settings->max_deposits_amount,
      'payment_gateway' => 'required|check_payment_gateway',
      'image' => 'required_if:payment_gateway,==,Bank|mimes:jpg,gif,png,jpe,jpeg|max:'.$this->settings->file_size_allowed_verify_account.'',
      ], $messages);

    if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ]);
        }

    switch ($this->request->payment_gateway) {
      case 'PayPal':
        return $this->sendPayPal();
        break;

      case 'Stripe':
        return $this->sendStripe();
        break;

      case 'Bank':
        return $this->sendBankTransfer();
        break;

      case 'CCBill':
        return $this->ccbillForm(
            $this->request->amount,
            auth()->user()->id,
            'wallet'
          );
        break;

      case 'Paystack':
      return $this->sendPaystack();
        break;

      case 'Coinpayments':
      return $this->sendCoinpayments();
        break;

      case 'Mercadopago':
      return $this->sendMercadopago();
        break;

      case 'Flutterwave':
      return $this->sendFlutterwave();
        break;

      case 'Mollie':
      $data = $this->sendMollie();
      if($data['success'])
      {
          return redirect($data['url']);
      }
      else
      {
          return back()->with("error" , "Error.");
      }
        break;

      case 'Razorpay':
      return $this->sendRazorpay();
        break;
    }
    return response()->json([
      'success' => true,
      'insertBody' => '<i></i>'
    ]);

  }
  public function sendMollie()
    {
      // Get Payment Gateway
      $paymentGateway = PaymentGateways::whereName('Mollie')->firstOrFail();

      $mollie = new MollieApiClient();
      $mollie->setApiKey($paymentGateway->key);

      $fee   = $paymentGateway->fee;
    	$cents =  $paymentGateway->fee_cents;

      $taxes = $this->settings->tax_on_wallet ? ($this->request->amount * auth()->user()->isTaxable()->sum('percentage') / 100) : 0;

    	$amount = number_format($this->request->amount + ($this->request->amount * $fee / 100) + $cents + $taxes, 2, '.', '');

      $payment = $mollie->payments->create([
      'amount' => [
          'currency' => $this->settings->currency_code,
          'value' => $amount, // You must send the correct number of decimals, thus we enforce the use of strings
      ],
      'description' => __('general.add_funds').' @'.auth()->user()->username,
      'webhookUrl' => url('api/webhook/mollie'),
      'redirectUrl' => url('api/add/funds'),
      "metadata"    => array(
            'user_id' => auth()->user()->id,
            'amount' =>  $this->request->amount,
            'taxes' => $this->settings->tax_on_wallet ? auth()->user()->taxesPayable() : null
        )
      ]);
      

      $payment = $mollie->payments->get($payment->id);
    //   return response()->json([
    // 			'success' => true,
    //       'url' => $payment->getCheckoutUrl(), // redirect customer to Mollie checkout page
    // 	]);
    $data = [
        'success' => true,
        "url" => $payment->getCheckoutUrl()
        ];
    return $data;

    }
    public function webhookMollie()
    {
    $paymentGateway = PaymentGateways::whereName('Mollie')->firstOrFail();

    $mollie = new MollieApiClient();
    $mollie->setApiKey($paymentGateway->key);

    if (! $this->request->has('id')) {
            return;
        }

        $payment = $mollie->payments->get($this->request->id);

        if ($payment->isPaid()) {

            // Verify Transaction ID and insert in DB
            $verifiedTxnId = Deposits::where('txn_id', $payment->id)->first();

            if (! isset($verifiedTxnId)) {

              // Insert Deposit
              $this->deposit(
                $payment->metadata->user_id,
                $payment->id,
                $payment->metadata->amount,
                'Mollie',
                $payment->metadata->taxes ?? null
              );

      				//Add Funds to User
      				User::find($payment->metadata->user_id)->increment('wallet', $payment->metadata->amount);

            }// Verify Transaction ID

        }// End isPaid()

    }
}
