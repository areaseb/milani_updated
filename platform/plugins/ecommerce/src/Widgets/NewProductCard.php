<?php

namespace Botble\Ecommerce\Widgets;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Base\Widgets\Card;

class NewProductCard extends Card
{
    public function getOptions(): array
    {
        $data = app(ProductInterface::class)
            ->getModel()
            ->whereDate('created_at', '>=', $this->startDate)
            ->whereDate('created_at', '<=', $this->endDate)
            ->selectRaw('count(id) as total, date_format(created_at, "' . $this->dateFormat . '") as period')
            ->groupBy('period')
            ->pluck('total')
            ->toArray();

        return [
            'series' => [
                [
                    'data' => $data,
                ],
            ],
        ];
    }

    public function getViewData(): array
    {
        $count = app(ProductInterface::class)
            ->getModel()
            ->whereDate('created_at', '>=', $this->startDate)
            ->whereDate('created_at', '<=', $this->endDate)
            ->where([
                'status' => BaseStatusEnum::PUBLISHED,
                'is_variation' => false,
            ])
            ->count();

        return array_merge(parent::getViewData(), [
            'label' => trans('plugins/ecommerce::reports.products'),
            'value' => $count,
            'icon' => 'fas fa-database',
        ]);
    }
}
