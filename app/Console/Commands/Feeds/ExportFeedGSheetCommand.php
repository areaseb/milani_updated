<?php

namespace App\Console\Commands\Feeds;

use App\Services\Feeds\GSheetFeedExporter;
use Illuminate\Console\Command;

class ExportFeedGSheetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feed:gsheet:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export GSheet feed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(GSheetFeedExporter $exporter)
    {
        $exporter->export();

        return Command::SUCCESS;
    }
}
