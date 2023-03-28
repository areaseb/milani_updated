<?php

namespace Botble\Ecommerce\Exports;

use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use EcommerceHelper;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CsvProductExport implements FromCollection, WithHeadings
{
    use Exportable;

    /**
     * @var Collection
     */
    protected $results;

    /**
     * @var bool
     */
    protected $isMarketplaceActive;

    /**
     * @var bool
     */
    protected $enabledDigital;

    /**
     * CsvProductExport constructor.
     */
    public function __construct()
    {
        $this->results = collect([]);

        $this->isMarketplaceActive = is_plugin_active('marketplace');
        $this->enabledDigital = EcommerceHelper::isEnabledSupportDigitalProducts();

        $with = [
            'categories',
            'slugable',
            'brand',
            'tax',
            'productLabels',
            'productCollections',
            'variations',
            'variations.product',
            'variations.configurableProduct',
            'variations.productAttributes.productAttributeSet',
            'tags',
            'productAttributeSets',
        ];
        if ($this->isMarketplaceActive) {
            $with[] = 'store';
        }

        app(ProductInterface::class)
            ->select(['*'])
            ->where('is_variation', 0)
            ->with($with)
            ->chunk(400, function ($products) {
                $this->results = $this->results->concat(collect($this->productResults($products)));
            });
    }

    /**
     * @param Collection $products
     * @return array
     */
    public function productResults(Collection $products): array
    {
        $results = [];
        foreach ($products as $product) {
            $productAttributes = [];
            if (!$product->is_variation) {
                $productAttributes = $product->productAttributeSets->pluck('title')->all();
            }

            foreach ($this->headings() as $key => $title) {
                $result[$key] = $product->{$key};
            }

            $result['product_name'] = $product->name;
            $result['categories'] = $product->categories->pluck('name')->implode(',');
            $result['status'] = $product->status->getValue();
            $result['product_collections'] = $product->productCollections->pluck('name')->implode(',');
            $result['labels'] = $product->productLabels->pluck('name')->implode(',');
            $result['tax'] = $product->tax->title;
            $result['images'] = implode(',', $product->images);
            $result['product_attributes'] = implode(',', $productAttributes);
            $result['import_type'] = 'product';
            $result['stock_status'] = $product->stock_status->getValue();
            $result['tags'] = $product->tags->pluck('name')->implode(',');

            if ($this->enabledDigital) {
                $result['product_type'] = $product->product_type;
            }

            if ($this->isMarketplaceActive) {
                $result['vendor'] = $product->store_id ? $product->store->name : null;
            }

            $results[] = $result;

            if ($product->variations->count()) {
                foreach ($product->variations as $variation) {
                    $productAttributes = $this->getProductAttributes($variation);

                    $result = [];
                    foreach ($this->headings() as $key => $title) {
                        $result[$key] = $variation->product->{$key};
                    }

                    $result['product_name'] = $variation->product->name;
                    $result['status'] = $variation->product->status->getValue();
                    $result['images'] = implode(',', $variation->product->images);
                    $result['product_attributes'] = implode(',', $productAttributes);
                    $result['import_type'] = 'variation';
                    $result['stock_status'] = $variation->product->stock_status->getValue();

                    if ($this->enabledDigital) {
                        $result['product_type'] = '';
                    }

                    if ($this->isMarketplaceActive) {
                        $result['vendor'] = '';
                    }

                    $results[] = $result;
                }
            }
        }

        return $results;
    }

    /**
     * @param Product|ProductVariation $product
     * @return array
     */
    public function getProductAttributes($product): array
    {
        $productAttributes = [];
        foreach ($product->productAttributes as $productAttribute) {
            if ($productAttribute->productAttributeSet) {
                $productAttributes[] = $productAttribute->productAttributeSet->title . ':' . $productAttribute->title;
            }
        }

        return $productAttributes;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->results;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        $headings = [
            'sku' => 'SKU',
            'codice_cosma' => 'Codice Cosma',
            'product_name' => 'Product name',
            'description' => 'Description',
            'slug' => 'Slug',
            'auto_generate_sku' => 'Auto Generate SKU',
            'categories' => 'Categories',
            'ord' => 'Ord',
            'status' => 'Status',
            'is_featured' => 'Is featured?',
            'brand' => 'Brand',
            'product_collections' => 'Product collections',
            'labels' => 'Labels',
            'tax' => 'Tax',
            'images' => 'Images',
            'price' => 'Price',
            'product_attributes' => 'Product attributes',
            'import_type' => 'Import type',
            'is_variation_default' => 'Is variation default?',
            'stock_status' => 'Stock status',
            'with_storehouse_management' => 'With storehouse management',
            'quantity' => 'Quantity',
            'allow_checkout_when_out_of_stock' => 'Allow checkout when out of stock',
            'sale_price' => 'Sale price',
            'start_date_sale_price' => 'Start date sale price',
            'end_date_sale_price' => 'End date sale price',
            'weight' => 'Weight',
            'length' => 'Length',
            'wide' => 'Wide',
            'height' => 'Height',
            'content' => 'Content',
            'tags' => 'Tags',
            'ean' => 'EAN',
            'nome_amazon' => 'Nome Amazon',
            'seo_amazon' => 'Seo Amazon',
            'bullet_1' => 'Bullet 1',
            'bullet_2' => 'Bullet 2',
            'bullet_3' => 'Bullet 3',
            'bullet_4' => 'Bullet 4',
            'bullet_5' => 'Bullet 5',
            'prodotti_correlati' => 'PRODOTTI CORRELATI',
            'made_in' => 'Made in',
            'larghezza_scatola_collo_1' => 'Larghezza Scatola collo 1',
            'larghezza_scatola_collo_2' => 'Larghezza Scatola collo 2',
            'larghezza_scatola_collo_3' => 'Larghezza Scatola collo 3',
            'larghezza_scatola_collo_4' => 'Larghezza Scatola collo 4',
            'larghezza_scatola_collo_5' => 'Larghezza Scatola collo 5',
            'profondita_scatola_collo_1' => 'Profondità Scatola collo 1',
            'profondita_scatola_collo_2' => 'Profondità Scatola collo 2',
            'profondita_scatola_collo_3' => 'Profondità Scatola collo 3',
            'profondita_scatola_collo_4' => 'Profondità Scatola collo 4',
            'profondita_scatola_collo_5' => 'Profondità Scatola collo 5',
            'altezza_scatola_collo_1' => 'Altezza Scatola collo 1',
            'altezza_scatola_collo_2' => 'Altezza Scatola collo 2',
            'altezza_scatola_collo_3' => 'Altezza Scatola collo 3',
            'altezza_scatola_collo_4' => 'Altezza Scatola collo 4',
            'altezza_scatola_collo_5' => 'Altezza Scatola collo 5',
            'cubatura' => 'Cubatura',
            'peso_con_imballo_collo_1' => 'Peso Con Imballo collo 1',
            'peso_con_imballo_collo_2' => 'Peso Con Imballo collo 2',
            'peso_con_imballo_collo_3' => 'Peso Con Imballo collo 3',
            'peso_con_imballo_collo_4' => 'Peso Con Imballo collo 4',
            'peso_con_imballo_collo_5' => 'Peso Con Imballo collo 5',
            'assemblato' => 'Assemblato',
            'kit_e_istruzioni_incluse_si_intendono_anche_pile' => 'Kit E Istruzioni Incluse (Si Intendono Anche Pile)',
            'sku_set' => 'sku_set',
        ];

        if ($this->enabledDigital) {
            $headings['product_type'] = 'Product type';
        }

        if ($this->isMarketplaceActive) {
            $headings['vendor'] = 'Vendor';
        }

        return $headings;
    }
}
