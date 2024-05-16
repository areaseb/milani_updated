<?php

namespace App\Console\Commands;

use App\Services\OrderExporterService;
use Illuminate\Console\Command;

class OrderExportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export orders to csv';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(OrderExporterService $orderExporterService)
    {
        try {
            $orderExporterService->export();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
