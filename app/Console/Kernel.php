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

        // caeru_db
        Commands\MigrateCaeruDatabase::class,
        Commands\SeedCaeruDatabase::class,
        Commands\ResetCaeruDatabase::class,
        Commands\RefreshCaeruDatabase::class,

        // caeru_error
        Commands\CheckForgotErrors::class,
        Commands\CheckHaveScheduleButOfflineError::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Check the Forgot end_work/return errors for every minute
        $schedule->command('caeru_error:check-forgot-errors')->everyMinute();

        // Check the HAVE_SCHEDULE_BUT_OFFLINE every day at 00:05:00
        $schedule->command('caeru_error:check-have-schedule-but-offline')->dailyAt('00:05');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
