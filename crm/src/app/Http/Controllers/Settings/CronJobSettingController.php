<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CronJobSettingController extends Controller
{
    public function index()
    {
        $php_path = exec("which php");
        $commands = [
            base_path() . '/artisan schedule:run >> /dev/null 2>&1',
            base_path() . '/artisan queue:work --sansdaemon --tries=3 --queue=high',
            base_path() . '/artisan queue:work --sansdaemon --tries=3 --queue=default'
        ];
        $cpanel_commands = [
            exec("which php") . ' ' . base_path() . '/artisan schedule:run >> /dev/null 2>&1',
            exec("which php") . ' ' . base_path() . '/artisan queue:work --sansdaemon --tries=3 --queue=high',
            exec("which php") . ' ' . base_path() . '/artisan queue:work --sansdaemon --tries=3 --queue=default',
        ];

        return [
            'php_path' => $php_path,
            'cpanel_commands' => $cpanel_commands,
            'commands' => $commands,
        ];
    }
}
