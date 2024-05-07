<?php

namespace App\Console\Commands;

use App\Services\OrderExporterMicheleService;
use Illuminate\Console\Command;

class ExportOrderMicheleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:michele:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export orders to Michele';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(OrderExporterMicheleService $exporter)
    {
        $exporter->export();

        return Command::SUCCESS;
    }
}
