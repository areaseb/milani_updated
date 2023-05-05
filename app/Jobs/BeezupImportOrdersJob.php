<?php

namespace App\Jobs;

use App\Services\BeezupClient;
use Botble\ACL\Models\User;
use Botble\Ecommerce\Models\Order;
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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(BeezupClient $client)
    {
        $this->client = $client;
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
        dd($data);
        // If the order exist, discard it
        if ($this->doesOrderExists($data->beezUPOrderId)) {
            return;
        }

        $user = $this->getOrCreateUser($data);
        $this->createOrder($data, $user);
    }

    protected function doesOrderExists($externalId)
    {
        return Order::where('external_id', $externalId)->exists();
    }

    protected function getOrCreateUser($data)
    {
        $user = User::where('external_id', $data->order_Buyer_Identifier)->first();
        if ($user) {
            return $user;
        }

        $user = new User();
        $user->external_id = $data->order_Buyer_Identifier;
        $user->last_name = $data->order_Buyer_LastName;
        $user->save();

        return $user;
    }

    protected function createOrder($data, $user)
    {
        $order = new Order();
        $order->external_id = $data->beezUPOrderId;
        $order->user_id = $user->id;
        $order->amount = (float) $data->order_TotalPrice;
        $order->tax_amount = 0;
        $order->shipping_method = 'default'; // Spedizione gratuita
        $order->shipping_amount = (float) $data->order_Shipping_Price;
        $order->sub_total = (float) $data->order_TotalPrice;
        $order->is_confirmed = true;
        $order->is_finished = true;
        $order->completed_at = Carbon::parse($data->order_PurchaseUtcDate)->format('Y-m-d H:i:s');
        $order->is_exported = false;

        $order->save();

        $this->createOrderAddress($order, $data);
        $this->createOrderProducts($order, $data);
    }

    protected function createOrderAddress($order, $data)
    {
        //
    }

    protected function createOrderProducts($order, $data)
    {

    }
}
