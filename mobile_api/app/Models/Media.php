<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Media
 *
 * @property int $id
 * @property int $updates_id
 * @property int $user_id
 * @property string $type
 * @property string $image
 * @property string|null $width
 * @property string|null $height
 * @property string $img_type
 * @property string $video
 * @property string $encoded
 * @property string|null $video_poster
 * @property string $video_embed
 * @property string $music
 * @property string $file
 * @property string $file_name
 * @property string $file_size
 * @property string $token
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Updates|null $updates
 * @method static \Illuminate\Database\Eloquent\Builder|Media newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Media newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Media query()
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereEncoded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereImgType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereMusic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereUpdatesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereVideo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereVideoEmbed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereVideoPoster($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereWidth($value)
 * @mixin \Eloquent
 */
class Media extends Model
{
  protected $fillable = [
    'updates_id',
    'user_id',
    'type',
    'image',
    'width',
    'height',
    'video',
    'video_poster',
    'video_embed',
    'music',
    'file',
    'file_name',
    'file_size',
    'img_type',
    'token',
    'status',
    'created_at'
  ];

  public function user() {
        return $this->belongsTo('App\Models\User')->first();
    }

  public function updates() {
        return $this->belongsTo('App\Models\Updates');
    }

}
