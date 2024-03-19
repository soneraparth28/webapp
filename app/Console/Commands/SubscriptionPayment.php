<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Plans;
use App\Models\Subscriptions;
use App\Models\Transactions;
use App\Models\PaymentGateways;
use App\Models\MollieSubscriptionDetails;
use App\Models\Addons;
use App\Models\TaxRates;
use App\Http\Controllers\Traits\Functions;

class SubscriptionPayment extends Command
{
    use Functions;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Capture Payment for subscription automatically at monthly interval using Revolut';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $current_date = date("Y-m-d");
        $subscriptions = Subscriptions::whereRaw("DATE_FORMAT(ends_at, '%Y-%m-%d') < '".$current_date."' and cancelled = 'no'")->get();
    //echo "<Pre>";print_r($subscriptions);die;
    
        foreach($subscriptions as $subscription)
        {
           // $this->info('Subscription payment has been done successfully...'.$subscription->subscription_id);
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
            'Revolut-Api-Version: 2023-09-01',
            'Authorization: Bearer '.$revolut_api_key
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $payment = json_decode($response, true);

        $this->startRevolutSubscriptionLocally($payment['id'], $subscription_id);

        }
        $this->info('Subscription payment has been done successfully');
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

}
