<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaView extends Model
{

    protected $fillable = [
        'updates_id',
        'media_id',
        'user_id',
        'percentage_watched',
        'is_embed',
        'updated_at'
    ];

}
