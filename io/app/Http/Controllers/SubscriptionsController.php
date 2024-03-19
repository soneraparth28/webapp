<?php

namespace App\Http\Controllers;

use App\Models\Addons;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Plans;
use App\Models\Subscriptions;
use App\Models\AdminSettings;
use App\Models\Withdrawals;
use App\Models\Notifications;
use App\Models\Transactions;
use Fahim\PaypalIPN\PaypalIPNListener;
use App\Helper;
use Illuminate\Validation\Rule;
use Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\PaymentGateways;
use App\Models\MollieSubscriptionDetails;
use App\Models\MollieSubscriptionWebhooks;
use App\Models\MollieSubscriptionRelations;
use App\Models\Countries;
use App\Models\TaxRates;
use Image;


class SubscriptionsController extends Controller
{
  use Traits\Functions;

  public const STORAGE_DIRECTORY = __DIR__ . "/../../../storage/logs/";

  public function __construct(Request $request, AdminSettings $settings) {
    $this->request = $request;
    $this->settings = $settings::first();
  }

  /**
	 * Buy subscription
	 *
	 * @return Response
	 */
  public function buy()
  {

    // echo $current_date = date('Y-m-d');
    // echo $start_date_for_mollie = date('Y-m-d', strtotime("+2 month", strtotime($current_date)));

    //   exit();

    $creator_id = $this->request->id;
    $interval = $this->request->interval;
   

    //exit();

    // Find the User
    $user = User::whereVerifiedId('yes')
        ->whereId($this->request->id)
        ->where('id', '<>', auth()->id())
        ->firstOrFail();

        // Check if Plan exists
        $plan = $user->plans()
        ->whereInterval($this->request->interval)
        ->firstOrFail();

        // echo "<pre>";
        // print_r($plan);
        // exit();

        if (! $plan->status) {
          return response()->json([
              'success' => false,
              'errors' => ['error' => trans('general.subscription_not_available')],
          ]);
        }

    // Check if subscription exists
    $checkSubscription = auth()->user()->mySubscriptions()
      ->whereStripePrice($plan->name)
        ->where('ends_at', '>=', now())->first();

    if ($checkSubscription) {
      return response()->json([
          'success' => false,
          'errors' => ['error' => trans('general.subscription_exists')],
      ]);
    }

  //<---- Validation
  $validator = Validator::make($this->request->all(), [
      //'payment_gateway' => 'required',
      //'payment_gateway' => 'required',
      'agree_terms' => 'required',
      ]);

    if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ]);
        }

        // ------------------------------------------------------------------
        // ------------------------------------------------------------------
        // ----------  New code for mollie subscription start ---------------
        // ------------------------------------------------------------------
        // ------------------------------------------------------------------

       /* $redirect_url = route('mollie-subscription-first-payment').'?'.$url_query;
        self::createCostumerIfNotExist();

        return response()->json([
            'success' => true,
            'url' => $redirect_url,
        ]);*/

        //return redirect()->to($url);


      
    
        
        $plan = Plans::where(['user_id'=>$creator_id, 'interval'=>$interval])->first();
       
    

      //self::createCostumerIfNotExist();
      self::createRevolutCustomerIfNotExist();



    $revolut_gateway = PaymentGateways::where('name', 'Revolut')->first()->toArray();
    $revolut_api_key = $revolut_gateway['key_secret'];
    $revolut_sandbox = $revolut_gateway['sandbox'];
    $revolut_customer_id = User::where('id',auth()->id())->pluck('revolut_customer_id')->first();

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
        "amount": '.self::priceWithTax($plan->price).',
        "currency": "EUR",
        "email": "'.auth()->user()->email.'"
      }',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer '.$revolut_api_key
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $order = json_decode($response, true);

     // create dummy id to track new payment after redirection
     $uniqid = uniqid();

     // create new query parameters
     $url_query = 'cr='.$creator_id.'&interval='.$interval.'&uniqid='.$uniqid;
     
     $mollie_subscription_relations = new MollieSubscriptionRelations();
     $mollie_subscription_relations->transaction_id = $order['id'];
     $mollie_subscription_relations->unique_id = $uniqid;
     $mollie_subscription_relations->save();


    return response()->json([
      'success' => true,
      'public_id' => $order['public_id'],
      'name' => auth()->user()->name,
      'mode' => $revolut_sandbox == 'true' ? 'sandbox' : 'prod',
      'callback_url' => route('revolut-subscription-first-payment-callback').'?'.$url_query
    ]);




        // // Wallet
        // if ($this->request->payment_gateway == 'wallet') {
        //   return $this->sendWallet();
        // }

        // // Get name of Payment Gateway
        // $payment = PaymentGateways::findOrFail($this->request->payment_gateway);

        // // Send data to the payment processor
        // return redirect()->route(str_slug($payment->name), $this->request->except(['_token']));

  }// End Method Send


    public static function createCostumerIfNotExist(): void {
        $mollie_gateway = PaymentGateways::where('name', 'Mollie')->first()->toArray();
        $mollie_api_key = $mollie_gateway['key'];

        $mollie_customer_id = User::where('id',auth()->id())->pluck('mollie_customer_id')->first();

        // if empty then create customer on mollie
        if(empty($mollie_customer_id)){
            require_once base_path()."/mollie-api/vendor/autoload.php";

            \Mollie::api()->setApiKey($mollie_api_key);

            $customer = \Mollie::api()->customers->create([
                "name" => auth()->user()->name,
                "email" => auth()->user()->email,
            ]);


            $mollie_customer_id = $customer->id;

            User::where('id',Auth::user()->id)->update(['mollie_customer_id'=>$mollie_customer_id]);
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

          $revolut_customer_id = $customer['id'];

          User::where('id',auth()->id())->update(['revolut_customer_id'=>$revolut_customer_id]);
      }
  }

 

    public static function priceWithTax(string|int|float $price): string {
        $subscribe_by_user_country_id = User::where('id',auth()->id())->pluck('countries_id')->first();
        $subscribe_by_user_country_code = !empty($subscribe_by_user_country_id) ? Countries::where('id', $subscribe_by_user_country_id)->pluck('country_code')->first() : "DK";
        $tax_percentage = TaxRates::where(['name' => 'VAT', 'country' => $subscribe_by_user_country_code])->pluck('percentage')->first();

        $priceExVat = (float)$price;
        $vat_added_amount = $priceExVat * ((100 + $tax_percentage) / 100);
        return floatval($vat_added_amount) * 100;
        //return number_format($vat_added_amount, 2, '.', '');
    }


    public function subscriptionAddonInitial(Request $request) {
        self::createCostumerIfNotExist();

        $validator = Validator::make($request->all(), [
            'addon' => 'gte:1',
            'interval' => Rule::in(["monthly", "biannually", "annually"]),
        ]);
        if ($validator->fails()) abort(404);

        $user = auth()->user();
        $addon = Addons::where(function ($query) use ($user, $request) {
            $query->whereId($request->addon)->where("status", 1);
            if($user->verified_id !== "yes") $query->where("verified_only", 0);
        })->firstOrFail();

        $price = (float)match($request->interval) {
            default => 0,
            $addon->plan_1_type => $addon->plan_1_price,
            $addon->plan_2_type => $addon->plan_2_price,
            $addon->plan_3_type => $addon->plan_3_price
        };
        $query = [
            "addon" => $addon->id,
            "interval" => $request->interval,
            "uniqid" => uniqid(),
        ];


        require base_path()."/mollie-api/vendor/autoload.php";
        // get mollie key
        $mollie_gateway = PaymentGateways::where('name', 'Mollie')->first()->toArray();
        $mollie_api_key = $mollie_gateway['key'];
        \Mollie::api()->setApiKey($mollie_api_key);
        $mollie_customer_id = User::where('id',auth()->id())->pluck('mollie_customer_id')->first();


        // Creating Payment
        $payment = \Mollie::api()->customers->get($mollie_customer_id)->createPayment([
            "amount" => [
                "currency" => "EUR",
                "value" => self::priceWithTax($price),
            ],
            "description" => "Subscribing to: " . ucfirst($addon->name) . " (Addon)",
            "sequenceType" => "first",
            "redirectUrl" => route('subscription-addon-initial-callback') . "?" . http_build_query($query), // after the payment completion where you to redirect
        ]);

        $mollie_subscription_relations = new MollieSubscriptionRelations();
        $mollie_subscription_relations->transaction_id = $payment->id;
        $mollie_subscription_relations->unique_id = $query["uniqid"];
        $mollie_subscription_relations->save();

        $payment = \Mollie::api()->payments()->get($payment->id);

        return redirect($payment->getCheckoutUrl(), 303);
    }


    public function subscriptionAddonInitialCallback(Request $request){
        file_put_contents(self::STORAGE_DIRECTORY . "subscripInitialCallback".rand(10,10000).".json", json_encode($request, JSON_PRETTY_PRINT));

        $user = auth()->user();
        $addon = Addons::where(function ($query) use ($user, $request) {
            $query->whereId($request->addon)->where("status", 1);
            if($user->verified_id !== "yes") $query->where("verified_only", 0);
        })->firstOrFail();

        $plan = match($request->interval) {
            default => 0,
            $addon->plan_1_type => 1,
            $addon->plan_2_type => 2,
            $addon->plan_3_type => 3
        };


        require base_path()."/mollie-api/vendor/autoload.php";

        $mollie_gateway = PaymentGateways::where('name', 'Mollie')->first()->toArray();
        $mollie_api_key = $mollie_gateway['key'];
        \Mollie::api()->setApiKey($mollie_api_key);


        // check if payment was successfull before proceed
        $relation = MollieSubscriptionRelations::where('unique_id', $request->uniqid)->first();
        $payment = \Mollie::api()->payments->get($relation->transaction_id);
        if(!$payment->isPaid()) return redirect()->to(route("addon", $request->addon));


        $mollie_customer_id = User::where('id',auth()->id())->pluck('mollie_customer_id')->first();
        $customer = \Mollie::api()->customers->get($mollie_customer_id);

        // interval for mollie
        $interval_for_mollie = match($request->interval) {
            default => "1 month",
            "weekly" => "1 week",
            "quarterly" => "3 months",
            "biannually" => "6 months",
            "yearly" => "12 months",
        };
        $price = match($request->interval) {
            default => 0,
            $addon->plan_1_type => $addon->plan_1_price,
            $addon->plan_2_type => $addon->plan_2_price,
            $addon->plan_3_type => $addon->plan_3_price
        };
        $plan = match($request->interval) {
            default => 0,
            $addon->plan_1_type => 1,
            $addon->plan_2_type => 2,
            $addon->plan_3_type => 3
        };

        //$interval_for_mollie = '1 day';

        // calculate start date
        $current_date_str = date('Y-m-d');
        $start_date_for_mollie = date('Y-m-d', strtotime("$current_date_str +$interval_for_mollie"));


        $subscription = $customer->createSubscription([
            "amount" => [
                "currency" => "EUR",
                "value" => self::priceWithTax($price),
            ],
            "interval" => $interval_for_mollie,
            "startDate" => $start_date_for_mollie,
            "description" => "Subscription (Addon@" . ucfirst($addon->name) . ") - User@" . $user->id,
            "webhookUrl" => route('mollie-subscription-webhook'),
            "metadata" => [
                "item_type" => "addon",
                "subscribe_by" => $user->id,
                "interval" => $request->interval,
                "plan" => $plan,
                "id" => $addon->id,
            ]
        ]);

        $subscription = json_decode(json_encode($subscription), true);
        $subscription = (array)$subscription;

        // log subscription data
        $subscription_details              = new MollieSubscriptionDetails();
        $subscription_details->subscriber_user_id     = auth()->id();
        $subscription_details->addon_id = $addon->id;
        $subscription_details->subscription_item = "addon";
        $subscription_details->subscribe_id = $subscription['id'];
        $subscription_details->customer_id = $subscription['customerId'];
        $subscription_details->subscribed_at = $subscription['createdAt'];
        $subscription_details->time_period = $request->interval;
        $subscription_details->save();


        // now subscription has been created on mollie, now fetch first transaction it and create subscription on our database
        $relation = MollieSubscriptionRelations::where('unique_id', $request->uniqid)->first();

        $this->startMollieSubscriptionLocally($relation->transaction_id, $subscription['id']);

        return redirect()->to(route("addons"));
    }





  public function mollieSubscriptionFirstPayment(Request $request){

    $cr = $request->cr;
    $interval = $request->interval;

    $url_query = 'cr='.$cr.'&interval='.$interval;

    // get mollie key
    $mollie_gateway = PaymentGateways::where('name', 'Mollie')->first()->toArray();
    $mollie_api_key = $mollie_gateway['key'];

    require base_path()."/mollie-api/vendor/autoload.php";

    \Mollie::api()->setApiKey($mollie_api_key);

    $plan = Plans::where(['user_id'=>$cr, 'interval'=>$interval])->first();
    $mollie_customer_id = User::where('id',auth()->id())->pluck('mollie_customer_id')->first();


    // create dummy id to track new payment after redirection
    $uniqid = uniqid();

    // create new query parameters
    $url_query = 'cr='.$cr.'&interval='.$interval.'&uniqid='.$uniqid;

    // Creating Payment
    $payment = \Mollie::api()->customers->get($mollie_customer_id)->createPayment([
        "amount" => [
           "currency" => "EUR",
           "value" => self::priceWithTax($plan->price),
        ],
        "description" => "Verify to Start Subscription ".rand(1000,9999)."@".$cr,
        "sequenceType" => "first",
        "redirectUrl" => route('mollie-subscription-first-payment-callback').'?'.$url_query, // after the payment completion where you to redirect

    ]);

    $mollie_subscription_relations = new MollieSubscriptionRelations();
    $mollie_subscription_relations->transaction_id = $payment->id;
    $mollie_subscription_relations->unique_id = $uniqid;
    $mollie_subscription_relations->save();

    $payment = \Mollie::api()->payments()->get($payment->id);

    return redirect($payment->getCheckoutUrl(), 303);

  }


  public function mollieSubscriptionFirstPaymentCallback(Request $request){



    // // get mollie key
    // $mollie_gateway = PaymentGateways::where('name', 'Mollie')->first()->toArray();
    // $mollie_api_key = $mollie_gateway['key'];

    // require base_path()."/mollie-api/vendor/autoload.php";

    // \Mollie::api()->setApiKey($mollie_api_key);

    // $subscription = \Mollie::api()->customers->get('cst_cWwUByv4m8')->subscriptions('sub_d9HavC47rJ');
    // $subscription = json_decode(json_encode($subscription), true);

    // echo "<pre>";
    // print_r($subscription);

    // exit();

    $cr = $request->cr;
    $interval = $request->interval;
    $uniqid = $request->uniqid;

    $selected_plan = Plans::where(['user_id'=>$cr, 'interval'=>$interval])->first();

    $mollie_customer_id = User::where('id',auth()->id())->pluck('mollie_customer_id')->first();

    // get mollie key
    $mollie_gateway = PaymentGateways::where('name', 'Mollie')->first()->toArray();
    $mollie_api_key = $mollie_gateway['key'];

    require base_path()."/mollie-api/vendor/autoload.php";

    \Mollie::api()->setApiKey($mollie_api_key);

    // check if payment was successfull before proceed
    $relation = MollieSubscriptionRelations::where('unique_id', $uniqid)->first();

    $payment = \Mollie::api()->payments->get($relation->transaction_id);
    if(!$payment->isPaid()){
      $cr_username = User::where('id', $cr)->pluck('username')->first();
      $url = url('/'.$cr_username);
      return redirect()->to($url);
    }

    $customer = \Mollie::api()->customers->get($mollie_customer_id);

    // interval for mollie
    $interval_for_mollie = '1 month';
    if($interval == 'weekly') { $interval_for_mollie = '1 week'; }
    if($interval == 'quarterly') { $interval_for_mollie = '3 months'; }
    if($interval == 'biannually') { $interval_for_mollie = '6 months'; }
    if($interval == 'yearly') { $interval_for_mollie = '12 months'; }

    //$interval_for_mollie = '1 day';

    // calculate start date
    $current_date_str = strtotime(date('Y-m-d'));
    $start_date_for_mollie = date('Y-m-d', strtotime("+1 month", $current_date_str));

    if($interval == 'weekly') { $start_date_for_mollie = date('Y-m-d', strtotime("+1 week", $current_date_str)); }
    if($interval == 'quarterly') { $start_date_for_mollie = date('Y-m-d', strtotime("+3 month", $current_date_str)); }
    if($interval == 'biannually') { $start_date_for_mollie = date('Y-m-d', strtotime("+6 month", $current_date_str)); }
    if($interval == 'yearly') { $start_date_for_mollie = date('Y-m-d', strtotime("+12 month", $current_date_str)); }


    // add vat in amount
    $subscribe_by_user_country_id = User::where('id',auth()->id())->pluck('countries_id')->first();

    $vat_added_amount = $selected_plan->price;

  $subscribe_by_user_country_code = !empty($subscribe_by_user_country_id) ? Countries::where('id', $subscribe_by_user_country_id)->pluck('country_code')->first() : "DK";
  $tax_percentage = TaxRates::where(['name' => 'VAT', 'country' => $subscribe_by_user_country_code])->pluck('percentage')->first();

  $vat_added_amount += ($vat_added_amount*$tax_percentage)/100;
  $vat_added_amount = number_format((float)$vat_added_amount, 2, '.', '');


    $subscription = $customer->createSubscription([
        "amount" => [
            "currency" => "EUR",
            "value" => $vat_added_amount,
        ],
        "interval" => $interval_for_mollie,
        "startDate" => $start_date_for_mollie,
        "description" => "subscription".rand(1000,9999)."@".$cr,
        "webhookUrl" => route('mollie-subscription-webhook'),
        "metadata" => [
          "to_subscribe" => $cr,
          "subscribe_by" => auth()->id(),
          "interval" => $interval,
          "plan_id" => $selected_plan->id
        ]
    ]);

    $subscription = json_decode(json_encode($subscription), true);

    $subscription = (array)$subscription;

    // log subscription data
     $subscription_details              = new MollieSubscriptionDetails();
     $subscription_details->subscriber_user_id     = auth()->id();
     $subscription_details->content_creator_id = $cr;
     $subscription_details->subscribe_id = $subscription['id'];
     $subscription_details->customer_id = $subscription['customerId'];
     $subscription_details->subscribed_at = $subscription['createdAt'];
     $subscription_details->time_period = $interval;
     $subscription_details->save();


     // now subscription has been created on mollie, now fetch first transaction it and create subscription on our database
     $relation = MollieSubscriptionRelations::where('unique_id', $uniqid)->first();

     $this->startMollieSubscriptionLocally($relation->transaction_id, $subscription['id']);


     // get content creator username for redirection back to page
     $cr_username = User::where('id', $cr)->pluck('username')->first();

     $url = url('/'.$cr_username);

     return redirect()->to($url);

  }

  public function revolutSubscriptionFirstPaymentCallback(Request $request) {

    $cr = $request->cr;
    $interval = $request->interval;
    $uniqid = $request->uniqid;

    $selected_plan = Plans::where(['user_id'=>$cr, 'interval'=>$interval])->first();

    $revolut_customer_id = User::where('id',auth()->id())->pluck('mollie_customer_id')->first();

    // check if payment was successfull before proceed
    $relation = MollieSubscriptionRelations::where('unique_id', $uniqid)->first();

    $revolut_gateway = PaymentGateways::where('name', 'Revolut')->first()->toArray();
    $revolut_api_key = $revolut_gateway['key_secret'];
    $revolut_sandbox = $revolut_gateway['sandbox'];

    $curl = curl_init();

    if($revolut_sandbox == 'true')
      $url = 'https://sandbox-merchant.revolut.com/api/1.0/orders/'.$relation->transaction_id;
    else
      $url = 'https://merchant.revolut.com/api/1.0/orders/'.$relation->transaction_id;

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

    $response = curl_exec($curl);

    curl_close($curl);

    $order = json_decode($response, true);
    //echo "<Pre>";print_r($order);die;

    if(!isset($order['payments'][0]) || $order['payments'][0]['state'] != 'CAPTURED'){
      //$cr_username = User::where('id', $cr)->pluck('username')->first();
      //$url = url('/'.$cr_username);
      //return redirect()->to($url);
      
      echo "payment not captured";
      echo "<Pre>";print_r($order);die;
    }

    $payment_id = $order['payments'][0]['id'];


    // interval for mollie
    $interval_for_mollie = '1 month';
    if($interval == 'weekly') { $interval_for_mollie = '1 week'; }
    if($interval == 'quarterly') { $interval_for_mollie = '3 months'; }
    if($interval == 'biannually') { $interval_for_mollie = '6 months'; }
    if($interval == 'yearly') { $interval_for_mollie = '12 months'; }

    //$interval_for_mollie = '1 day';

   


    // add vat in amount
    $subscribe_by_user_country_id = User::where('id',auth()->id())->pluck('countries_id')->first();

    $vat_added_amount = $selected_plan->price;

  $subscribe_by_user_country_code = !empty($subscribe_by_user_country_id) ? Countries::where('id', $subscribe_by_user_country_id)->pluck('country_code')->first() : "DK";
  $tax_percentage = TaxRates::where(['name' => 'VAT', 'country' => $subscribe_by_user_country_code])->pluck('percentage')->first();

  $vat_added_amount += ($vat_added_amount*$tax_percentage)/100;
  $vat_added_amount = number_format((float)$vat_added_amount, 2, '.', '');


   

    // log subscription data
     $subscription_details              = new MollieSubscriptionDetails();
     $subscription_details->subscriber_user_id     = auth()->id();
     $subscription_details->content_creator_id = $cr;
     $subscription_details->subscribe_id = $payment_id;
     $subscription_details->customer_id = $revolut_customer_id;
     $subscription_details->subscribed_at = $order['created_at'];
     $subscription_details->time_period = $interval;
     $subscription_details->save();


     // now subscription has been created on mollie, now fetch first transaction it and create subscription on our database
     $relation = MollieSubscriptionRelations::where('unique_id', $uniqid)->first();

     $this->startRevolutSubscriptionLocally($payment_id);


     // get content creator username for redirection back to page
     $cr_username = User::where('id', $cr)->pluck('username')->first();

     $url = url('/'.$cr_username);

     return redirect()->to($url);
  }

  public function revolutSubscriptionCronjob()
  {
    $current_date = date("Y-m-d");
    $subscriptions = Subscriptions::whereRaw("DATE_FORMAT(ends_at, '%Y-%m-%d') < '".$current_date."'")->get();
//echo "<Pre>";print_r($subscriptions);die;
    foreach($subscriptions as $subscription)
    {
      $subscription_id = $subscription->subscription_id;

      $plan = Plans::where('name',$subscription->stripe_price)->first();

      $revolut_gateway = PaymentGateways::where('name', 'Revolut')->first()->toArray();
      $revolut_api_key = $revolut_gateway['key_secret'];
      $revolut_sandbox = $revolut_gateway['sandbox'];
      $revolut_customer = User::where('id',$subscription->user_id)->first();

      $revolut_customer_id = $revolut_customer->revolut_customer_id;

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
          "amount": '.self::priceWithTax($plan->price).',
          "currency": "EUR",
          "email": "'.$revolut_customer->email.'"
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

      $order_id = $order['id'];

      $curl = curl_init();

      if($revolut_sandbox == 'true')
        $url = 'https://sandbox-merchant.revolut.com/api/payments/'.$subscription_id;
      else
        $url = 'https://merchant.revolut.com/api/payments/'.$subscription_id;

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

      $response = curl_exec($curl);

      curl_close($curl);

      $payment = json_decode($response, true);

      $payment_method = ['saved_payment_method' => ['type' => $payment['payment_method']['type'], 'id' => $payment['payment_method']['id'], 'initiator' => 'merchant']];

      $curl = curl_init();

      if($revolut_sandbox == 'true')
        $url = 'https://sandbox-merchant.revolut.com/api/orders/'.$order['id'].'/payments';
      else
        $url = 'https://merchant.revolut.com/api/orders/'.$order['id'].'/payments';


      curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($payment_method),
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json',
          'Accept: application/json',
          'Authorization: Bearer '.$revolut_api_key
        ),
      ));

      $response = curl_exec($curl);

      curl_close($curl);

      $payment = json_decode($response, true);

      $this->startRevolutSubscriptionLocally($payment['id'], $subscription_id);

    }

  }

  public function mollieSubscriptionWebhook(Request $request){

      file_put_contents(self::STORAGE_DIRECTORY . "SubscriptionWebhook".rand(10,10000).".json", json_encode($request, JSON_PRETTY_PRINT));
      $payment_id = $request->id;

      if(!empty($payment_id)){

        // get mollie key
        $mollie_gateway = PaymentGateways::where('name', 'Mollie')->first()->toArray();
        $mollie_api_key = $mollie_gateway['key'];

        require base_path()."/mollie-api/vendor/autoload.php";

        \Mollie::api()->setApiKey($mollie_api_key);

        $payment = \Mollie::api()->payments->get($payment_id);

        $subscription_webhook = new MollieSubscriptionWebhooks();
        $payment_array = json_decode(json_encode($payment), true);
        $subscription_webhook->details     = json_encode($payment_array);
        $subscription_webhook->save();

        $this->startMollieSubscriptionLocally($payment_id);

      }
  }

  // when called first time subscription id will be given explicitly as time of payment subscription was not performed
    private function startMollieSubscriptionLocally($payment_id, $subscription_id = '') {

        if(empty($payment_id)) return;
        require base_path()."/mollie-api/vendor/autoload.php";

        $mollie_gateway = PaymentGateways::where('name', 'Mollie')->first()->toArray();
        $mollie_api_key = $mollie_gateway['key'];
        \Mollie::api()->setApiKey($mollie_api_key);

        $payment = \Mollie::api()->payments->get($payment_id);
        $payment_array = json_decode(json_encode($payment), true);
        if (!($payment->isPaid() && ! $payment->hasRefunds() && ! $payment->hasChargebacks())) return;

        if(empty($subscription_id)) $subscription_id = $payment_array['subscriptionId'];
        Subscriptions::where('subscription_id', $subscription_id)->update(['cancelled'=>'yes']);

        $mollie_customer_id = $payment_array['customerId'];
        $mollie_subscription_details = MollieSubscriptionDetails::where('subscribe_id', $subscription_id)->first()->toArray();

        $subscribe_by = $mollie_subscription_details['subscriber_user_id'];
        $interval = $mollie_subscription_details['time_period'];
        $subscriptionItem = $mollie_subscription_details['subscription_item'];

        // user who subscribing
        $subscribe_by_user = User::whereId($subscribe_by)
        ->firstOrFail();

        $subscription = \Mollie::api()->customers->get($mollie_customer_id)->subscriptions($subscription_id);
        $subscription = json_decode(json_encode($subscription), true);

        $sql = new Subscriptions();
        $sql->stripe_status = '';
        $sql->quantity = null;
        $sql->trial_ends_at = null;
        $sql->last_payment = null;
        $sql->free = 'no';
        $sql->cancelled = 'no';
        $sql->rebill_wallet = 'no';
        $sql->taxes = '';
        $sql->ends_at = $subscription[0]['nextPaymentDate'];
        $sql->subscription_id = $subscription_id;
        $sql->user_id = $subscribe_by;
        $sql->interval = $interval;

        if($subscriptionItem === "cr") {
            $to_subscribe = $mollie_subscription_details['content_creator_id'];
            // Find the user to subscribe
            $to_subscribe_user = User::whereVerifiedId('yes')
                ->whereId($to_subscribe)
                ->where('id', '<>', auth()->id())
                ->firstOrFail();

            // Check if Plan exists
            $plan = $to_subscribe_user->plans()
                ->whereInterval($interval)
                ->firstOrFail();

            $sql->name = '';
            $sql->stripe_id = '';
            $sql->stripe_price = $plan->name;
            $amount = $plan->price;
            $platformFee = $to_subscribe_user->custom_fee;

        }
        elseif($subscriptionItem === "addon") {
            $to_subscribe = $mollie_subscription_details['addon_id'];
            $sql->stripe_price = "addon_$to_subscribe";
            $sql->name = 'addon';

            $user = auth()->user();
            $addon = Addons::where(function ($query) use ($user, $to_subscribe) {
                $query->whereId($to_subscribe);
                if($user->verified_id !== "yes") $query->where("verified_only", 0);
            })->firstOrFail();
            $amount = (float)match($interval) {
                default => 0,
                $addon->plan_1_type => $addon->plan_1_price,
                $addon->plan_2_type => $addon->plan_2_price,
                $addon->plan_3_type => $addon->plan_3_price
            };
            $amount = number_format($amount, 2, ".", "");
            $platformFee = 100;
            $sql->stripe_id = $addon->id;
        }
        else return;

        $sql->save();
        $db_subscription_id = $sql->id;

        $earnings = $this->earningsAdminUser($platformFee, $amount, $mollie_gateway['fee'], $mollie_gateway['fee_cents']);

        $subscribe_by_user_country_id = $subscribe_by_user->countries_id;
        $subscribe_by_user_country_code = !empty($subscribe_by_user_country_id) ? Countries::where('id', $subscribe_by_user_country_id)->pluck('country_code')->first() : "DK";
        $tax_rates_id = TaxRates::where(['name' => 'VAT', 'country' => $subscribe_by_user_country_code])->pluck('id')->first();

        // Insert Transaction
        $this->transaction(
            $payment_array['id'],
            $subscribe_by,
            $db_subscription_id,
            $to_subscribe,
            $amount,
            $earnings['user'],
            $earnings['admin'],
            'Mollie',
            $subscriptionItem === "cr" ? "subscription" : "addon",
            $earnings['percentageApplied'],
            $tax_rates_id
          );


        if($subscriptionItem === "cr") $to_subscribe_user->increment('balance', $earnings['user']);


            // Send Email to User and Notification
            //Subscriptions::sendEmailAndNotify(auth()->user()->name, $to_subscribe)





  }



  // when called first time subscription id will be given explicitly as time of payment subscription was not performed
  private function startRevolutSubscriptionLocally($payment_id, $subscription_id='') {

    if(empty($payment_id)) return;

    $revolut_gateway = PaymentGateways::where('name', 'Revolut')->first()->toArray();
    $revolut_api_key = $revolut_gateway['key_secret'];
    $revolut_sandbox = $revolut_gateway['sandbox'];

    $curl = curl_init();

    if($revolut_sandbox == 'true')
      $url = 'https://sandbox-merchant.revolut.com/api/payments/'.$payment_id;
    else
      $url = 'https://merchant.revolut.com/api/payments/'.$payment_id;

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

    $response = curl_exec($curl);

    curl_close($curl);

    $payment = json_decode($response, true);
    //echo "<pre>";print_r($payment);die;

    if ($payment['state']!='captured') return;

    if(empty($subscription_id)) $subscription_id = $payment_id;
    Subscriptions::where('subscription_id', $subscription_id)->update(['cancelled'=>'yes']);

    $mollie_subscription_details = MollieSubscriptionDetails::where('subscribe_id', $subscription_id)->first()->toArray();

    $subscribe_by = $mollie_subscription_details['subscriber_user_id'];
    $interval = $mollie_subscription_details['time_period'];
    $subscriptionItem = $mollie_subscription_details['subscription_item'];

    // user who subscribing
    $subscribe_by_user = User::whereId($subscribe_by)
    ->firstOrFail();

    //$subscription = \Mollie::api()->customers->get($mollie_customer_id)->subscriptions($subscription_id);
    //$subscription = json_decode(json_encode($subscription), true);

    $old_subscription = Subscriptions::where('subscription_id', $subscription_id)->first();
    if($old_subscription)
    {
      $current_date_str = strtotime(date('Y-m-d', strtotime($old_subscription->ends_at)));
    }
    else
    {
      $current_date_str = strtotime(date('Y-m-d'));
    }
     // calculate start date
     
     $end_date_for_revolut = date('Y-m-d', strtotime("+1 month", $current_date_str));
 
     if($interval == 'weekly') { $end_date_for_revolut = date('Y-m-d', strtotime("+1 week", $current_date_str)); }
     if($interval == 'quarterly') { $end_date_for_revolut = date('Y-m-d', strtotime("+3 month", $current_date_str)); }
     if($interval == 'biannually') { $end_date_for_revolut = date('Y-m-d', strtotime("+6 month", $current_date_str)); }
     if($interval == 'yearly') { $end_date_for_revolut = date('Y-m-d', strtotime("+12 month", $current_date_str)); }

    $sql = new Subscriptions();
    $sql->stripe_status = '';
    $sql->quantity = null;
    $sql->trial_ends_at = null;
    $sql->last_payment = null;
    $sql->free = 'no';
    $sql->cancelled = 'no';
    $sql->rebill_wallet = 'no';
    $sql->taxes = '';
    $sql->ends_at = $end_date_for_revolut;
    $sql->subscription_id = $payment_id;
    $sql->user_id = $subscribe_by;
    $sql->interval = $interval;

    if($subscriptionItem === "cr") {
        $to_subscribe = $mollie_subscription_details['content_creator_id'];
        // Find the user to subscribe
        $to_subscribe_user = User::whereVerifiedId('yes')
            ->whereId($to_subscribe)
            ->where('id', '<>', auth()->id())
            ->firstOrFail();

        // Check if Plan exists
        $plan = $to_subscribe_user->plans()
            ->whereInterval($interval)
            ->firstOrFail();

        $sql->name = '';
        $sql->stripe_id = '';
        $sql->stripe_price = $plan->name;
        $amount = $plan->price;
        $platformFee = $to_subscribe_user->custom_fee;

    }
    elseif($subscriptionItem === "addon") {
        $to_subscribe = $mollie_subscription_details['addon_id'];
        $sql->stripe_price = "addon_$to_subscribe";
        $sql->name = 'addon';

        $user = auth()->user();
        $addon = Addons::where(function ($query) use ($user, $to_subscribe) {
            $query->whereId($to_subscribe);
            if($user->verified_id !== "yes") $query->where("verified_only", 0);
        })->firstOrFail();
        $amount = (float)match($interval) {
            default => 0,
            $addon->plan_1_type => $addon->plan_1_price,
            $addon->plan_2_type => $addon->plan_2_price,
            $addon->plan_3_type => $addon->plan_3_price
        };
        $amount = number_format($amount, 2, ".", "");
        $platformFee = 100;
        $sql->stripe_id = $addon->id;
    }
    else return;

    $sql->save();
    $db_subscription_id = $sql->id;

    $earnings = $this->earningsAdminUser($platformFee, $amount, $revolut_gateway['fee'], $revolut_gateway['fee_cents']);

    $subscribe_by_user_country_id = $subscribe_by_user->countries_id;
    $subscribe_by_user_country_code = !empty($subscribe_by_user_country_id) ? Countries::where('id', $subscribe_by_user_country_id)->pluck('country_code')->first() : "DK";
    $tax_rates_id = TaxRates::where(['name' => 'VAT', 'country' => $subscribe_by_user_country_code])->pluck('id')->first();

    // Insert Transaction
    $this->transaction(
        $payment_id,
        $subscribe_by,
        $db_subscription_id,
        $to_subscribe,
        $amount,
        $earnings['user'],
        $earnings['admin'],
        'Revolut',
        $subscriptionItem === "cr" ? "subscription" : "addon",
        $earnings['percentageApplied'],
        $tax_rates_id
      );


    if($subscriptionItem === "cr") $to_subscribe_user->increment('balance', $earnings['user']);


        // Send Email to User and Notification
        //Subscriptions::sendEmailAndNotify(auth()->user()->name, $to_subscribe)





}

  public function cancelMollieSubscription($subscription_id){

    if(!empty($subscription_id)){

      $mollie_subscription_id = Subscriptions::where('id',$subscription_id)->pluck('subscription_id')->first();
      $mollie_customer_id = User::where('id',auth()->id())->pluck('mollie_customer_id')->first();

      // get mollie key
      $mollie_gateway = PaymentGateways::where('name', 'Mollie')->first()->toArray();
      $mollie_api_key = $mollie_gateway['key'];

      require base_path()."/mollie-api/vendor/autoload.php";

      \Mollie::api()->setApiKey($mollie_api_key);

      $customer = \Mollie::api()->customers->get($mollie_customer_id);

      $subscription = $customer->cancelSubscription($mollie_subscription_id);

      //echo "<pre>";
      //print_r($subscription);
      $subscription = json_decode(json_encode($subscription), true);
      //print_r($subscription);

      if($subscription['status'] == 'canceled'){
        Subscriptions::where('id', $subscription_id)->update(array('cancelled' => 'yes'));
      }

      return redirect()->to(url('/my/subscriptions'));

    }

  }

  /**
	 * Free subscription
	 *
   */
  public function subscriptionFree()
  {
    // Find user
    $creator = User::whereId($this->request->id)
        ->whereFreeSubscription('yes')
        ->whereVerifiedId('yes')
          ->firstOrFail();

    // Verify plan no is empty
    if (! $creator->plan) {
       $creator->plan = 'user_'.$creator->id;
       $creator->save();
    }

    // Check if not plans
    if ($creator->plans()->count() == 0) {

        Plans::updateOrCreate(
          [
            'user_id' => $creator->id,
            'name' => 'user_'.$creator->id
          ],
         [
           'interval' => 'monthly',
           'status' => '1'
        ]);
    }

    // Verify subscription exists
    $subscription = Subscriptions::whereUserId(auth()->id())
        ->whereStripePrice($creator->plan)
          ->whereFree('yes')
            ->first();

      if ($subscription) {
        return response()->json([
          'success' => false,
          'error' => trans('general.subscription_exists'),
        ]);
      }

    // Insert DB
    $sql          = new Subscriptions();
    $sql->user_id = auth()->id();
    $sql->stripe_price = $creator->plan;
    $sql->free = 'yes';
    $sql->save();

    // Send Email to User and Notification
    Subscriptions::sendEmailAndNotify(auth()->user()->name, $creator->id);

    return response()->json([
      'success' => true,
    ]);
  } // End Method SubscriptionFree

  public function cancelFreeSubscription($id)
  {
    $checkSubscription = auth()->user()->userSubscriptions()->whereId($id)->firstOrFail();
    $creator = User::wherePlan($checkSubscription->stripe_price)->first();

    // Delete Subscription
    $checkSubscription->delete();

    session()->put('subscription_cancel', trans('general.subscription_cancel'));
    return redirect($creator->username);

  }// End Method cancelFreeSubscription

  public function cancelWalletSubscription($id)
  {
    $subscription = auth()->user()->userSubscriptions()->whereId($id)->firstOrFail();
    $creator = Plans::whereName($subscription->stripe_price)->first();

    // Delete Subscription
    $subscription->cancelled = 'yes';
    $subscription->save();

    session()->put('subscription_cancel', trans('general.subscription_cancel'));
    return redirect($creator->user()->username);

  }// End Method cancelWalletSubscription

  /**
	 *  Subscription via Wallet
	 *
	 * @return Response
	 */
   protected function sendWallet()
   {
     // Find user
     $creator = User::whereId($this->request->id)
         ->whereVerifiedId('yes')
           ->firstOrFail();

     // Check if Plan exists
     $plan = $creator->plans()
       ->whereInterval($this->request->interval)
          ->firstOrFail();

     $amount = $plan->price;

     // Verify plan no is empty
     if (! $creator->plan) {
        $creator->plan = 'user_'.$creator->id;
        $creator->save();
     }

     if (auth()->user()->wallet < Helper::amountGross($amount)) {
       return response()->json([
         "success" => false,
         "errors" => ['error' => __('general.not_enough_funds')]
       ]);
     }

     // Insert DB
     $subscription              = new Subscriptions();
     $subscription->user_id     = auth()->id();
     $subscription->stripe_price = $plan->name;
     $subscription->ends_at     = $creator->planInterval($plan->interval);
     $subscription->rebill_wallet = 'on';
     $subscription->interval = $plan->interval;
     $subscription->taxes = auth()->user()->taxesPayable();
     $subscription->save();

     // Admin and user earnings calculation
     $earnings = $this->earningsAdminUser($creator->custom_fee, $amount, null, null);

     // Insert Transaction
     $this->transaction(
        'subw_'.str_random(25),
        auth()->id(),
        $subscription->id,
        $creator->id,
        $amount,
        $earnings['user'],
        $earnings['admin'],
        'Wallet',
        'subscription',
        $earnings['percentageApplied'],
        auth()->user()->taxesPayable()
      );

     // Subtract user funds
     auth()->user()->decrement('wallet', Helper::amountGross($amount));

     // Add Earnings to User
     $creator->increment('balance', $earnings['user']);

     // Send Email to User and Notification
     Subscriptions::sendEmailAndNotify(auth()->user()->name, $creator->id);

     return response()->json([
       'success' => true,
       'url' => url('buy/subscription/success', $creator->username)
     ]);

   } // End sendTipWallet

}
