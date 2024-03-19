<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PaymentGateways
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $enabled
 * @property string $sandbox
 * @property string $fee
 * @property string $fee_cents
 * @property string $email
 * @property string $token
 * @property string $key
 * @property string $key_secret
 * @property string $bank_info
 * @property string $recurrent
 * @property string $logo
 * @property string $webhook_secret
 * @property string $subscription
 * @property string $ccbill_accnum
 * @property string $ccbill_subacc
 * @property string $ccbill_flexid
 * @property string $ccbill_salt
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateways newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateways newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateways query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateways whereBankInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateways whereCcbillAccnum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateways whereCcbillFlexid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateways whereCcbillSalt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateways whereCcbillSubacc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateways whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateways whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateways whereFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateways whereFeeCents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateways whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateways whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateways whereKeySecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateways whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateways whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateways whereRecurrent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateways whereSandbox($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateways whereSubscription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateways whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateways whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateways whereWebhookSecret($value)
 * @mixin \Eloquent
 */
class PaymentGateways extends Model
{
    protected $guarded = array();
    public $timestamps = false;
}
