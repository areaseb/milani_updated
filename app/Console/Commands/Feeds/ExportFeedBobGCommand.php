<?php

namespace App\Console\Commands\Feeds;

use App\Services\Feeds\BobGFeedExporter;
use Illuminate\Console\Command;

class ExportFeedBobGCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feed:bobg:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Bob G feed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(BobGFeedExporter $exporter)
    {
        $exporter->export();

        return Command::SUCCESS;
    }
}
