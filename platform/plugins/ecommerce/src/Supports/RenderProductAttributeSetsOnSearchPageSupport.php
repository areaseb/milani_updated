<?php

namespace Botble\Ecommerce\Supports;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Repositories\Eloquent\ProductAttributeSetRepository;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeSetInterface;

class RenderProductAttributeSetsOnSearchPageSupport
{
    protected ProductAttributeSetInterface|ProductAttributeSetRepository $productAttributeSetRepository;

    public function __construct(ProductAttributeSetInterface $productAttributeSetRepository)
    {
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

        // First let's filter out not wanted attribute sets
        $attributeSets = $attributeSets->filter(function ($attributeSet) {
            return in_array($attributeSet->slug, ['colore-1', 'materiale-1', 'forma', 'stile']);
        });

        return view($params['view'], compact('attributeSets'))->render();
    }
}
