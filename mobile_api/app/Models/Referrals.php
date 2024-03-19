<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Referrals
 *
 * @property int $id
 * @property int $user_id
 * @property int $referred_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Referrals newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Referrals newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Referrals query()
 * @method static \Illuminate\Database\Eloquent\Builder|Referrals whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Referrals whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Referrals whereReferredBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Referrals whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Referrals whereUserId($value)
 * @mixin \Eloquent
 */
class Referrals extends Model
{
  protected $fillable = [
    'user_id',
    'referred_by'
  ];

  public function user()
  {
    return $this->belongsTo(User::class)->first();
  }

  public function referredBy()
  {
    return $this->belongsTo(User::class, 'referred_by')->first();
  }

  public function earnings()
  {
    return $this->hasMany(ReferralTransactions::class)->sum('earnings');
  }
}
