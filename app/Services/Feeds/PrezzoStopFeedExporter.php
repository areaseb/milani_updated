<?php

namespace App\Services\Feeds;

use App\XML\SimpleXMLExtended;
use Botble\Ecommerce\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PrezzoStopFeedExporter
{
    protected const DISK = 'feed';
    protected const FILENAME = 'prezzostop.xml';

    protected const CHUNK = 100;

    /**
     * xml
     */
    protected $xml;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function export()
    {
        $this->deleteOldCatalogFile();

        $this->createCatalogFile();
        $this->exportProductRows();

        $this->saveCatalogFile();
    }

    protected function deleteOldCatalogFile()
    {
        Storage::disk(static::DISK)->delete(static::FILENAME);
    }

    protected function createCatalogFile()
    {
       $this->xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . '<products></products>');
    }

    protected function saveCatalogFile()
    {
        Storage::disk(static::DISK)->put(static::FILENAME, $this->xml->asXML());
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

        $xmlProduct = $this->xml->addChild('product');
        foreach ($row as $key => $value) {
            if (is_string($value)) {
                $prop = $xmlProduct->addChild($key);
                $prop->addCData($value);
            } else {
                $xmlProduct->addChild($key, $value);
            }
        }
    }

    protected function generateProductRow($product)
    {
        $parentProduct = $product->parentProduct[0];

        $categories = $parentProduct->categories->map(fn ($category) => $category->name)->implode(';');

        $price = $product->price;
        if (now()->isBetween(Carbon::parse($product->start_date), Carbon::parse($product->end_date))) {
            $price = $product->sale_price;
        }

        $image = $product->images[0] ?? null;
        if ($image) {
            $image = asset('storage/' . $image);
        }

        return [
            'ID' => $product->id,
            'PRODOTTONO' => $product->name,
            'MEDESCRIZIONE' => $product->content,
            'CATEGORIA' => $categories,
            'PREZZO' => number_format($price, 2, '.', ''),
            'LINK' => $parentProduct->url . '?s=' . $product->sku,
            'IMMAGINE' => $image ?? '',
            'MPN' => $product->id,
            'EAN' => $product->ean,
        ];
    }
}
