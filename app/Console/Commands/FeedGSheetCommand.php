<?php

namespace App\Console\Commands;

use App\Services\Feed\GSheet;
use Illuminate\Console\Command;

class FeedGSheetCommand extends Command
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
    public function handle(GSheet $gsheet)
    {
        $gsheet->export();

        return Command::SUCCESS;
    }
}
