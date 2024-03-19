<?php

namespace App\Http\Controllers;

use App\Helpers\Traits\SetIiiTrait;
use Gainhq\Installer\App\Managers\StorageManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class InstallDemoDataController extends Controller
{
    use SetIiiTrait;

    public function run()
    {
        if (env('INSTALL_DEMO_DATA')) {
            $this->setMemoryLimit('500M');
            $this->setExecutionTime(500);
            Artisan::call('clear-compiled');
            Artisan::call('view:clear');

            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            config()->set('database.connections.mysql.strict', false);
            Artisan::call('migrate:fresh --force');

            Artisan::call('db:demo');
            config()->set('database.connections.mysql.strict', true);
            Artisan::call('queue:restart');

            resolve(StorageManager::class)->link();
        }

        return true;
    }
}
