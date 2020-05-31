<?php

namespace App\Console;

use App\Collector;
use App\Jobs\ProcessTask;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $collectors = Collector::get();
        foreach($collectors as $collector) {

            switch($collector->period) {
                case 'hourly':
                                $schedule->job(new ProcessTask($collector))->hourly();
                                break;
                case 'daily':
                                $schedule->job(new ProcessTask($collector))->daily();
                                break;
                case 'weekly':
                                $schedule->job(new ProcessTask($collector))->weeklyOn(1, '8:00');
                                break;
                case 'monthly':
                                $schedule->job(new ProcessTask($collector))->monthly();
                                break;
            }
        }
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
