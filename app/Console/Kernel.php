<?php

namespace App\Console;

use App\Console\Commands\BeezupStartAutoImportCommand;
use App\Console\Commands\ExportOrderCommand;
use App\Console\Commands\ExportOrderMicheleCommand;
use App\Console\Commands\StockUpdateCommand;
use App\Console\Commands\Feeds\ExportFeedBobGCommand;
use App\Console\Commands\Feeds\ExportFeedCommercioVirtuosoCommand;
use App\Console\Commands\Feeds\ExportFeedGSheetCommand;
use App\Console\Commands\Feeds\ExportFeedKijijiCommand;
use App\Console\Commands\Feeds\ExportFeedPrezzoStopCommand;
use App\Console\Commands\Feeds\ExportFeedSeoPortingCommand;
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
        $schedule->command(BeezupStartAutoImportCommand::class)->everyTwoHours();		//dailyAt('01:00');
        $schedule->job(app(BeezupImportOrdersJob::class))->everyTenMinutes();
        $schedule->command(ExportOrderCommand::class)->everyTenMinutes();
<<<<<<< HEAD
        $schedule->command(ExportOrderMicheleCommand::class)->everyTenMinutes();
=======
>>>>>>> dev/load-more

        $schedule->command(StockUpdateCommand::class)->everyThirtyMinutes();		//dailyAt('00:00');
        
        // FEEDS
        
        $schedule->command(ExportFeedBobGCommand::class)->hourly();
        $schedule->command(ExportFeedCommercioVirtuosoCommand::class)->hourly();
        $schedule->command(ExportFeedGSheetCommand::class)->hourly();
        $schedule->command(ExportFeedKijijiCommand::class)->hourly();
        $schedule->command(ExportFeedPrezzoStopCommand::class)->hourly();
        $schedule->command(ExportFeedSeoPortingCommand::class)->hourly();
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
