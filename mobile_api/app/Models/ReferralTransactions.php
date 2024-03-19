<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ReferralTransactions
 *
 * @property int $id
 * @property int|null $transactions_id
 * @property int $referrals_id
 * @property int $user_id
 * @property int $referred_by
 * @property float $earnings
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralTransactions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralTransactions newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralTransactions query()
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralTransactions whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralTransactions whereEarnings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralTransactions whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralTransactions whereReferralsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralTransactions whereReferredBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralTransactions whereTransactionsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralTransactions whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralTransactions whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralTransactions whereUserId($value)
 * @mixin \Eloquent
 */
class ReferralTransactions extends Model
{
  protected $fillable = [
    'transactions_id',
    'referrals_id',
    'user_id',
    'referred_by',
    'earnings',
    'type',
    'created_at'
  ];

    public function user()
    {
      return $this->belongsTo(User::class)->first();
    }

		public function referredBy()
    {
      return $this->belongsTo(User::class, 'referred_by')->first();
    }

}
