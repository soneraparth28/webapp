<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLesson extends Model
{

    protected $fillable = [
        'updates_id',
        'user_id',
        'token_id',
        'module_id',
        'lesson_id',
        'media_id',
        'module_title',
        'duration',
        'lesson_title',
        'lesson_description',
        'status',
        "lesson_index",
        "lesson_file"
    ];

    public function user() {
        return $this->belongsTo('App\Models\User')->first();
    }

    public function updates() {
        return $this->belongsTo('App\Models\Updates');
    }
    public function media() {
        return $this->belongsTo('App\Models\Media');
    }
}
