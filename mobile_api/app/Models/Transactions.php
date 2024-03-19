<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Transactions
 *
 * @property int $id
 * @property string $txn_id
 * @property int $user_id
 * @property int $subscriptions_id
 * @property int $subscribed
 * @property \Illuminate\Support\Carbon $created_at
 * @property string $earning_net_user
 * @property string $earning_net_admin
 * @property string $payment_gateway
 * @property string $approved 0 Pending, 1 Success, 2 Canceled
 * @property float $amount
 * @property string $type
 * @property string $percentage_applied
 * @property int $referred_commission
 * @property string|null $taxes
 * @property int $direct_payment
 * @method static \Illuminate\Database\Eloquent\Builder|Transactions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transactions newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transactions query()
 * @method static \Illuminate\Database\Eloquent\Builder|Transactions whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transactions whereApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transactions whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transactions whereDirectPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transactions whereEarningNetAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transactions whereEarningNetUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transactions whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transactions wherePaymentGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transactions wherePercentageApplied($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transactions whereReferredCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transactions whereSubscribed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transactions whereSubscriptionsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transactions whereTaxes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transactions whereTxnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transactions whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transactions whereUserId($value)
 * @mixin \Eloquent
 */
class Transactions extends Model
{
    const UPDATED_AT = null;

    public function user() {
          return $this->belongsTo('App\Models\User')->first();
      }

		public function subscribed() {
	        return $this->belongsTo('App\Models\User', 'subscribed')->first();
	    }

    public function subscription() {
	        return $this->belongsTo('App\Models\Subscriptions', 'subscriptions_id')->first();
	    }
}
