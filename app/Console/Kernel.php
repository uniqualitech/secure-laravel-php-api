<?php

namespace App\Console;

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
        // Commands\Inspire::class,
        '\App\Console\Commands\Notification',
        '\App\Console\Commands\Reminder'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    // protected function schedule(Schedule $schedule)
    // {
    //     $schedule->call('inspire')
    //              ->hourly();
    // }

    protected function schedule(Schedule $schedule)
    {   
        $schedule->call('App\Http\Controllers\notification@notification')->everyMinute();
        $schedule->call('App\Http\Controllers\notification@reminder')->everyMinute();
    }


}
