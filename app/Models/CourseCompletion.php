<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseCompletion extends Model
{

    protected $fillable = [
        'user_id',
        'updates_id',
    ];

}
