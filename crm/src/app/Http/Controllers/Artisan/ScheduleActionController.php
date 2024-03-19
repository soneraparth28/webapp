<?php

namespace App\Http\Controllers\Artisan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class ScheduleActionController extends Controller
{

    public function run()
    {
        Artisan::call('schedule:run');
        return true;
    }
}
