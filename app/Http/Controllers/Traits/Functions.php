<?php

namespace App\Http\Controllers\Traits;

use DB;
use Illuminate\Http\Request;
use Mail;
use App\Helper;
use App\Models\User;
use App\Models\AdminSettings;
use App\Models\Subscriptions;
use App\Models\Notifications;
use App\Models\Comments;
use App\Models\Like;
use App\Models\Updates;
use App\Models\TaxRates;
use App\Models\Reports;
use App\Models\VerificationRequests;
use App\Models\Referrals;
use App\Models\ReferralTransactions;
use App\Models\PaymentGateways;
use App\Models\Conversations;
use App\Models\Messages;
use App\Models\Bookmarks;
use App\Models\Transactions;
use App\Models\PayPerViews;
use App\Models\Deposits;
use App\Models\ContentCreatorsReferralsTransactions;

use App\Notifications\TipReceived;
use App\Notifications\PayPerViewReceived;
use App\Models\TwoFactorCodes;
use App\Notifications\SendTwoFactorCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

trait Functions {

	public function __construct(AdminSettings $settings) {
    $this->settings = $settings::first();
  }

	// Users on Card Explore
	public function userExplore()
	{
		return User::where('status','active')
			->where('id', '<>', auth()->user()->id ?? 0)
				->whereVerifiedId('yes')
				->where('id', '<>', $this->settings->hide_admin_profile == 'on' ? 1 : 0)
				->whereHas('plans', function($query) {
					$query->where('status', '1');
				})
				->whereFreeSubscription('no')
				->whereHideProfile('no')
				->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')
			->orWhere('status','active')
				->where('id', '<>', auth()->user()->id ?? 0)
					->whereVerifiedId('yes')
					->where('id', '<>', $this->settings->hide_admin_profile == 'on' ? 1 : 0)
					->whereFreeSubscription('yes')
					->whereHideProfile('no')
					->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')
				->inRandomOrder()
				->paginate(3);
	}// End Method

	// CCBill Form
	public function ccbillForm($price, $userAuth, $type, $creator = null, $isMessage = null)
	{
		// Get Payment Gateway
		$payment = PaymentGateways::whereName('CCBill')->firstOrFail();

		if ($creator) {
		$user  = User::whereVerifiedId('yes')->whereId($creator)->firstOrFail();
		}

		$currencyCodes = [
			'AUD' => 036,
			'CAD' => 124,
			'JPY' => 392,
			'GBP' => 826,
			'USD' => 840,
			'EUR' => 978
		];

		if ($type == 'wallet') {

			$taxes = ($this->request->amount * auth()->user()->isTaxable()->sum('percentage') / 100);

			if ($this->settings->currency_code == 'JPY') {
				$formPrice = round($price + ($price * $payment->fee / 100) + $payment->fee_cents + $taxes, 2, '.', '');
			} else {
				$formPrice = number_format($price + ($price * $payment->fee / 100) + $payment->fee_cents + $taxes, 2, '.', '');
			}
		} else {
			$charge = Helper::amountGross($price);
			$formPrice = number_format($charge, 2);
		}

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
		$input['type']          = $type;
		$input['isMessage']     = $isMessage;
		$input['creator']       = $user->id ?? null;
		$input['user']          = $userAuth;
		$input['amountFixed']   = $price;
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

	// Admin and user earnings calculation
	public function earningsAdminUser($userCustomFee, $amount, $paymentFee, $paymentFeeCents, $forceNoFee = false)
	{
		$settings = AdminSettings::first();

		$feeCommission = !$forceNoFee && $userCustomFee == 0 ? $settings->fee_commission : $userCustomFee;

		if (isset($paymentFee)) {
			$processorFees = $amount - ($amount * $paymentFee/100) - $paymentFeeCents;

			// Earnings Net User
			$earningNetUser = $processorFees - ($processorFees * $feeCommission/100);
			// Earnings Net Admin
			$earningNetAdmin = $processorFees - $earningNetUser;
		} else {
			// Earnings Net User
      $earningNetUser = $amount - ($amount * $feeCommission/100);

      // Earnings Net Admin
      $earningNetAdmin = ($amount - $earningNetUser);
		}

		if (isset($paymentFee)) {
			$paymentFees =  $paymentFeeCents == 0.00 ? $paymentFee.'% + ' : $paymentFee.'%'.' + '.$paymentFeeCents.' + ';
		} else {
			$paymentFees = null;
		}

		// Percentage applied
		$percentageApplied = $paymentFees.$feeCommission.'%';


		if ($settings->currency_code == 'JPY') {
			$userEarning = floor($earningNetUser);
			$adminEarning = floor($earningNetAdmin);
		} else {
			$userEarning = number_format($earningNetUser, 2, '.', '');
			$adminEarning = number_format($earningNetAdmin, 2, '.', '');
		}

		return [
			'user' => $userEarning,
			'admin' => $adminEarning,
			'percentageApplied' => $percentageApplied
		];

	}// End Method

	// Insert Transaction
	public function transaction(
		$txnId,
		$userId,
		$subscriptionsId,
		$subscribed,
		$amount,
		$userEarning,
		$adminEarning,
		$paymentGateway,
		$type,
		$percentageApplied,
		$taxes,
		$approved = '1'
		) {
				$settings = AdminSettings::first();

				// Referred
				$referred = $approved == '1' ? $this->referred($userId, $adminEarning, $type) : null;
				
				// CC Referred
				$cc_referred = $approved == '1' ? $this->referredCC($subscribed, $adminEarning, $type) : null;
				
				$admin_earnings = $adminEarning;
				
				if($referred != null){
				    $admin_earnings = $referred['adminEarning'];
				}
				else if($cc_referred != null){
				    $admin_earnings = $cc_referred['adminEarning'];
				}
				else{}

				// Stripe Connect
				if ($paymentGateway == 'Stripe' && $type == 'subscription'
					|| $paymentGateway == 'Stripe' && $type == 'tip'
					|| $paymentGateway == 'Stripe' && $type == 'ppv')
					{
						$stripeConnect = $approved == '1' ? $this->stripeConnect($userId, $type, $userEarning) : null;
					}

					// Insert Transaction
					$txn = new Transactions();
					$txn->txn_id  = $txnId;
					$txn->user_id = $userId;
					$txn->subscriptions_id = $subscriptionsId;
					$txn->subscribed = $subscribed;
					$txn->amount   = $amount;
					$txn->earning_net_user  =  $userEarning;
				// 	$txn->earning_net_admin = $referred ? $referred['adminEarning'] : $adminEarning;
					$txn->earning_net_admin = $admin_earnings;
					$txn->payment_gateway = $paymentGateway;
					$txn->type = $type;
					$txn->percentage_applied = $percentageApplied;
					$txn->approved = $approved;
					$txn->referred_commission = $referred ? true : false;
					$txn->taxes = $taxes;
					$txn->direct_payment = $stripeConnect ?? false;
					$txn->save();


					// Update Transaction ID on ReferralTransactions
					if ($referred) {
						ReferralTransactions::whereId($referred['txnId'])->update([
							'transactions_id' => $txn->id
						]);
					}
					
					// Update Transaction ID on ContentCreatorReferralTransactions
					if ($cc_referred) {
						ContentCreatorsReferralsTransactions::whereId($cc_referred['txnId'])->update([
							'transactions_id' => $txn->id
						]);
					}

					//Saving user's data
					$user = User::where("id", $userId)->first();
					if($user){
						$txn->invoice_name = !blank($user->name) ? $user->name : NULL ;
						$txn->invoice_company = !blank($user->company) ? $user->company : NULL ;
						$txn->invoice_address = !blank($user->address) ? $user->address : NULL ;
						$txn->invoice_city = !blank($user->city) ? $user->city : NULL ;
						$txn->invoice_zip = !blank($user->zip) ? $user->zip : NULL ;
						$txn->invoice_email = !blank($user->email) ? $user->email : NULL ;
						$txn->invoice_country = !blank($user->countries_id) ? $user->country()->country_name : NULL ;
						$txn->save();
					}


					return $txn;

		}// End Method Insert Transaction

	// Insert PayPerViews
	public function payPerViews($user_id, $updates_id, $messages_id)
	{
		$sql = new PayPerViews();
		$sql->user_id = $user_id;
		$sql->updates_id = $updates_id;
		$sql->messages_id = $messages_id;
		$sql->save();

	}// End Method

	// Send notification via Email to creator that you have received a tip
	protected function notifyEmailNewTip($user, $tipper, $amount)
	{
		$data = [
				'tipper' => $tipper,
				'amount' => $amount
			];

			try {
				$user->notify(new TipReceived($data));
			} catch (\Exception $e) {
				\Log::info($e->getMessage());
			}
	} // End Method

	// Send notification via Email to creator that you have received a PPV
	protected function notifyEmailNewPPV($user, $buyer, $media, $type)
	{
		$data = [
				'buyer' => $buyer,
				'content' => $media,
				'type' => $type
			];

			try {
				$user->notify(new PayPerViewReceived($data));
			} catch (\Exception $e) {
				\Log::info($e->getMessage());
			}
	} // End Method

	// Insert Deposit (Add funds user wallet)
	public function deposit($userId, $txnId, $amount, $paymentGateway, $taxes, $screenshotTransfer = '')
	{
		$payment = PaymentGateways::whereName($paymentGateway)->firstOrFail();
		$paymentFee = $payment->fee;
		$paymentFeeCents = $payment->fee_cents;

		// Percentage applied
		$percentageApplied =  $paymentFeeCents == 0.00 ? $paymentFee.'%' : $paymentFee.'%'.' + '.$paymentFeeCents;

		// Percentage applied amount
		$transactionFeeAmount = number_format($amount + ($amount * $paymentFee / 100) + $paymentFeeCents, 2, '.', '');
		$transactionFee = ($transactionFeeAmount - $amount);

		$sql = new Deposits();
		$sql->user_id = $userId;
		$sql->txn_id = $txnId;
		$sql->amount = $amount;
		$sql->payment_gateway = $paymentGateway;
		$sql->status = $paymentGateway == 'Bank' ? 'pending' : 'active';
    $sql->screenshot_transfer = $screenshotTransfer;
		$sql->percentage_applied = $percentageApplied;
		$sql->transaction_fee = $transactionFee;
		$sql->taxes = $taxes;
		$sql->save();

		return $sql;

	}// End Method

	public function generateTwofaCode($user)
  {
    $code = rand(1000, 9999);

    // Delete old session user id
    session()->forget('user:id');

    // Create session user
    session()->put('user:id', $user->id);

        TwoFactorCodes::updateOrCreate([
          'user_id' => $user->id,
          'code' => $code
        ]);

        try {
            $data = ['code' => $code];

            $user->notify(new SendTwoFactorCode($data));

        } catch (Exception $e) {
            \Log::info("Error: ". $e->getMessage());
        }
  }// End method

	public function createTaxStripe($id, $name, $country, $stateCode, $percentage)
	{
		$payment = PaymentGateways::whereName('Stripe')
			->whereEnabled('1')
			->where('key_secret', '<>', '')
			->first();

			if ($payment) {
				try {
					$stripe = new \Stripe\StripeClient($payment->key_secret);

					if ($stateCode) {
						$tax = $stripe->taxRates->create([
							'display_name' => $name,
							'description' => $name.' - '.$country->country_name,
							'country' => $country->country_code,
							'jurisdiction' => $country->country_code,
							'state' => $stateCode,
							'percentage' => $percentage,
							'inclusive' => false,
						]);
					} else {
						$tax = $stripe->taxRates->create([
							'display_name' => $name,
							'description' => $name.' - '.$country->country_name,
							'country' => $country->country_code,
							'jurisdiction' => $country->country_code,
							'percentage' => $percentage,
							'inclusive' => false,
						]);
					}

					// Insert ID to tax_rates table
					TaxRates::whereId($id)->update([
						'stripe_id' => $tax->id
					]);


				} catch (\Exception $e) {
					\Log::debug($e->getMessage());
				}
			}
	}// End method

	public function updateTaxStripe($stripe_id, $name, $status)
	{
		$payment = PaymentGateways::whereName('Stripe')
			->whereEnabled('1')
			->where('key_secret', '<>', '')
			->first();

			if ($payment) {
				try {
					$stripe = new \Stripe\StripeClient($payment->key_secret);

					$stripe->taxRates->update($stripe_id,
					['active' => $status ? 'true' : 'false',
					'display_name' => $name
					]);

				} catch (\Exception $e) {
					\Log::debug($e->getMessage());
				}
			}
	}// End method

	protected function referred($userId, $adminEarning, $type)
	{
		$settings = AdminSettings::first();

		// Check Referred
		if ($settings->referral_system == 'on') {

			// Check for referred
			$referred = Referrals::whereUserId($userId)->first();

					if ($referred) {

						// Check if the user who referred exists
						$referredBy = User::find($referred->referred_by);

						if ($referredBy) {

							// Check numbers of transactions
							$transactions = ReferralTransactions::whereUserId($userId)->count();

							if ($settings->referral_transaction_limit == 'unlimited'
									|| $transactions < $settings->referral_transaction_limit
								) {

									$adminEarningFinal = $adminEarning - ($adminEarning * $settings->percentage_referred/100);

									$earningNetUser = ($adminEarning - $adminEarningFinal);
									$adminEarning   = ($adminEarning - $earningNetUser);

									if ($settings->currency_code == 'JPY') {
										$earningNetUser = floor($earningNetUser);
										$adminEarning   = floor($adminEarning);
									} else {
										$earningNetUser = round($earningNetUser, 2, PHP_ROUND_HALF_DOWN);
										$adminEarning   = round($adminEarning, 2, PHP_ROUND_HALF_DOWN);
									}

									if ($earningNetUser != 0) {
										// Insert User Earning
										$newTransaction = new ReferralTransactions();
										$newTransaction->referrals_id = $referred->id;
										$newTransaction->user_id = $referred->user_id;
										$newTransaction->referred_by = $referred->referred_by;
										$newTransaction->earnings = $earningNetUser;
										$newTransaction->type = $type;
										$newTransaction->save();

										// Add Earnings to User
										$referred->referredBy()->increment('balance', $earningNetUser);

										// Notify to user - destination, author, type, target
										Notifications::send($referred->referred_by, $referred->referred_by, 11, $referred->referred_by);

										return [
											'txnId' => $newTransaction->id,
											'adminEarning' => $adminEarning
										];
									}
							}
						}//=== $referredBy
					}// $referred
		}// referral_system On

		return false;
	}// End Method referred
	
	protected function referredCC($creator_id, $adminEarning, $type)
	{
	    $settings = AdminSettings::first();
	    
		$creator = User::whereId($creator_id)->first();
		
		$creator_referrer_id = $creator->creator_referred_id;
		
		$creator_referrer = User::whereId($creator_referrer_id)->first();
		
		if (!empty($creator_referrer_id)){
            
			$adminEarningFinal = $adminEarning - ($adminEarning * 40/100);
			$earningNetUser = ($adminEarning - $adminEarningFinal);
			$adminEarning   = ($adminEarning - $earningNetUser);
            
			if ($settings->currency_code == 'JPY') {
				$earningNetUser = floor($earningNetUser);
				$adminEarning   = floor($adminEarning);
			} else {
				$earningNetUser = round($earningNetUser, 2, PHP_ROUND_HALF_DOWN);
				$adminEarning   = round($adminEarning, 2, PHP_ROUND_HALF_DOWN);
			}

			if ($earningNetUser != 0) {
				$newTransaction = new ContentCreatorsReferralsTransactions();
				$newTransaction->user_id = $creator_referrer_id;
				$newTransaction->content_creator_id = $creator_id;
				$newTransaction->earnings = $earningNetUser;
				$newTransaction->type = $type;
				$newTransaction->save();
                
				// Add Earnings to User
			    $new_balance = $creator_referrer->balance + $earningNetUser;
			    User::where(['id'=>$creator_referrer_id])->update(['balance' => $new_balance]);
                
				// Notify to user - destination, author, type, target
				Notifications::send($creator_referrer_id, $creator_referrer_id, 11, $creator_referrer_id);
				
				return [
					'txnId' => $newTransaction->id,
					'adminEarning' => $adminEarning
				];
			}

		}

		return false;
	}// End Method referred
	
// 	protected function referredCCTest()
// 	{
	    
// 	    echo $creator_id = 233;
// 	    $adminEarning = 1;
// 	    $type = 'subscription';
	    
// 	   // exit();
	    
	    
// 	    $settings = AdminSettings::first();
	    
// 		$creator = User::whereId($creator_id)->first();
		
// 		$creator_referrer_id = $creator->creator_referred_id;
		
// 		$creator_referrer = User::whereId($creator_referrer_id)->first();
		
// // 		echo $creator_referrer->balance;
// // 		exit();
		
// 		if (!empty($creator_referrer_id)){
            
// 			$adminEarningFinal = $adminEarning - ($adminEarning * 40/100);
// 			$earningNetUser = ($adminEarning - $adminEarningFinal);
// 			$adminEarning   = ($adminEarning - $earningNetUser);
            
// 			if ($settings->currency_code == 'JPY') {
// 				$earningNetUser = floor($earningNetUser);
// 				$adminEarning   = floor($adminEarning);
// 			} else {
// 				$earningNetUser = round($earningNetUser, 2, PHP_ROUND_HALF_DOWN);
// 				$adminEarning   = round($adminEarning, 2, PHP_ROUND_HALF_DOWN);
// 			}

// 			if ($earningNetUser != 0) {
// 				$newTransaction = new ContentCreatorsReferralsTransactions();
// 				$newTransaction->user_id = $creator_referrer_id;
// 				$newTransaction->content_creator_id = $creator_id;
// 				$newTransaction->earnings = $earningNetUser;
// 				$newTransaction->type = $type;
// 				$newTransaction->save();
                
//                 echo $new_balance = $creator_referrer->balance + $earningNetUser;
//                 echo $creator_referrer_id;
// 				// Add Earnings to User
// 				// $creator_referrer->update([
// 				//     'balance' => $new_balance
// 			 //   ]);
// 			    User::where(['id'=>$creator_referrer_id])->update(['balance' => $new_balance]);
                
// 				// Notify to user - destination, author, type, target
// 				//Notifications::send($referred->referred_by, $referred->referred_by, 11, $referred->referred_by);
                
// 				return [
// 					'txnId' => $newTransaction->id,
// 					'adminEarning' => $adminEarning
// 				];
// 			}

// 		}

// 		return false;
// 	}// End Method referred


	protected function stripeConnect($user, $type, $earnings)
	{
		$settings = AdminSettings::first();

		// Get Payment Gateway
		$payment = PaymentGateways::whereName('Stripe')->first();

		// Get User
		$user = User::find($user);

		// Stripe Connect
		if ($user->stripe_connect_id && $user->completed_stripe_onboarding) {
			try {
				// Stripe Client
				$stripe = new \Stripe\StripeClient($payment->key_secret);

				$earningsUser = $settings->currency_code == 'JPY' ? $earnings : ($earnings*100);

				switch ($type) {
					case 'tip':
						$description = __('general.tip');
						break;

						case 'ppv':
							$description = __('general.ppv');
							break;

							case 'subscription':
								$description = __('general.subscription');
								break;
				}

				$stripe->transfers->create([
					'amount' => $earningsUser,
					'currency' => $settings->currency_code,
					'destination' => $user->stripe_connect_id,
					'description' => $description
				]);

				// Subtract amount from balance
				$user->decrement('balance', $earningsUser);

				return true;

			} catch (\Exception $e) {
				return false;

				Log::info($e->getMessage());
			}
		}
		return false;
	}// End Method stripeConnect





    //Load transaction data to graph and table sorted
    public function filterTransactions($transactions, $request) {
        return $transactions->filter(function ($transaction) use ($request) {

            if($request->data_type === "chart") {
                if($request->year) {
                    if($transaction->created_at->year !== (int)$request->year) return false;
                }
                if($request->month && $request->month !== "all") {
                    if($transaction->created_at->month !== (int)$request->month) return false;
                }
                if($request->country && $request->country !== "all") {
                    if(strtolower($transaction->invoice_country) !== strtolower($request->country)) return false;
                }
            }
            elseif($request->data_type === "table") {
                $timeStart = (int)$request->start;
                $timeEnd = (int)$request->end;
                if(strtotime($transaction->created_at) < $timeStart || strtotime($transaction->created_at) > $timeEnd) return false;
            }


            return true;
        });
    }

    private function mapDataByDate($filteredItems, $request): array {
        if($request->month !== "all") {
            $month = strlen($request->month) === 1 ? 0 . $request->month : $request->month;
            $timeStart = strtotime($request->year . $month . "01");
            $timeEnd = strtotime(date("Y-m-d", $timeStart) . " +1 month");
            $additionKey = "day";
            $dateKey = "Y-m-d";
        }
        else {
            $timeStart = strtotime($request->year . "0101");
            $timeEnd = strtotime(date("Y-m-d", $timeStart) . " +1 year");
            $additionKey = "month";
            $dateKey = "Y-m";
        }

        $endDate = date($dateKey, $timeEnd);
        $currentDate = date($dateKey, $timeStart);
        $dataPointListKeys = array();
        while($currentDate !== $endDate) {
            if(strtotime($currentDate) > strtotime($endDate)) break;
            $dataPointListKeys[] = $currentDate;
            $currentDate = date($dateKey, strtotime($currentDate . " +1 $additionKey"));
        }

        $response = [];
        foreach ($dataPointListKeys as $date) {
            $response[$date] = $filteredItems->filter(function ($item) use($date, $dateKey) {
//                echo json_encode(array(date($dateKey, strtotime($item->created_at)), $date));
                return date($dateKey, strtotime($item->created_at)) == $date;
            });
        }
        return $response;
    }


    private function mapDataByCountry($filteredItems, $timeStart = 0, $timeEnd = 0): array {
        if(empty($filteredItems)) return array();
        $collector = $response = array();

        foreach ($filteredItems as $item) {
            $country = empty($item->invoice_country) ? "No country" : $item->invoice_country;
            if(!array_key_exists($country, $collector)) $collector[$country] = array();
            $collector[$country][] = $item;
        }

        $totalInclVat = $totalExclVat = $totalVat = $totalItems = 0;
        foreach ($collector as $countryName => $items) {
            $countryInclVat = $countryExclVat = $countryVat = $countryVatPercentage = 0;
            $countryItems = count($items);

            $countryCode = "";

            foreach ($items as $item) {
                $itemExclVat = (float)$item->amount;
//                $itemInclVat = (float)$item->amount;
                $itemVat = 0;

                if(!empty($item->tax_details)) {
                    $taxPercentage = $countryVatPercentage = (float)$item->tax_details->percentage;
                    $itemInclVat = ( ($itemExclVat * (1 + $taxPercentage / 100)) );
                    $itemVat = ( $itemInclVat - $itemExclVat );
                    $countryCode = $item->tax_details->country;
                }
                else $itemInclVat = $itemExclVat;

                $countryInclVat += $itemInclVat;
                $countryExclVat += $itemExclVat;
                $countryVat += $itemVat;
            }

            $totalInclVat += $countryInclVat;
            $totalExclVat += $countryExclVat;
            $totalVat += $countryVat;
            $totalItems += $countryItems;
            $response[$countryName] = array(
                "net_sales" => round($countryInclVat, 2),
                "net_sales_excl_vat" => round($countryExclVat, 2),
                "vat" => round($countryVat, 2),
                "total_sales" => $countryItems,
                "vat_percentage" => $countryVatPercentage,
                "country_code" => $countryCode
            );
        }

        $response["total"] = array(
            "net_sales" => round($totalInclVat, 2),
            "net_sales_excl_vat" => round($totalExclVat, 2),
            "vat" => round($totalVat, 2),
            "total_sales" => $totalItems,
            "vat_percentage" => 0,
        );
        return [
            "start" => date("Y-m-d", $timeStart),
            "end" => date("Y-m-d", $timeEnd),
            "data" => $response
        ];
    }



    public function getMappedTransactions($userTransactions = false, $purchased = false) {
        if(auth()->user()->isAdmin() && !$userTransactions) $transactions = Transactions::all();
        else {
            $key = $purchased ? "user_id" : "subscribed"; //subscribed would be earnings, user_id would be spending's
            $transactions = Transactions::where($key, auth()->id())->get();
        }
        return $transactions->map(function ($transaction) {
            $newItem = $transaction;
            $newItem->tax_details = null;
            if(empty($transaction->taxes)) return $newItem;
            $newItem->tax_details = \Illuminate\Support\Facades\DB::table("tax_rates")->where("id", "=", $transaction->taxes)->first();

            return $newItem;
        });
    }




}// End Class
