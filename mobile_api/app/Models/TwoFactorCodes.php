<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TwoFactorCodes
 *
 * @property int $id
 * @property int $user_id
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorCodes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorCodes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorCodes query()
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorCodes whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorCodes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorCodes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorCodes whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoFactorCodes whereUserId($value)
 * @mixin \Eloquent
 */
class TwoFactorCodes extends Model
{
  protected $fillable = [
    'user_id',
    'code'
  ];
}
