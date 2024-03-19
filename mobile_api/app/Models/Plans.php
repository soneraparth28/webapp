<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Plans
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $price
 * @property string $interval
 * @property string $paystack
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Plans newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plans newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plans query()
 * @method static \Illuminate\Database\Eloquent\Builder|Plans whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plans whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plans whereInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plans whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plans wherePaystack($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plans wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plans whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plans whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plans whereUserId($value)
 * @mixin \Eloquent
 */
class Plans extends Model
{
    use HasFactory;

    protected $fillable = [
      'user_id',
      'name',
      'price',
      'interval',
      'paystack',
      'status',
      'created_at'
    ];

    public function user() {
  		return $this->belongsTo(User::class)->first();
  	}
}
