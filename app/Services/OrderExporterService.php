<?php

namespace App\Services;

use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\Product;
use Illuminate\Support\Facades\Storage;

class OrderExporterService
{
    protected const HEADER = ['ordine', 'SKU', 'qta', 'riferimento', 'indirizzo', 'cap', 'citta', 'provincia', 'stato', 'file_1', 'file_2', 'file_3', 'file_4', 'file_5', 'file_6', 'file_7', 'file_8', 'file_9', 'file_10'];
    protected const FILENAME_TEMPLATE = '04110830249_[DATE].csv';

    protected const DISK = 'tmp';

    protected $filename;

    public function export()
    {
        $orders = $this->retrieveOrdersToExport();
        if ($orders->isEmpty()) {
            return;
        }

        $this->generateFilename();
        $this->exportHeader();
        $orders->each(fn ($order) => $this->exportOrder($order));
    }

    protected function retrieveOrdersToExport()
    {
        return Order::whereHas('payment', fn ($query) => $query->where('status', 'completed'))
            ->where('is_exported', false)
            ->get();
    }

    protected function generateFilename()
    {
        $this->filename = str_replace('[DATE]', date('Ymdhis'), self::FILENAME_TEMPLATE);
    }

    protected function exportHeader()
    {
        $this->exportRow(self::HEADER);
    }

    protected function exportOrder($order)
    {
        $order->products->each(fn ($product) => $this->exportProduct($order, $product));
        // $order->update(['is_exported' => true]);
    }

    protected function exportProduct($order, $orderProduct)
    {
        $rows = [];
        $skuSet = $orderProduct->product->sku_set;
        if (!empty($skuSet) && $skuSet !== 'tempesta') {
            $skuSetExploded = explode(',', $skuSet);
            collect($skuSetExploded)->each(function ($set) use (&$rows, $order, $orderProduct) {
                $setExploded = explode(':', $set);
                $quantity = ((int) $setExploded[1]) * $orderProduct->qty;
                $rows[] = $this->generateProductRow($order, $setExploded[0], $quantity);
            });

        } else {
            $rows = [$this->generateProductRow($order, $orderProduct->product->codice_cosma, $orderProduct->qty)];
        }

        collect($rows)->each(fn ($row) => $this->exportRow($row));
    }

    protected function generateProductRow($order, $sku, $quantity)
    {
        return [
            $order->code,
            $sku,
            $quantity,
            $order->code,
            $order->shippingAddress->address,
            '',
            $order->shippingAddress->city,
            $order->shippingAddress->state,
            $order->shippingAddress->country,
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ];
    }

    protected function exportRow($row)
    {
        $content = implode(';', array_map(fn ($item) => "\"{$item}\"", $row));
        Storage::disk(self::DISK)->append($this->filename, $content);
    }
}
