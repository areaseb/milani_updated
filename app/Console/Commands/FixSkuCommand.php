<?php

namespace App\Console\Commands;

use App\Services\StockUpdaterService;
use Botble\Ecommerce\Models\Product;
use Illuminate\Console\Command;

class FixSkuCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sku:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $columns_to_fix = [
            'sku',
            'sku_parent',
            'sku_parentela',
            'sku_parentela_amz',
        ];

        $sku_to_fix = [
            '08215350',
            '08010986',
            '08017473',
            '08312318',
            '08010962',
            '08006576',
            '08011570',
            '08013642',
            '08006590',
            '08311465',
            '08311472',
            '08312813',
            '08312806',
            '08015295',
            '08014489',
            '08015981',
            '08015264',
            '08214742',
            '08014458',
            '08311533',
            '08312097',
            '08016186',
            '08015745',
            '08016193',
            '08312103',
            '08312110',
            '08311304',
            '08016377',
            '08014762',
            '08295666',
            '08295710',
            '08295659',
            '08014359',
            '08014854',
            '08295703',
            '08295680',
            '08006408',
            '08014380',
            '08014335',
            '08015639',
            '08011259',
            '08215152',
            '08016162',
            '08215381',
            '08215138',
            '08295598',
            '08295673',
            '08013901',
            '08011549',
            '08309905',
            '08215367',
            '08312653',
            '08312752',
            '08311908',
            '08010825',
            '08215404',
            '08215343',
            '08215176',
            '08016070',
        ];

        foreach($sku_to_fix as $sku) {
            if($sku[0] != '0')
                continue;   

            $sku_old = substr($sku, 1);

            foreach($columns_to_fix as $column) {
                Product::where($column, $sku_old)->update([$column => $sku]);    
            }
        }

        return Command::SUCCESS;
    }
}
