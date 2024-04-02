<?php

namespace App\Services;

use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Repositories\Eloquent\ProductRepository;
use League\Csv\Reader;

class StockUpdaterService
{
    protected const SKU_KEY = 'SKU';
    protected const QUANTITY_KEY = 'giacenza';
    protected const IMPEGNATO_KEY = 'impegnato';
    protected const DATA_ARRIVO_KEY = 'data_arrivo';

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
        $products = Product::where('sku_set', 'tempesta')->get();
        if (!$products->isEmpty()) {
            return;
        }

        foreach ($products as $product) {
            $product->quantity = 0;
            $product->save();
        };
    }

    protected function updateProductStock($record)
    {
        $product = Product::where('codice_cosma', $record[self::SKU_KEY])->first();
        if (!$product) {
            return;
        }

        $product->quantity = (int) $record[self::QUANTITY_KEY] - (int) $record[self::IMPEGNATO_KEY];
        $product->data_arrivo = $record[self::DATA_ARRIVO_KEY];
        $product->save();
    }
}
