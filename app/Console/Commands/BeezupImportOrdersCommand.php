<?php

namespace App\Console\Commands;

use App\Jobs\BeezupImportOrdersJob;
use Exception;
use Illuminate\Console\Command;

class BeezupImportOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'beezup:order:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import orders from Beezup';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            dispatch_sync(app(BeezupImportOrdersJob::class));

            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
