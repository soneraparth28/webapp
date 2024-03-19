<?php

namespace App\Console;

use App\Console\Commands\CampaignMailScheduler;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Class Kernel.
 */
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CampaignMailScheduler::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('campaign:schedule')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
