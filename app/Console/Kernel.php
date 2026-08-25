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
        $schedule->command('sgsst:notificar-actividades')->dailyAt('09:00');
        $schedule->command('notificar:plan-trabajo')->dailyAt('08:00');
        $schedule->command('sgsst:alertas-mes')->monthlyOn(1, '08:00');
        $schedule->command('sgsst:check-committee-operations')->dailyAt('07:30')->withoutOverlapping();
        $schedule->command('sgsst:close-expired-attendance')->everyFiveMinutes()->withoutOverlapping();
        $schedule->command('sgsst:training-reminders')->everyFiveMinutes()->withoutOverlapping();
        $schedule->command('sgsst:check-training-alerts')->dailyAt('06:30')->withoutOverlapping();
        $schedule->command('transport:check-scheduling')->everyThirtyMinutes()->withoutOverlapping();
        $schedule->command('transport:check-control')->everyThirtyMinutes()->withoutOverlapping();
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
