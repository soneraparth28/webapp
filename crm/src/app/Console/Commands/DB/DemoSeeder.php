<?php

namespace App\Console\Commands\DB;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DemoSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:demo {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Working....');
        Artisan::call('db:seed', ['--class' => '\Database\Seeders\DBDemoSeeder', '--force' => true]);
        $this->info('Done');
    }
}
