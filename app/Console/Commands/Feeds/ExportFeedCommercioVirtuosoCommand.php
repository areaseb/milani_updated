<?php

namespace App\Console\Commands\Feeds;

use App\Services\Feeds\CommercioVirtuosoFeedExporter;
use Illuminate\Console\Command;

class ExportFeedCommercioVirtuosoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feed:commerciocirtuoso:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Commercio Virtuoso feed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(CommercioVirtuosoFeedExporter $exporter)
    {
        $exporter->export();

        return Command::SUCCESS;
    }
}
