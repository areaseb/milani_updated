<?php

namespace App\Services;

use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Repositories\Eloquent\ProductRepository;
use League\Csv\Reader;

class StockUpdaterService
{
    protected const SKU_KEY = 'SKU';
    protected const QUANTITY_KEY = 'qta';

    public function update($content)
    {
        $reader = Reader::createFromString($content)
            ->setHeaderOffset(0)
            ->setDelimiter(';')
            ->setEnclosure('"')
            ->setEscape('\\');

        $records = $reader->getRecords();
        foreach ($records as $record) {
            $this->updateProductStock($record);
        }
    }

    public function updateTempesta()
    {
        $product = Product::where('sku_set', 'tempesta')->first();
        if (!$product) {
            return;
        }

        $product->quantity = 99;
        $product->save();
    }

    protected function updateProductStock($record)
    {
        $product = Product::where('codice_cosma', $record[self::SKU_KEY])->first();
        if (!$product) {
            return;
        }

        $product->quantity = (int) $record[self::QUANTITY_KEY];
        $product->save();
    }
}
