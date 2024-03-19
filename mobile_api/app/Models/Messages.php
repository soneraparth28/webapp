<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Messages
 *
 * @property int $id
 * @property int $conversations_id
 * @property int $from_user_id
 * @property int $to_user_id
 * @property string $message
 * @property string $attach_file
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string $status
 * @property string $remove_from 0 Delete, 1 Active
 * @property string $file
 * @property string $original_name
 * @property string $format
 * @property string $size
 * @property string $price
 * @property string $tip
 * @property int $tip_amount
 * @property string $mode
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MediaMessages[] $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder|Messages newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Messages newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Messages query()
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereAttachFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereConversationsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereFromUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereRemoveFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereTip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereTipAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereToUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Messages extends Model
{
  protected $guarded = array();

  public function user()
  {
    return $this->belongsTo('App\Models\User', 'from_user_id')->first();
  }

  public function from()
  {
    return $this->belongsTo('App\Models\User', 'from_user_id')->first();
  }

  public function to()
  {
    return $this->belongsTo('App\Models\User', 'to_user_id')->first();
  }

  public static function markSeen()
  {
    $this->timestamps = false;
    $this->status = 'readed';
    $this->save();
  }

  public function media() {
		return $this->hasMany('App\Models\MediaMessages')->where('status', 'active')->orderBy('id','asc');
	}
}
