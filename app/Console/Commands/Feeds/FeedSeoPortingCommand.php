<?php

namespace App\Console\Commands\Feeds;

use App\Services\Feed\GSheet;
use App\Services\Feeds\SeoPortingFeedExporter;
use Illuminate\Console\Command;

class FeedSeoPortingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feed:seoporting:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Seo Porting feed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(SeoPortingFeedExporter $exporter)
    {
        $exporter->export();

        return Command::SUCCESS;
    }
}
