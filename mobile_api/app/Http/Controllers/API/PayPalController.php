<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\AdminSettings;
use App\Models\Subscriptions;
use App\Models\Notifications;
use App\Models\User;
use Fahim\PaypalIPN\PaypalIPNListener;
use App\Helper;
use Mail;
use Carbon\Carbon;
use App\Models\PaymentGateways;
use App\Models\Transactions;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Http\Controllers\Traits\Functions;

class PayPalController extends Controller
{
    use Functions;
    public function __construct(AdminSettings $settings, Request $request) {
		$this->settings = $settings::first();
		$this->request = $request;
	}
    public function show()
    {
        // dd($this->request->id);
        if(! $this->request->expectsJson()) {
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'Error OCcured.',
            ];
            return response()->json($response , 404);
            // abort(404);
        }
        // Find the User
        $user = User::whereVerifiedId('yes')->whereId($this->request->id)->where('id', '<>', auth()->id())->firstOrFail();
        // Check if Plan exists
        $plan = $user->plans()->whereInterval($this->request->interval)->whereStatus('1')->firstOrFail();
        // Get Payment Gateway
        $payment = PaymentGateways::findOrFail($this->request->payment_gateway);
        // Verify environment Sandbox or Live
        if($payment->sandbox == 'true') {
			$action = "https://www.sandbox.paypal.com/cgi-bin/webscr";
		} else {
			$action = "https://www.paypal.com/cgi-bin/webscr";
		}
        $urlSuccess = url('buy/subscription/success', $user->username).'?paypal=1';
  		$urlCancel   = url('buy/subscription/cancel', $user->username);
  		$urlPaypalIPN = url('paypal/ipn');
        switch($plan->interval) {
            case 'weekly':
                $interval = 'D';
                $interval_count = 7;
                break;
            case 'monthly':
                $interval = 'M';
                $interval_count = 1;
                break;
            case 'quarterly':
                $interval = 'M';
                $interval_count = 3;
                break;
            case 'biannually':
                $interval = 'M';
                $interval_count = 6;
                break;
            case 'yearly':
                $interval = 'Y';
                $interval_count = 1;
                break;
        }
        $response = [
            'success' => true,
            'data' => [
                'insertBody' => '<form id="form_pp" name="_xclick" action="'.$action.'" method="post"  style="display:none">
                                    <input type="hidden" name="cmd" value="_xclick-subscriptions"/>
                                    <input type="hidden" name="return" value="'.$urlSuccess.'">
                                    <input type="hidden" name="cancel_return"   value="'.$urlCancel.'">
                                    <input type="hidden" name="notify_url" value="'.$urlPaypalIPN.'">
                                    <input type="hidden" name="no_shipping" value="1">
                                    <input type="hidden" name="currency_code" value="'.$this->settings->currency_code.'">
                                    <input type="hidden" name="item_name" value="'.trans('general.subscription_for').' @'.$user->username.'">
                                    <input type="hidden" name="custom" value="id='.$this->request->id.'&amount='.$plan->price.'&subscriber='.auth()->id().'&name='.auth()->user()->name.'&plan='.$plan->name.'&taxes='.auth()->user()->taxesPayable().'">
                                    <input type="hidden" name="a3" value="'.Helper::amountGross($plan->price).'"/>
                                    <input type="hidden" name="p3" value="'.$interval_count.'"/>
                                    <input type="hidden" name="t3" value="'.$interval.'"/>
                                    <input type="hidden" name="src" value="1"/>
                                    <input type="hidden" name="rm" value="2"/>
                                    <input type="hidden" name="business" value="'.$payment->email.'">
                                </form> <script type="text/javascript">document._xclick.submit();</script>',
            ],
            'message' => 'HTML Data.',
        ];
        return response()->json($response , 200);
  		// return response()->json([
  		// 	'success' => true,
  		// 	'insertBody' => '<form id="form_pp" name="_xclick" action="'.$action.'" method="post"  style="display:none">
        //     <input type="hidden" name="cmd" value="_xclick-subscriptions"/>
        //     <input type="hidden" name="return" value="'.$urlSuccess.'">
  		// 	<input type="hidden" name="cancel_return"   value="'.$urlCancel.'">
        //     <input type="hidden" name="notify_url" value="'.$urlPaypalIPN.'">
        //     <input type="hidden" name="no_shipping" value="1">
        //     <input type="hidden" name="currency_code" value="'.$this->settings->currency_code.'">
        //     <input type="hidden" name="item_name" value="'.trans('general.subscription_for').' @'.$user->username.'">
        //     <input type="hidden" name="custom" value="id='.$this->request->id.'&amount='.$plan->price.'&subscriber='.auth()->id().'&name='.auth()->user()->name.'&plan='.$plan->name.'&taxes='.auth()->user()->taxesPayable().'">
        //     <input type="hidden" name="a3" value="'.Helper::amountGross($plan->price).'"/>
        //     <input type="hidden" name="p3" value="'.$interval_count.'"/>
        //     <input type="hidden" name="t3" value="'.$interval.'"/>
        //     <input type="hidden" name="src" value="1"/>
        //     <input type="hidden" name="rm" value="2"/>
        //     <input type="hidden" name="business" value="'.$payment->email.'">
        //     </form> <script type="text/javascript">document._xclick.submit();</script>',
  		// ]);
    }
}
