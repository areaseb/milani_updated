<?php

namespace Botble\Ecommerce\Widgets;

use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Base\Widgets\Card;
use Botble\Payment\Enums\PaymentStatusEnum;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class RevenueCard extends Card
{
    public function getOptions(): array
    {
        $data = app(OrderInterface::class)
            ->getModel()
            ->whereDate('created_at', '>=', $this->startDate)
            ->whereDate('created_at', '<=', $this->endDate)
            ->select([
                DB::raw('SUM(COALESCE(amount, 0)) as revenue'),
            ])
            ->selectRaw('count(id) as total, date_format(created_at, "' . $this->dateFormat . '") as period')
            ->groupBy('period')
            ->pluck('revenue')
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
        $revenue = app(OrderInterface::class)
            ->getModel()
            ->select([
                DB::raw('SUM(COALESCE(payments.amount, 0) - COALESCE(payments.refunded_amount, 0)) as revenue'),
                'payments.status',
            ])
            ->join('payments', 'payments.id', '=', 'ec_orders.payment_id')
            ->whereIn('payments.status', [PaymentStatusEnum::COMPLETED, PaymentStatusEnum::PENDING])
            ->whereDate('payments.created_at', '>=', $this->startDate)
            ->whereDate('payments.created_at', '<=', $this->endDate)
            ->groupBy('payments.status')
            ->first();

        return array_merge(parent::getViewData(), [
            'label' => trans('plugins/ecommerce::reports.revenue'),
            'value' => format_price(Arr::get($revenue, 'revenue')),
            'icon' => 'fas fa-hand-holding-usd',
        ]);
    }
}
