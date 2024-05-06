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
// cancellare
//->where('name', 'like', 'RYAN%')            
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
                if ($key == 'sku') {
                    $result[$key] = $product->sku . '_PARENT';
                    continue;
                }

                $result[$key] = $product->{$key};
            }

            $result['product_name'] = $product->name;

            for ($i = 0; $i < count($product->categories) && $i < 9; $i++) {
                $result['category_' . $i] = $product->categories[$i]->name;
            }

            if (count($product->categories) < 9) {
                for ($i = count($product->categories); $i < 9; $i++) {
                    $result['category_' . $i] = '';
                }
            }

            $result['status'] = $product->status->getValue();
            $result['product_collections'] = $product->productCollections->pluck('name')->implode(',');
            $result['labels'] = $product->productLabels->pluck('name')->implode(',');
            $result['tax'] = $product->tax->title;

            for ($i = 0; $i < count($product->images) && $i < 10; $i++) {
                $result['image_' . $i] = url('storage/' . $product->images[$i]);
            }

            if (count($product->images) < 10) {
                for ($i = count($product->images); $i < 10; $i++) {
                    $result['image_' . $i] = '';
                }
            }

            //$result['product_attributes'] = implode(',', $productAttributes);                 
            foreach($productAttributes as $pA){
            	$key = str_replace(' ', '_', strtolower($pA));
            	$result[$key] = '';
            }      
            $result['import_type'] = 'product';
            $result['stock_status'] = $product->stock_status->getValue();
            $result['tags'] = $product->tags->pluck('name')->implode(',');

            if ($this->enabledDigital) {
                $result['product_type'] = $product->product_type;
            }

            if ($this->isMarketplaceActive) {
                $result['vendor'] = $product->store_id ? $product->store->name : null;
            }
            
            for($n = 1; $n <= 5; $n++){
            	if($result['peso_con_imballo_collo_'.$n] == '' || is_null($result['peso_con_imballo_collo_'.$n])){
            		$result['peso_con_imballo_collo_'.$n] = 0;
            	}
            }
			
			$result['quantity'] = $product->quantity ?? 0;
			
			$result['url'] = env('APP_URL').'/prodotti/'.$product->slug.'?s='.$product->sku;
			
            // Let's fix the key
            $result['brand'] = $result['brand']->name ?? null;
            $result['tax'] = $result['tax']->percentage ?? null;
	
            $results[] = $result;
            $parentResult = $result;

            if ($product->variations->count()) {
            	
                foreach ($product->variations as $variation) {
                    $productAttributes = $this->getProductAttributes($variation);

                    $result = [];
                    foreach ($this->headings() as $key => $title) {
                        if (!is_null($variation->product->{$key})) {
                            $result[$key] = $variation->product->{$key};
                        } else {
                            $result[$key] = $parentResult[$key];
                        }
                    }

                    $result['product_name'] = $variation->product->name;
                    $result['status'] = $variation->product->status->getValue();

                    for ($i = 0; $i < count($variation->product->images) && $i < 10; $i++) {
                        $result['image_' . $i] = url('storage/' . $variation->product->images[$i]);
                    }

                    if (count($variation->product->images) < 10) {
                        for ($i = count($variation->product->images); $i < 10; $i++) {
                            $result['image_' . $i] = '';
                        }
                    }

                    //$result['product_attributes'] = implode(',', $productAttributes);
                    foreach($productAttributes as $pA){
		            	if($pA){
		            		list($key, $value) = explode(':', $pA);
		            		$key = str_replace(' ', '_', strtolower($key));
		            		$result[$key] = $value;
		            	}		            	
		            }
  
                    $result['import_type'] = 'variation';
                    $result['stock_status'] = $variation->product->stock_status->getValue();

                    if ($this->enabledDigital) {
                        $result['product_type'] = '';
                    }

                    if ($this->isMarketplaceActive) {
                        $result['vendor'] = '';
                    }
                    
                    for($n = 1; $n <= 5; $n++){
		            	if($result['peso_con_imballo_collo_'.$n] == '' || is_null($result['peso_con_imballo_collo_'.$n])){
		            		$result['peso_con_imballo_collo_'.$n] = 0;
		            	}
		            }
		            
                    $result['quantity'] = $product->quantity ?? 0;
                    
                    $result['url'] = env('APP_URL').'/prodotti/'.$product->slug.'?s='.$product->sku;

                    // Let's fix the key
                    $result['brand'] = $result['brand']->name ?? null;
                    $result['tax'] = $result['tax']->percentage ?? null;
                    $result['tags'] = $result['tags']->pluck('name')->implode(',');

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
            'category_0' => 'Category 0',
            'category_1' => 'Category 1',
            'category_2' => 'Category 2',
            'category_3' => 'Category 3',
            'category_4' => 'Category 4',
            'category_5' => 'Category 5',
            'category_6' => 'Category 6',
            'category_7' => 'Category 7',
            'category_8' => 'Category 8',
            'ord' => 'Ord',
            'status' => 'Status',
            'is_featured' => 'Is featured?',
            'brand' => 'Brand',
            'product_collections' => 'Product collections',
            'labels' => 'Labels',
            'tax' => 'Tax',
            'image_0' => 'Image 0',
            'image_1' => 'Image 1',
            'image_2' => 'Image 2',
            'image_3' => 'Image 3',
            'image_4' => 'Image 4',
            'image_5' => 'Image 5',
            'image_6' => 'Image 6',
            'image_7' => 'Image 7',
            'image_8' => 'Image 8',
            'image_9' => 'Image 9',
            'price' => 'Price',
            'colore_1' => 'Colore 1',
            'colore_2' => 'Colore 2',
            'forma' => 'Forma',
            'tipologia' => 'Tipologia',
            'stile' => 'Stile',
            'impiego' => 'Impiego',
            'materiale_1' => 'Materiale 1',
            'materiale_2' => 'Materiale 2',
            'materiale_3' => 'Materiale 3',
            'sedute' => 'Sedute',
            'dimensioni_disp.' => 'Dimensioni Disp.',
            'sfoderabile' => 'Sfoderabile',
            'portata_massima' => 'Portata massima',
/*            'product_attributes' => 'Product attributes',
            'product_attributes' => 'Product attributes',
            'product_attributes' => 'Product attributes',
            'product_attributes' => 'Product attributes',
            'product_attributes' => 'Product attributes',
            'product_attributes' => 'Product attributes',
            'product_attributes' => 'Product attributes',
            'product_attributes' => 'Product attributes',
            'product_attributes' => 'Product attributes',
            'product_attributes' => 'Product attributes',
            'product_attributes' => 'Product attributes',
            'product_attributes' => 'Product attributes',*/
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
            'url' => 'URL',
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
