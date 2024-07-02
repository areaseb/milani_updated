<?php

namespace App\Jobs;

use App\Exceptions\BeezupCustomerNotValidException;
use App\Mail\BeezupCustomerNotValidEmail;
use App\Services\BeezupClient;
use Botble\ACL\Models\User;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderAddress;
use Botble\Ecommerce\Models\OrderHistory;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Models\Payment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BeezupImportOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $client;

    protected $carriers;
    
    protected $sources;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(BeezupClient $client)
    {
        $this->client = $client;
        $this->carriers = config('beezup.carriers');
        $this->sources = config('beezup.sources');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $debug = false;

        $page = 1;
        do {
            if($debug) {
                $result = $this->client->getOrders($page++, Carbon::now()->subDays(5));
                $add_payment = false;
                $add_products = false;
                $add_address = false;
                $fix_customer = true;

                foreach($result->orders as $order) {
                    $find_by_bezzup_id = true;
                    $find_by_marketplace_id = false;

                    $ids_to_find = [
                        '8D61A57C5A6700002690ab800795754aa6793b978764492',
                        '8D61A57C5A67000975f13ced7355ca3ad7d2f980d2ebb99',
                        '8D61A57C5A67000b415ceeb727f586892d284caf6ae6d18',
                        '8D61A57C5A670008093f54c02845330868d5017af51441d',
                        '8D61A57C5A67000b3eee14ed17a57299678fc4217bbf69b',
                        '8D61A57C5A67000d2cabce854425696a280db668001d7e0',
                        '8D61A57C5A67000f742a413988f5a2db40086fdf73b4042',
                        '8D61A57C5A67000b7c72e0a1a8b5ebebe7c28000e34f9ec',
                        '8D61A57C5A6700074d8fb09a70450e38bb1d0dfa7d06e8e',
                        '8D61A57C5A67000c79aa1789eb657f9bb4fa944d7a283fd',
                        '8D61A57C5A670000d2192bdea75590881fbce38fe325242',
                    ];

                    $found = false;

                    if($find_by_bezzup_id) {
                        $found = in_array($order->beezUPOrderId, $ids_to_find);
                    }
                    /*
                    else if($find_by_marketplace_id) {
                        if(strstr($order->order_MarketplaceOrderId, $ids_to_find))
                            $found = true;
                    }
                    */
                    
                    if($found) {
                        // Find order in DB
                        $db_order = Order::where('external_id', $order->beezUPOrderId)->first();                        

                        if($fix_customer) {
                            $customer = $this->getOrCreateCustomer($order);

                            if($customer) {
                                $db_order->user_id = $customer->id;
                                $db_order->save();
                            }
                        }

                        if($add_address) {
                            $this->createOrderAddress($db_order, $order);
                        }

                        if($add_payment) {
                            $payment_id = $this->createOrderPayment($db_order, $order);
                            $db_order->payment_id = $payment_id;
                            $db_order->save();
                        }

                        if($add_products) {
                            $this->createOrderProducts($db_order, $order);                    
                        }
                    }
                }

            } else {          
                //$result = $this->client->getOrders($page++);
                $result = $this->client->getOrders($page++);                

                $this->importOrders($result->orders);
            }
        } while ($result->hasMoreResults);
    }

    protected function importOrders(Collection $orders)
    {
        $orders->each(function ($order) {
        	\Log::info('Ordine: '.print_r($order, true));
            try {
                $this->importOrder($order);
            } catch (BeezupCustomerNotValidException $e) {
                $this->sendBeezupCustomerNotValidNotification($order);
            }
        });
    }

    protected function importOrder($data)
    {
        // If the order exist, discard it
        if ($this->doesOrderExists($data->beezUPOrderId)) {
            Log::info('order exists: ' . $data->beezUPOrderId);
            return;
        } else {
            Log::info('order DOES NOT exists: ' . $data->beezUPOrderId);    
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
        if (empty($data->order_Buyer_Identifier) && empty($data->order_Buyer_Email)) {
            throw new BeezupCustomerNotValidException();
        }        
        /*
        $customer = Customer::when($data->order_Buyer_Identifier ?? null, fn ($query) => $query->where('external_id', $data->order_Buyer_Identifier))
            ->when($data->order_Buyer_Email ?? null, fn ($query) => $query->where('email', $data->order_Buyer_Email))
            ->first();
        */
        if(!empty($data->order_Buyer_Identifier)) {
            $customer = Customer::where('external_id', $data->order_Buyer_Identifier)->first();
        } else {
            $customer = Customer::where('email', $data->order_Buyer_Email)->first();
        }

        if ($customer) {
            return $customer;
        }

        $customer = new Customer();
        $customer->external_id = $data->order_Buyer_Identifier ?? null;
        $customer->email = $data->order_Buyer_Email ?? null;
        $customer->name = $data->order_Buyer_Name ?? ($data->order_Buyer_LastName ?? 'NA');
        $customer->status = 'activated';
        $customer->save();

        return $customer;
    }

    protected function createOrder($data, $customer)
    {
    	
        $order = new Order();
        $order->source = $this->sources[$data->marketplaceBusinessCode] ?? '_';
        $order->external_id = $data->beezUPOrderId;
        $order->marketplace_order_id = $data->order_MarketplaceOrderId;
        $order->marketplace_technical_code = $data->marketplaceTechnicalCode;
        $order->marketplace_account_id = $data->accountId;
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
        $order->status = 'pending';
        $order->carrier = $this->carriers[$data->marketplaceBusinessCode] ?? '_';

        $order->save();

        $this->createOrderProducts($order, $data);
        $this->createOrderAddress($order, $data);
        $this->createOrderHistory($order);
        $payment_id = $this->createOrderPayment($order, $data);
//\Log::info('Payment id: '.$payment_id);        
        $order->payment_id = $payment_id;
        $order->save();
//\Log::info('Order con payment: '.print_r($order, true));  
//\Log::info('Order data: '.print_r($data, true));      
    }

    protected function createOrderAddress($order, $data)
    {
        $address = new OrderAddress();
        $name = ($data->order_Shipping_FirstName ?? '') . ' ' . ($data->order_Shipping_LastName ?? '');

        if($name == ' ')
            $name = $data->order_Shipping_AddressName;

        $address->name = $name;
        //$address->name = $data->order_Shipping_AddressName ?? (($data->order_Shipping_FirstName ?? '') . ' ' . ($data->order_Shipping_LastName ?? ''));
        $address->phone = $data->order_Shipping_Phone ?? '';
        $address->country = $data->order_Shipping_AddressCountryIsoCodeAlpha2;
        $address->city = $data->order_Shipping_AddressCity;
        $address->address = isset($data->order_Shipping_AddressLine2) ? $data->order_Shipping_AddressLine1 . ' ' . $data->order_Shipping_AddressLine2 : $data->order_Shipping_AddressLine1;
        $address->zip_code = $data->order_Shipping_AddressPostalCode;
        $address->state = $data->order_Shipping_AddressStateOrRegion ?? $this->getProvince($data->order_Shipping_AddressPostalCode);
        $address->order_id = $order->id;
        $address->email = $data->order_Buyer_Email ?? null;
        $address->type = 'shipping_address';
        $address->save();
    }
	
	protected function getProvince($cap)
	{
        $auth_key = env('ZIP_API_AUTH_KEY', '4cQ2_iD1e8@!9DE');//'4cQ2_iD1e8@!9DE';
        $api_url = 'https://api.rider-crm.it/api/zip/province';

        $data = [
            'zip' => $cap,
        ];

        $response = Http::withHeaders([
            'Authorization' => $auth_key,
        ])->post($api_url, $data);

        if ($response->successful()) {
            return $response->json()['data']['province'];
        } else {
            return 'Roma';
        }

        /*
		$curlSES = curl_init(); 
	
		curl_setopt($curlSES,CURLOPT_URL,"https://www.gerriquez.com/comuni/ws.php?datidacap=$cap");
		curl_setopt($curlSES,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curlSES,CURLOPT_HEADER, false); 
		
		$result = json_decode(curl_exec($curlSES));
		
		curl_close($curlSES);
        
		if($result && is_array($result))
		    return $result[0]->Provincia;
        else
            return 'Roma';
        */
	}
	
    protected function createOrderProducts($order, $data)
    {
        collect($data->orderItems)->each (fn ($item) => $this->createOrderProduct($order, $item));
    }

    protected function createOrderProduct($order, $item)
    {
        $orderProduct = new OrderProduct();
        $orderProduct->order_id = $order->id;
        $orderProduct->qty = (int) $item->orderItem_Quantity;
        $orderProduct->price = (float) $item->orderItem_ItemPrice;
        $orderProduct->tax_amount = 0;
        $orderProduct->options = [];
        $orderProduct->product_options = null;
        $orderProduct->product_name = $item->orderItem_Title;
        $orderProduct->product_image = null;
        $orderProduct->restock_quantity = 0;
        $orderProduct->product_type = 'physical';
        $orderProduct->product_id = null;
		$orderProduct->weight = 0;	
        

        $product = $this->getProductBySku($item->orderItem_MerchantImportedProductId, $item->orderItem_MerchantProductId);

		if($product) {
			$orderProduct->product_id = $product->id ?? null;
			$orderProduct->weight = $product->weight ?? 0;

            // Add image
            $image = null;
            if($product->image) {
                $image = $product->image;
            } else if($product->images && is_array($product->images)) {
                $image = $product->images[0];
            }

            $orderProduct->product_image = $image;
		} else {
			// $orderProduct->external_sku = $item->orderItem_MerchantProductId;
        }

        $orderProduct->save();
    }

    protected function getProductBySku($sku, $external_sku = false)
    {
		$product = Product::where('sku', $sku)->first();

		if($product)
			return $product;

		if(!$external_sku)
			return false;

		return Product::where('attaccati_amz', 'like', '%' . $external_sku . '%')->where('is_variation', 1)->first();
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

    protected function createOrderPayment($order, $data)
    {
        $payment = new Payment();

        $payment->order_id = $order->id;
        $payment->payment_channel = PaymentMethodEnum::EXTERNAL; $order->order_PaymentMethod;
        $payment->external_payment_channel = $data->order_PaymentMethod ?? 'Other';
        $payment->amount = $data->order_TotalPrice;
        $payment->status = 'completed';

        $payment->save();
        
        return $payment->id;
    }

    protected function sendBeezupCustomerNotValidNotification($data)
    {
        Mail::to(config('beezup.notification_email'))->send(new BeezupCustomerNotValidEmail($data->beezUPOrderId, $data->beezUPOrderUrl));
    }
}
