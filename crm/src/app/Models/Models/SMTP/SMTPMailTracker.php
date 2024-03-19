<?php

namespace App\Models\Models\SMTP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SMTPMailTracker extends Model
{
    protected $table = 'smtp_mail_trackers';

    protected $fillable = [
        'username', 'touched_at'
    ];

}
