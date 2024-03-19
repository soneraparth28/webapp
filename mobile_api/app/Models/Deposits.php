<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Deposits
 *
 * @property int $id
 * @property int $user_id
 * @property string $txn_id
 * @property int $amount
 * @property string $payment_gateway
 * @property \Illuminate\Support\Carbon $date
 * @property string $status
 * @property string $screenshot_transfer
 * @property string $percentage_applied
 * @property float $transaction_fee
 * @property string|null $taxes
 * @method static \Illuminate\Database\Eloquent\Builder|Deposits newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Deposits newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Deposits query()
 * @method static \Illuminate\Database\Eloquent\Builder|Deposits whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposits whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposits whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposits wherePaymentGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposits wherePercentageApplied($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposits whereScreenshotTransfer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposits whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposits whereTaxes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposits whereTransactionFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposits whereTxnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposits whereUserId($value)
 * @mixin \Eloquent
 */
class Deposits extends Model {

	protected $guarded = array();
	const CREATED_AT = 'date';
	const UPDATED_AT = null;

	public function user() {
        return $this->belongsTo('App\Models\User')->first();
    }

}
