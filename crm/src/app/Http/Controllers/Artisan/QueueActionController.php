<?php

namespace App\Http\Controllers\Artisan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class QueueActionController extends Controller
{
    public function run($queue)
    {
        Artisan::call('queue:work', [
            '--queue' => $queue,
            '--tries' => 3,
            '--sansdaemon' => true
        ]);

        return true;
    }
}
