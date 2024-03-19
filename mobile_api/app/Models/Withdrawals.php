<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Withdrawals
 *
 * @property int $id
 * @property int $user_id
 * @property string $status
 * @property string $amount
 * @property \Illuminate\Support\Carbon $date
 * @property string $gateway
 * @property string $account
 * @property string $date_paid
 * @property string $txn_id
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawals newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawals newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawals query()
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawals whereAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawals whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawals whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawals whereDatePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawals whereGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawals whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawals whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawals whereTxnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawals whereUserId($value)
 * @mixin \Eloquent
 */
class Withdrawals extends Model {

	protected $guarded = array();
	const CREATED_AT = 'date';
	const UPDATED_AT = null;

	public function user() {
        return $this->belongsTo('App\Models\User')->first();
    }
}
