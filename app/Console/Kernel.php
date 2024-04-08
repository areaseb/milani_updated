<?php

namespace App\Console;

use App\Console\Commands\BeezupStartAutoImportCommand;
use App\Console\Commands\ExportOrderCommand;
use App\Jobs\BeezupImportOrdersJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(BeezupStartAutoImportCommand::class)->dailyAt('01:00');
        $schedule->job(app(BeezupImportOrdersJob::class))->everyTenMinutes();
        // $schedule->command(ExportOrderCommand::class)->everyTenMinutes();

        $schedule->command(StockUpdateCommand::class)->dailyAt('00:00');
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
