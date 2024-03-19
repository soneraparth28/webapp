<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LiveOnlineUsers
 *
 * @property int $id
 * @property int $user_id
 * @property int $live_streamings_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|LiveOnlineUsers newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveOnlineUsers newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveOnlineUsers query()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveOnlineUsers whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveOnlineUsers whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveOnlineUsers whereLiveStreamingsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveOnlineUsers whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveOnlineUsers whereUserId($value)
 * @mixin \Eloquent
 */
class LiveOnlineUsers extends Model
{
  use HasFactory;

  protected $fillable = [
    'user_id',
    'live_streamings_id'
  ];
}
