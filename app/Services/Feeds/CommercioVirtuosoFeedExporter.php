<?php

namespace App\Services\Feeds;

use Botble\Ecommerce\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class CommercioVirtuosoFeedExporter
{
    protected const DISK = 'feed';
    protected const FILENAME = 'commerciovirtuoso.csv';
    protected const SEPARATAOR = ',';

    protected const HEADER = [
        'Unique Id',
        'Variant Qty',
        'Variant Price'
    ];

    protected const CHUNK = 100;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function export()
    {
        $this->deleteOldCatalogFile();

        $this->exportHeader();

        $this->exportProductRows();
    }

    protected function deleteOldCatalogFile()
    {
        Storage::disk(static::DISK)->delete(static::FILENAME);
    }

    protected function exportHeader()
    {
        $header = implode(self::SEPARATAOR, static::HEADER);

        Storage::disk(static::DISK)->put(self::FILENAME, $header);
    }

    protected function exportProductRows()
    {
        Product::where('is_variation', true)
            ->chunk(self::CHUNK, fn ($products) => $products->each(fn ($product) => $this->exportProductRow($product)));
    }

    protected function exportProductRow($product)
    {
        $row = $this->generateProductRow($product);
        if (empty($row)) {
            return;
        }

        $row = implode(self::SEPARATAOR, array_values($row));

        Storage::disk(static::DISK)->append(self::FILENAME, $row);
    }

    protected function generateProductRow($product)
    {
        $price = $product->price;
        if (now()->isBetween(Carbon::parse($product->start_date), Carbon::parse($product->end_date))) {
            $price = $product->sale_price;
        }

        return [
            'Unique Id' => $product->sku,
            'Variant Qty' => $product->quantity,
            'Variant Price' => number_format($price * 1.22, 2, '.', '')
        ];
    }
}
