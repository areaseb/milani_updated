<?php

namespace App\Console\Commands\Feeds;

use App\Services\Feeds\KijijiFeedExporter;
use Illuminate\Console\Command;

class ExportFeedKijijiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feed:kijiji:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Kijiji feed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(KijijiFeedExporter $exporter)
    {
        $exporter->export();

        return Command::SUCCESS;
    }
}
