<?php

namespace App\Services;

use Botble\Ecommerce\Models\Product;
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
        if (!$products) {
            return;
        }

        foreach ($products as $product) {
            $product->quantity = 99;
            $product->save();
        }
    }

    protected function updateProductStock($record)
    {
        $product = Product::where('codice_cosma', $record[self::SKU_KEY])
            ->where('is_variation', true)
            ->first();

        if (!$product) {
            return;
        }

        $quantity = (int) $record[self::QUANTITY_KEY] - (int) $record[self::IMPEGNATO_KEY];

        $product->quantity = $quantity > 0 ? $quantity : 0;
        $product->data_arrivo = $record[self::DATA_ARRIVO_KEY];
        $product->save();
    }

    public function updateSets()
    {
        $products = Product::where('is_variation', true)
            ->where(function ($query) {
                $query->where('sku_set', '!=', 'tempesta')
                    ->whereNotNull('sku_set');
            })
            ->get();

        foreach ($products as $product) {
            $skuSet = $product->sku_set;
            $skuSetExploded = explode(',', $skuSet); // WI806915-TAUP:1,WI806917-TAUP:4

            $productQuantity = -1;
            foreach ($skuSetExploded as $skuSetItem) {
                $skuSetItemExploded = explode(':', $skuSetItem);
                if (count($skuSetItemExploded) !== 2) {
                    $productQuantity = 0;
                    break;
                }

                $sku = $skuSetItemExploded[0];
                $skuQuantity = $skuSetItemExploded[1];

                $productSet = Product::where('codice_cosma', $sku)
                    ->where('is_variation', true)
                    ->first();

                if (!$productSet) {
                    $productQuantity = 0;
                    break;
                }

                $minQuantity = floor(((int) $productSet->quantity / (int) $skuQuantity));

                if ($productQuantity === -1 || $minQuantity < $productQuantity) {
                    $productQuantity = $minQuantity;
                }
            }

            $product->quantity = $productQuantity > 0 ? $productQuantity : 0;
            $product->save();
        }
    }
}
