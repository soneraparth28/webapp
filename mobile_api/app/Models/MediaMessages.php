<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MediaMessages
 *
 * @property int $id
 * @property int $messages_id
 * @property string $type
 * @property string $file
 * @property string|null $width
 * @property string|null $height
 * @property string|null $video_poster
 * @property string $file_name
 * @property string $file_size
 * @property string $token
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $encoded
 * @property-read \App\Models\Messages|null $messages
 * @method static \Illuminate\Database\Eloquent\Builder|MediaMessages newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaMessages newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaMessages query()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaMessages whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaMessages whereEncoded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaMessages whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaMessages whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaMessages whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaMessages whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaMessages whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaMessages whereMessagesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaMessages whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaMessages whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaMessages whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaMessages whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaMessages whereVideoPoster($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaMessages whereWidth($value)
 * @mixin \Eloquent
 */
class MediaMessages extends Model
{
  protected $fillable = [
    'messages_id',
    'type',
    'file',
    'width',
    'height',
    'video_poster',
    'file_name',
    'file_size',
    'token',
    'status',
    'created_at'
  ];

  public function messages() {
        return $this->belongsTo('App\Models\Messages');
    }
}
