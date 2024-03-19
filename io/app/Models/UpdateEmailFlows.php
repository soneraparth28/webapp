<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpdateEmailFlows extends Model
{
    protected $fillable = [
        'title',
        'updates_id',
        'user_id',
        'template_node',
        'send_days_after_unlock',
    ];

    public function user() {
        return $this->belongsTo('App\Models\User',"user_id")->first();
    }

    public function updates() {
        return $this->belongsTo('App\Models\Updates', "updates_id");
    }

}
