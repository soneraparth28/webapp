<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LiveComments
 *
 * @property int $id
 * @property int $user_id
 * @property int $live_streamings_id
 * @property string $comment
 * @property int $joined
 * @property string $tip
 * @property int $tip_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|LiveComments newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveComments newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveComments query()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveComments whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveComments whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveComments whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveComments whereJoined($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveComments whereLiveStreamingsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveComments whereTip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveComments whereTipAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveComments whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveComments whereUserId($value)
 * @mixin \Eloquent
 */
class LiveComments extends Model
{
    use HasFactory;

    protected $fillable = [
      'user_id',
      'live_streamings_id'
    ];

    public function user()
    {
      return $this->belongsTo(User::class)->first();
    }
}
