<?php

namespace Botble\Ecommerce\Widgets;

use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Base\Widgets\Card;

class NewCustomerCard extends Card
{
    public function getOptions(): array
    {
        $data = app(CustomerInterface::class)
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
        $count = app(CustomerInterface::class)
            ->getModel()
            ->whereDate('created_at', '>=', $this->startDate)
            ->whereDate('created_at', '<=', $this->endDate)
            ->count();

        return array_merge(parent::getViewData(), [
            'label' => trans('plugins/ecommerce::reports.customers'),
            'value' => $count,
            'icon' => 'fas fa-users',
        ]);
    }
}
