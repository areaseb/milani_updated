<?php

namespace App\Console\Commands\Feeds;

use App\Services\Feeds\PrezzoStopFeedExporter;
use Illuminate\Console\Command;

class ExportFeedPrezzoStopCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feed:prezzostop:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export PrezzoStop feed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(PrezzoStopFeedExporter $exporter)
    {
        $exporter->export();

        return Command::SUCCESS;
    }
}
