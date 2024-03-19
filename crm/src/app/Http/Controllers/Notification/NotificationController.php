<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        return view('application.views.notification.index');
    }

    public function list()
    {
        return view('brands.notification.index');
    }
}
