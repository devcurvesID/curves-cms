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
        Commands\Flag::class,
        Commands\StaffPoint::class,
        Commands\BonusTargetPoint::class,
        Commands\FlagPoint::class,
        Commands\MonthlyEndingBalance::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('command:flag')->dailyAt('04:00')->timezone('Asia/Jakarta');
        $schedule->command('command:bonus_target_point')->dailyAt('06:00')->timezone('Asia/Jakarta');
        $schedule->command('command:flag_point')->monthlyOn(1, '06:30')->timezone('Asia/Jakarta');
        $schedule->command('command:staff_point')->monthlyOn(1, '07:30')->timezone('Asia/Jakarta');
        $schedule->command('command:meb')->monthlyOn(1, '07:30')->timezone('Asia/Jakarta');
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
