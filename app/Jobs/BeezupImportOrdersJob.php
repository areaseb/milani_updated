<?php

namespace App\Jobs;

use App\Services\BeezupClient;
use Botble\ACL\Models\User;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderAddress;
use Botble\Ecommerce\Models\OrderHistory;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class BeezupImportOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $client;

    protected $carriers;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(BeezupClient $client)
    {
        $this->client = $client;
        $this->carriers = config('beezup.carriers');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $page = 1;
        do {
            $result = $this->client->getOrders($page++);
            $this->importOrders($result->orders);
        } while ($result->hasMoreResults);
    }

    protected function importOrders(Collection $orders)
    {
        $orders->each(fn ($order) => $this->importOrder($order));
    }

    protected function importOrder($data)
    {
        // If the order exist, discard it
        if ($this->doesOrderExists($data->beezUPOrderId)) {
            return;
        }

        $customer = $this->getOrCreateCustomer($data);
        $this->createOrder($data, $customer);
    }

    protected function doesOrderExists($externalId)
    {
        return Order::where('external_id', $externalId)->exists();
    }

    protected function getOrCreateCustomer($data)
    {
        $customer = Customer::where('external_id', $data->order_Buyer_Identifier)->first();
        if ($customer) {
            return $customer;
        }

        $customer = new Customer();
        $customer->external_id = $data->order_Buyer_Identifier;
        $customer->name = $data->order_Buyer_LastName;
        $customer->status = 'activated';
        $customer->save();

        return $customer;
    }

    protected function createOrder($data, $customer)
    {
        $order = new Order();
        $order->external_id = $data->beezUPOrderId;
        $order->user_id = $customer->id;
        $order->amount = (float) $data->order_TotalPrice;
        $order->tax_amount = 0;
        $order->shipping_method = 'default'; // Spedizione gratuita
        $order->shipping_amount = (float) $data->order_Shipping_Price;
        $order->sub_total = (float) $data->order_TotalPrice;
        $order->is_confirmed = true;
        $order->is_finished = true;
        $order->completed_at = Carbon::parse($data->order_PurchaseUtcDate)->format('Y-m-d H:i:s');
        $order->is_exported = false;
        $order->status = 'processing';
        $order->shipping = $this->carriers[$data->marketplaceBusinessCode] ?? null;

        $order->save();

        $this->createOrderAddress($order, $data);
        $this->createOrderProducts($order, $data);
        $this->createOrderHistory($order);
    }

    protected function createOrderAddress($order, $data)
    {
        $address = new OrderAddress();
        $address->name = $data->order_Shipping_FirstName . ' ' . $data->order_Shipping_LastName;
        $address->phone = $data->order_Shipping_Phone;
        $address->country = $data->order_Shipping_AddressCountryIsoCodeAlpha2;
        $address->city = $data->order_Shipping_AddressCity;
        $address->address = $data->order_Shipping_AddressLine1;
        $address->zip_code = $data->order_Shipping_AddressPostalCode;
        $address->order_id = $order->id;
        $address->type = 'shipping_address';

        $address->save();
    }

    protected function createOrderProducts($order, $data)
    {
        collect($data->orderItems)->each (fn ($item) => $this->createOrderProduct($order, $item));

    }

    protected function createOrderProduct($order, $item)
    {
        $product = $this->getProductBySku($item->orderItem_MerchantImportedProductId);

        $orderProduct = new OrderProduct();
        $orderProduct->order_id = $order->id;
        $orderProduct->qty = (int) $item->orderItem_Quantity;
        $orderProduct->price = (float) $item->orderItem_ItemPrice;
        $orderProduct->tax_amount = 0;
        $orderProduct->options = [];
        $orderProduct->product_options = null;
        $orderProduct->product_id = $product?->id;
        $orderProduct->product_name = $item->orderItem_Title;
        $orderProduct->product_image = null;
        $orderProduct->weight = $product?->weight;
        $orderProduct->restock_quantity = 0;
        $orderProduct->product_type = 'physical';

        $orderProduct->save();
    }

    protected function getProductBySku($sku)
    {
        return Product::where('sku', $sku)->first();
    }

    protected function createOrderHistory($order)
    {
        collect([
            'create_order' => 'New order from Beezup',
        ])->each(function ($value, $key) use ($order) {
            $history = new OrderHistory();
            $history->action = $key;
            $history->description = $value;
            $history->order_id = $order->id;
            $history->save();
        });
    }
}
