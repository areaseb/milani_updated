<?php

namespace App\Console\Commands;

use App\Services\BeezupClient;
use Illuminate\Console\Command;

class BeezupStartAutoImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'beezup:autoimport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start autoimport from Beezup';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(BeezupClient $client)
    {
        $client->autoimport();

        return Command::SUCCESS;
    }
}
