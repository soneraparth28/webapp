<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Restrictions
 *
 * @property int $id
 * @property int $user_id
 * @property int $user_restricted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Restrictions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Restrictions newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Restrictions query()
 * @method static \Illuminate\Database\Eloquent\Builder|Restrictions whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restrictions whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restrictions whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restrictions whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restrictions whereUserRestricted($value)
 * @mixin \Eloquent
 */
class Restrictions extends Model
{
    use HasFactory;

    protected $fillable = [
      'user_id',
      'user_restricted'
    ];

    public function user()
    {
      return $this->belongsTo(User::class)->first();
    }

    public function userRestricted()
    {
      return $this->belongsTo(User::class, 'user_restricted')->first();
    }
}
