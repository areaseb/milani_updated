<?php

namespace App\Console\Commands;

use App\Services\StockUpdaterService;
use Illuminate\Console\Command;

class StockUpdateCommand extends Command
{
    public const STOCK_FILE_URL = 'https://b2b.magazzinicosma.it/storage/exports/giacenze.csv';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update products stock';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(StockUpdaterService $stockUpdaterService)
    {
        // Let's download the CSV
        $content = $this->downloadStockFile();
        $stockUpdaterService->update($content);

        $stockUpdaterService->updateTempesta();

        return Command::SUCCESS;
    }

    private function downloadStockFile(): string
    {
        $content = file_get_contents(self::STOCK_FILE_URL);
        return $content;
    }
}
