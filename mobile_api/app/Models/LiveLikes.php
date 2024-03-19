<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LiveLikes
 *
 * @property int $id
 * @property int $user_id
 * @property int $live_streamings_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|LiveLikes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveLikes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveLikes query()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveLikes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveLikes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveLikes whereLiveStreamingsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveLikes whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveLikes whereUserId($value)
 * @mixin \Eloquent
 */
class LiveLikes extends Model
{
  protected $guarded = array();
  protected $fillable = [
    'user_id',
    'live_streamings_id'
  ];
  
    use HasFactory;

    public function user()
    {
      return $this->belongsTo(User::class)->first();
    }
}
