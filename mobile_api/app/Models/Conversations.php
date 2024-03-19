<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Conversations
 *
 * @property int $id
 * @property int $user_1
 * @property int $user_2
 * @property string $created_at
 * @property string $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Messages[] $messages
 * @property-read int|null $messages_count
 * @method static \Illuminate\Database\Eloquent\Builder|Conversations newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Conversations newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Conversations query()
 * @method static \Illuminate\Database\Eloquent\Builder|Conversations whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversations whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversations whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversations whereUser1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversations whereUser2($value)
 * @mixin \Eloquent
 */
class Conversations extends Model
{
  protected $guarded = array();
  public $timestamps = false;

  public function user()
  {
        return $this->belongsTo('App\Models\User')->first();
    }

  public function last()
  {
      return $this->hasMany('App\Models\Messages','conversations_id')
          ->where('messages.mode', 'active')
          ->orderBy('messages.updated_at', 'DESC')
          ->take(1)
          ->first();
  }

  public function messages()
  {
      return $this->hasMany('App\Models\Messages','conversations_id')
        ->where('messages.mode', 'active')
        ->orderBy('messages.updated_at', 'DESC');
    }

  public function from()
  {
        return $this->belongsTo('App\Models\User', 'from_user_id')->first();
    }

  public function to()
  {
        return $this->belongsTo('App\Models\User', 'to_user_id')->first();
    }
}
