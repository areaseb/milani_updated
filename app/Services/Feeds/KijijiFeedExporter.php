<?php

namespace App\Services\Feeds;

use App\XML\SimpleXMLExtended;
use Botble\Ecommerce\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class KijijiFeedExporter
{
    protected const DISK = 'feed';
    protected const FILENAME = 'kijiji.xml';

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
       $this->xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . '<kijijipartners_xml></kijijipartners_xml>');
    }

    protected function saveCatalogFile()
    {
        Storage::disk(static::DISK)->put(static::FILENAME, $this->xml->asXML());
    }

    protected function exportProductRows()
    {
        $annunciXML = $this->xml->addChild('annunci');
        Product::where('is_variation', true)
            ->chunk(self::CHUNK, fn ($products) => $products->each(fn ($product) => $this->exportProductRow($product, $annunciXML)));
    }

    protected function exportProductRow($product, $parentNode)
    {
        $row = $this->generateProductRow($product);
        if (empty($row)) {
            return;
        }

        $xmlProduct = $parentNode->addChild('annuncio');
        return $this->generateXML($row, $xmlProduct);
    }

    protected function generateXML($row, $parentNode)
    {
        if (empty($row)) {
            return;
        }

        foreach ($row as $key => $value) {
            if (is_array($value)) {
                $content = $value['_content'] ?? null;
                $attributes = $value['_attributes'] ?? [];

                $xmlRow = $parentNode->addChild($key, $content);
                foreach ($attributes as $attributeName => $attributeValue) {
                    $xmlRow->addAttribute($attributeName, $attributeValue);
                }

                foreach ($value as $subKey => $subValue) {
                    if ($subKey === '_content' || $subKey === '_attributes') {
                        continue;
                    }

                    if (is_array($subValue)) {
                        $this->generateXML([$subKey => $subValue], $xmlRow);
                    }
                }
            } else if (is_string($value)) {
                $prop = $parentNode->addChild($key);
                $prop->addCData($value);
            } else {
                $parentNode->addChild($key, $value);
            }
        }
    }

    protected function generateProductRow($product)
    {
        $parentProduct = $product->parentProduct[0];

        $price = $product->price;
        if (now()->isBetween(Carbon::parse($product->start_date), Carbon::parse($product->end_date))) {
            $price = $product->sale_price;
        }

        $image = $product->images[0] ?? null;
        if ($image) {
            $image = asset('storage/' . $image);
        }

        return [
            'id' => $product->id,
            'titolo' => $product->name,
            'descrizione' => $product->content,
            'categoria' => [
                '_attributes' => [
                    'codice' => '319029248',
                ],
            ],
            'data' => now()->format('Y-m-d'),
            'comune' => [
                '_attributes' => [
                    'codice' => '024038'
                ],
            ],
            'email' => 'info@milanihome.it',
            'prezzo' => (float) number_format($price, 2, '.', ''),
            'paypal' => 1,
            'url_redirect' => $parentProduct->url . '?s=' . $product->sku,
            'pictures' => [
                'picture_url' => $image ?? '',
            ],
        ];
    }
}
