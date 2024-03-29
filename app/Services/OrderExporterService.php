<?php

namespace App\Services;

use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\Product;
use Botble\Payment\Enums\PaymentMethodEnum;
use Illuminate\Support\Collection;

class OrderExporterService
{
    protected const NUMBER_OF_ORDERS_PER_REQUEST = 50;

    protected $client;

    protected $lines = null;

    protected $orderIds = [];

    public function __construct(GSLinkClient $client)
    {
        $this->client = $client;
        $this->lines = collect([]);
        $this->orderIds = collect([]);
    }

    public function export()
    {
        $this->getOrdersToExportQuery()
            ->chunk(self::NUMBER_OF_ORDERS_PER_REQUEST, fn ($orders) => $orders->each(fn ($order) => $this->exportOrder($order)));

        if ($this->exportLines()) {
            Order::whereIn('id', $this->orderIds)->update(['is_exported' => true]);
        }

        $this->orderIds = collect([]);
        $this->lines = collect([]);
    }

    public function forceUpdateBatch(Collection $orders, $updateCustomer = true)
    {
        // Force update only already exported orders.
        // The others will be exported normally
        $this->lines = collect([]);

        $orders
            ->filter(fn ($order) => $order->is_exported)
            ->each(fn ($order) => $this->exportOrder($order));

        // Export only if there are lines to export
        if ($this->lines->count()) {
            $this->client->export($this->lines, true);
        }
    }

    public function forceUpdate($order, $updateCustomer = true)
    {
        try {
            if ($updateCustomer) {
                $customer = $this->exportCustomer($order);
                $this->client->updateCustomer($customer);
            }

            $this->lines = collect([]);
            $this->exportOrder($order);
            $this->client->export($this->lines, true);
            $this->orderIds = collect([]);

        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    protected function getOrdersToExportQuery()
    {
        return Order::whereHas('payment', fn ($query) => $query->where('status', 'completed'))
            ->where('is_exported', false);
    }

    protected function exportOrder($order)
    {
        $order->products->each(fn ($product) => $this->exportProduct($order, $product));
        $this->orderIds->push($order->id);
    }

    protected function exportCustomer($order)
    {
        return collect([
            'numeroOrdine' => $order->code,
            'nomeCliente' => $order->shippingAddress->name,
            'indirizzo' => $order->shippingAddress->address,
            'cap' => (string) ($order->shippingAddress->zip_code ?? ""),
            'citta' => $order->shippingAddress->city,
            'prov' => $order->shippingAddress->state,
            'nazione' => $order->shippingAddress->country,
            'telefono' => $order->shippingAddress->phone,
            'email' => $order->shippingAddress->email,
            'note' => '',
        ]);
    }

    protected function exportProduct($order, $orderProduct)
    {
        $i = 0;
        $skuSet = $orderProduct->product->sku_set;
        if (!empty($skuSet) && $skuSet !== 'tempesta') {
            $skuSetExploded = explode(',', $skuSet);
            collect($skuSetExploded)->each(function ($set) use (&$rows, $order, $orderProduct, &$i) {
                $setExploded = explode(':', $set);
                $quantity = ((int) $setExploded[1]) * $orderProduct->qty;

                $line = $this->generateProductRow($order, $this->getProductByCodiceCosma($setExploded[0]), $quantity, $i++);
                if ($line) {
                    $this->lines->push($line);
                }
            });

        } else {
            $line = $this->generateProductRow($order, $orderProduct->product, $orderProduct->qty, $i++);
            if ($line) {
                $this->lines->push($line);
            }
        }
    }

    protected function generateProductRow($order, $product, $quantity, $index)
    {
        $carrier = (int) $order->carrier;
        if (!$carrier || $carrier == 1) {
            $carrier = (int) $product->carrier;
        }

        return [
            'sku' => $product->codice_cosma,
            'spedizioniere' => $carrier,
            'barcode' => '',
            'descrizione' => '',
            'quantita' => $quantity,
            'prezzo' => $product->price,
            'pagamento' => $this->getPayment($order),
            'nomeCliente' => $order->shippingAddress->name,
            'indirizzo' => $order->shippingAddress->address,
            'cap' => (string) ($order->shippingAddress->zip_code ?? ""),
            'citta' => $order->shippingAddress->city,
            'prov' => $order->shippingAddress->state,
            'nazione' => $order->shippingAddress->country,
            'telefono' => $order->shippingAddress->phone,
            'email' => $order->shippingAddress->email,
            'numeroOrdine' => $order->code,
            'numeroItem' => (string) $index,
            'note' => '',
            'provenienza' => $this->getSource($order),
        ];
    }

    protected function getSource($order)
    {
        return config("gslink.source.{$order->source}", config('gslink.source_default'));
    }

    protected function getPayment($order)
    {
        $method = $order->payment->payment_channel == PaymentMethodEnum::EXTERNAL
            ? $order->payment->external_payment_channel
            : $order->payment->payment_channel;

        return config("gslink.payment.{$method}", config('gslink.payment_default'));
    }

    protected function getProductByCodiceCosma($codiceCosma)
    {
        return Product::where('codice_cosma', $codiceCosma)->first();
    }

    protected function exportLines()
    {
        return $this->client->export($this->lines);
    }
}
