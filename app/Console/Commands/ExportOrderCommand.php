<?php

namespace App\Console\Commands;

use App\Services\OrderExporterService;
use Illuminate\Console\Command;

class ExportOrderCommand extends Command
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
    protected $description = 'Export orders to CRM';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(OrderExporterService $exporter)
    {
        $exporter->export();

        return Command::SUCCESS;
    }
}
