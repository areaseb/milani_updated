<?php

namespace Botble\Ecommerce\Supports;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductAttribute;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Repositories\Eloquent\ProductAttributeSetRepository;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeSetInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class RenderProductAttributeSetsOnSearchPageSupport
{
    protected ProductAttributeSetInterface|ProductAttributeSetRepository $productAttributeSetRepository;

    protected $categories = [];

    public function __construct(ProductAttributeSetInterface $productAttributeSetRepository, )
    {
        $this->categories = $this->getValidCategories();

        $this->productAttributeSetRepository = $productAttributeSetRepository;
    }

    public function render(array $params = []): string
    {
        $params = array_merge(['view' => 'plugins/ecommerce::themes.attributes.attributes-filter-renderer'], $params);

        $with = ['attributes'];

        if (is_plugin_active('language') && is_plugin_active('language-advanced')) {
            $with[] = 'attributes.translations';
        }

        $attributeSets = $this->productAttributeSetRepository
            ->advancedGet([
                'condition' => [
                    'status' => BaseStatusEnum::PUBLISHED,
                    'is_searchable' => 1,
                ],
                'order_by' => [
                    'order' => 'ASC',
                ],
                'with' => $with,
            ]);

        $data = [];
        foreach ($attributeSets as $attributeSet) {
            if (!in_array($attributeSet->slug, ['colore-1', 'materiale-1', 'forma', 'stile'])) {
                continue;
            }

            $data[] = [
                'attributeSet' => $attributeSet,
                'attributes' => $this->getAttributesForAttributeSet($attributeSet),
            ];
        }

        return view($params['view'], ['attributeSets' => $data])->render();
    }

    public function getMaxPrice()
    {
        $categories = Request::get('categories');
        if (!$categories) {
            return (int) theme_option('max_filter_price', 100000) * get_current_exchange_rate();
        }

        $now = Carbon::now();

        $query = Product::select('products_with_final_price.final_price')
            ->join(DB::raw('
                (
                    SELECT DISTINCT
                        `ec_products`.id,
                        CASE
                            WHEN (
                                ec_products.sale_type = 0 AND
                                ec_products.sale_price <> 0
                            ) THEN ec_products.sale_price
                            WHEN (
                                ec_products.sale_type = 0 AND
                                ec_products.sale_price = 0
                            ) THEN ec_products.price
                            WHEN (
                                ec_products.sale_type = 1 AND
                                (
                                    ec_products.start_date > ' . esc_sql($now) . ' OR
                                    ec_products.end_date < ' . esc_sql($now) . '
                                )
                            ) THEN ec_products.price
                            WHEN (
                                ec_products.sale_type = 1 AND
                                ec_products.start_date <= ' . esc_sql($now) . ' AND
                                ec_products.end_date >= ' . esc_sql($now) . '
                            ) THEN ec_products.sale_price
                            WHEN (
                                ec_products.sale_type = 1 AND
                                ec_products.start_date IS NULL AND
                                ec_products.end_date >= ' . esc_sql($now) . '
                            ) THEN ec_products.sale_price
                            WHEN (
                                ec_products.sale_type = 1 AND
                                ec_products.start_date <= ' . esc_sql($now) . ' AND
                                ec_products.end_date IS NULL
                            ) THEN ec_products.sale_price
                            ELSE ec_products.price
                        END AS final_price
                    FROM `ec_products`
                ) AS products_with_final_price
            '), function ($join) {
                return $join->on('products_with_final_price.id', '=', 'ec_products.id');
            });

        // We are getting only variations, so we need to join with product_variations table
        $query = $query
            ->leftJoin('ec_product_variations', 'ec_product_variations.product_id', 'ec_products.id');

        $query = $query->rightJoin('ec_product_category_product', 'ec_product_category_product.product_id', 'ec_product_variations.configurable_product_id')
            ->whereIn('ec_product_category_product.category_id', $this->categories);

        $maxPrice = $query->max('products_with_final_price.final_price');

        return (int) (ceil((float) $maxPrice));
    }

    protected function getValidCategories()
    {
        $categories = Request::get('categories');
        if (!$categories) {
            return [];
        }

        $valid = [];
        foreach ($categories as $category) {
            $children = ProductCategory::where('parent_id', $category)
                ->get()
                ->pluck('id')
                ->toArray();

            // Let's check that there are no children in the $categories array
            $isValid = true;
            foreach ($children as $child) {
                if (in_array($child, $categories)) {
                    $isValid = false;
                    break;
                }
            }

            if ($isValid) {
                $valid[] = $category;
            }
        }

        return $valid;
    }

    protected function getAttributesForAttributeSet($attributeSet)
    {
        if (empty($this->categories)) {
            return $attributeSet->attributes;
        }

        // Let's get all attribute for this attribute set for products in the selected categories
        $attributes = DB::table('ec_product_attributes')
            ->select('ec_product_attributes.id')

            ->leftJoin('ec_product_variation_items', 'ec_product_variation_items.attribute_id', '=', 'ec_product_attributes.id')
            ->leftJoin('ec_product_variations', 'ec_product_variations.id', '=', 'ec_product_variation_items.variation_id')
            ->leftJoin('ec_product_category_product', 'ec_product_category_product.product_id', '=', 'ec_product_variations.configurable_product_id')

            ->where('ec_product_attributes.attribute_set_id', $attributeSet->id)
            ->whereIn('ec_product_category_product.category_id', $this->categories)
            ->distinct()
            ->get()
            ->pluck('id')
            ->map(function ($id) {
                return ProductAttribute::find($id);
            });

        return $attributes;
    }
}
