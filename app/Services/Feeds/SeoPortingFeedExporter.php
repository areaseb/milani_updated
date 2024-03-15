<?php

namespace App\Services\Feeds;

use Botble\Ecommerce\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class SeoPortingFeedExporter
{
    protected const DISK = 'feed';
    protected const FILENAME = 'seo_porting.csv';
    protected const SEPARATAOR = ';';

    protected const HEADER = [
        'SKU',
        'EAN',
        'Nome prodotto',
        'Descrizione',
        'Descrizione breve',
        'Meta description',
        'Meta keywords',
        'Meta title',
        'Link',
        'Categorie',
        'Breadcrumb'
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
        $parentProduct = $product->parentProduct[0];

        $categories = $parentProduct->categories->map(fn ($category) => $category->name)->implode(', ');
        $breadcrumb = $parentProduct->categories->map(fn ($category) => $category->name)->implode(' - ');

        return [
            'SKU' => $product->sku,
            'EAN' => $product->ean,
            'Nome prodotto' => $product->name,
            'Descrizione' => $product->content,
            'Descrizione breve' => $product->description,
            'Meta description' => $product->description,
            'Meta keywords' => '',
            'Meta title' => $product->name,
            'Link' => $parentProduct->url . '?s=' . $product->sku,
            'Categorie' => $categories,
            'Breadcrumb' => $breadcrumb,
        ];
    }
}
