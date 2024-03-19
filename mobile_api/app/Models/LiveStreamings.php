<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LiveStreamings
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $channel
 * @property int $price
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $availability
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\LiveComments[] $comments
 * @property-read int|null $comments_count
 * @property-read mixed $time_elapsed
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\LiveLikes[] $likes
 * @property-read int|null $likes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\LiveOnlineUsers[] $onlineUsers
 * @property-read int|null $online_users_count
 * @method static \Illuminate\Database\Eloquent\Builder|LiveStreamings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveStreamings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveStreamings query()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveStreamings whereAvailability($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveStreamings whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveStreamings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveStreamings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveStreamings whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveStreamings wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveStreamings whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveStreamings whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveStreamings whereUserId($value)
 * @mixin \Eloquent
 */
class LiveStreamings extends Model
{
    use HasFactory;

    public function user()
  	{
  		return $this->belongsTo(User::class)->first();
  	}

    public function comments()
    {
      return $this->hasMany(LiveComments::class);
    }

    public function likes()
    {
      return $this->hasMany(LiveLikes::class);
    }

    public function onlineUsers()
    {
      return $this->hasMany(LiveOnlineUsers::class)
        ->where('updated_at', '>', now()->subSeconds(10));
    }

    public function getTimeElapsedAttribute()
    {
      $created_at = $this->created_at;
      $updated_at = $this->updated_at;

      return $updated_at->diffInMinutes($created_at);
    }
}
