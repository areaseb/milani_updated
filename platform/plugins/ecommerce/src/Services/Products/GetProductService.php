<?php

namespace Botble\Ecommerce\Services\Products;

use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use EcommerceHelper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetProductService
{
    protected ProductInterface $productRepository;

    public function __construct(ProductInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getProduct(
        Request $request,
        $category = null,
        $brand = null,
        array $with = [],
        array $withCount = [],
        array $conditions = [],
        $paginate = true
    ): Collection|LengthAwarePaginator {
        $num = (int)$request->input('num');
        $shows = EcommerceHelper::getShowParams();

        if (! array_key_exists($num, $shows)) {
            $num = (int)theme_option('number_of_products_per_page', 12);
        }

        $queryVar = [
            'keyword' => $request->input('q'),
            'brands' => (array)$request->input('brands', []),
            'categories' => (array)$request->input('categories', []),
            'tags' => (array)$request->input('tags', []),
            'collections' => (array)$request->input('collections', []),
            'attributes' => (array)$request->input('attributes', []),
            'max_price' => $request->input('max_price'),
            'min_price' => $request->input('min_price'),
            'sort_by' => $request->input('sort-by'),
            'num' => $num,
        ];

        if ($category) {
            $queryVar['categories'] = array_merge($queryVar['categories'], [is_object($category) ? $category->id : $category]);
        }

        $queryVar['categories'] = $this->purgeCategories($queryVar['categories']);

        if ($brand) {
            $queryVar['brands'] = array_merge(($queryVar['brands']), [$brand]);
        }

        $countAttributeGroups = 1;
        if (count($queryVar['attributes'])) {
            $countAttributeGroups = DB::table('ec_product_attributes')
                ->whereIn('id', $queryVar['attributes'])
                ->distinct('attribute_set_id')
                ->count('attribute_set_id');
        }

        $orderBy = [
            'ec_products.order' => 'ASC',
            'ec_products.created_at' => 'DESC',
        ];

        if (! EcommerceHelper::isReviewEnabled() && in_array($queryVar['sort_by'], ['rating_asc', 'rating_desc'])) {
            $queryVar['sort_by'] = 'date_desc';
        }

        $params = array_merge([
                'paginate' => [
                    'per_page' => $queryVar['num'],
                    'current_paged' => (int)$request->query('page', 1),
                ],
                'with' => $with,
                'withCount' => $withCount,
            ], EcommerceHelper::withReviewsParams());

        if (!$paginate) {
            $params['paginate']['per_page'] = false;
        }

        switch ($queryVar['sort_by']) {
            case 'date_asc':
                $orderBy = [
                    'ec_products.created_at' => 'asc',
                ];

                break;
            case 'date_desc':
                $orderBy = [
                    'ec_products.created_at' => 'desc',
                ];

                break;
            case 'price_asc':
                $orderBy = [
                    'products_with_final_price.final_price' => 'asc',
                ];

                break;
            case 'price_desc':
                $orderBy = [
                    'products_with_final_price.final_price' => 'desc',
                ];

                break;
            case 'name_asc':
                $orderBy = [
                    'ec_products.name' => 'asc',
                ];

                break;
            case 'name_desc':
                $orderBy = [
                    'ec_products.name' => 'desc',
                ];

                break;
            case 'rating_asc':
                if (EcommerceHelper::isReviewEnabled()) {
                    $orderBy = [
                        'reviews_avg' => 'asc',
                    ];
                }

                break;
            case 'rating_desc':
                if (EcommerceHelper::isReviewEnabled()) {
                    $orderBy = [
                        'reviews_avg' => 'desc',
                    ];
                }

                break;
            default: // date_desc
                $orderBy = [
                    'ec_products.created_at' => 'desc',
                ];
        }

        if (! empty($conditions)) {
            $params['condition'] = array_merge([
                'ec_products.status' => BaseStatusEnum::PUBLISHED,
                'ec_products.is_variation' => 0,
            ], $conditions);
        }

        $products = $this->productRepository->filterProducts([
            'keyword' => $queryVar['keyword'],
            'min_price' => $queryVar['min_price'],
            'max_price' => $queryVar['max_price'],
            'categories' => $queryVar['categories'],
            'tags' => $queryVar['tags'],
            'collections' => $queryVar['collections'],
            'brands' => $queryVar['brands'],
            'attributes' => $queryVar['attributes'],
            'count_attribute_groups' => $countAttributeGroups,
            'order_by' => $orderBy,
        ], $params, true);

        if ($queryVar['keyword'] && is_string($queryVar['keyword'])) {
            $products->setCollection(BaseHelper::sortSearchResults($products->getCollection(), $queryVar['keyword'], 'name'));
        }

        return $products;
    }

    protected function purgeCategories($categories)
    {
        $valid = [];
        foreach ($categories as $id) {
            $category = ProductCategory::find($id);
            if (!$category) {
                continue;
            }

            foreach ($category->children as $child) {
                if (in_array($child->id, $categories)) {
                    continue 2;
                }
            }

            $valid[] = $id;
        }

        return $valid;
    }
}
