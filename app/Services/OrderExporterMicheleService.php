<?php

namespace App\Services;

use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\Product;
use Botble\Payment\Enums\PaymentMethodEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class OrderExporterMicheleService
{
    protected const NUMBER_OF_ORDERS_PER_REQUEST = 50;
    protected const DISK = 'feed';
    protected const FILENAME = 'OrdersMichele.csv';
    protected const SEPARATOR = ";";

    protected $lines = null;

    protected $orderIds = [];

    public function __construct()
    {
        $this->lines = collect([]);
        $this->orderIds = collect([]);
    }

    public function export()
    {
    	$this->deleteOldCatalogFile();
    	
    	$this->getOrdersToExportQuery()
            ->chunk(self::NUMBER_OF_ORDERS_PER_REQUEST, fn ($orders) => $orders->each(fn ($order) => $this->exportOrder($order)));

        if ($this->exportLines()) {
            //download
            echo 'Fatto';
        }

        $this->orderIds = collect([]);
        $this->lines = collect([]);
    }
	
	protected function deleteOldCatalogFile()
    {
        Storage::disk(static::DISK)->delete(static::FILENAME);
    }
    
    protected function getOrdersToExportQuery()
    {
        return Order::where('status', 'processing')
            ->where('created_at', '>', Carbon::now()->subDays(14));		//whereHas('payment', fn ($query) => $query->where('status', 'completed'))
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
            'nomeCliente' => '"'.$order->shippingAddress->name.'"',
            'indirizzo' => '"'.$order->shippingAddress->address.'"',
            'cap' => (string) ($order->shippingAddress->zip_code ?? ""),
            'citta' => '"'.$order->shippingAddress->city.'"',
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
/*            $skuSetExploded = explode(',', $skuSet);
            collect($skuSetExploded)->each(function ($set) use (&$rows, $order, $orderProduct, &$i) {
                $setExploded = explode(':', $set);
                $quantity = ((int) $setExploded[1]) * $orderProduct->qty;
//\Log::info('Set: '. print_r($setExploded[0], true) . ' - Check prodotto: ' . print_r($this->getProductByCodiceCosma($setExploded[0]), true));
                $line = $this->generateProductRow($order, $this->getProductByCodiceCosma($setExploded[0]), $quantity, $i++);
                if ($line) {
                    $this->lines->push($line);
                }
            });
*/
			$line = $this->generateProductRow($order, $orderProduct->product, $orderProduct->qty, $i++);
            if ($line) {
                $this->lines->push($line);
            }
        } else {
//\Log::info('Prodotto normale: '. print_r($orderProduct->product, true));        	
            $line = $this->generateProductRow($order, $orderProduct->product, $orderProduct->qty, $i++);
            if ($line) {
                $this->lines->push($line);
            }
        }
    }

    protected function generateProductRow($order, $product, $quantity, $index)
    {
    	
if(is_null($product)){
	\Log::info('errore import ordine: '. print_r($order, true));
}
        return [
            'sku' => $product->sku,
            'quantita' => $quantity,
            'prezzo' => number_format((($product->price * 1.22) * $quantity) + ($order->shipping_amount / $order->products->count()), 2, ',', '.'),
            'pagamento' => $this->getPayment($order),
            'nomeCliente' => '"'.$order->shippingAddress->name.'"',
            'indirizzo' => '"'.$order->shippingAddress->address.'"',
            'cap' => (string) ($order->shippingAddress->zip_code ?? ""),
            'citta' => '"'.$order->shippingAddress->city.'"',
            'nazione' => $order->shippingAddress->country,
            'telefono' => $order->shippingAddress->phone,
            'email' => $order->shippingAddress->email,
            'numeroOrdine' => $order->marketplace_order_id,
            'idOrdine' => '#1'.str_pad($order->id, 7, '0', STR_PAD_LEFT),
            'numeroItem' => (string) $index,
            'provenienza' => $order->source,
            'data' => $order->created_at,
            'status' => $order->status,
        ];
    }

    protected function getSource($source)
    {
    	$source = explode('.', $source);   	
        return config("gslink.source.{$source[0]}", config('gslink.source_default'));
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
    	$lines = 'SKU;QUANTITÀ;PREZZO;PAGAMENTO;NOME CLIENTE;INDIRIZZO;CAP;CITTÀ;Nazione;TELEFONO;EMAIL;NUMERO ORDINE;ID ORDINE;ORDER ITEMS;PROVENIENZA;DATA;STATUS
';
    	
    	foreach($this->lines as $line){
    		$lines .= implode(self::SEPARATOR, $line).'
';    		
    	}
    	
        return Storage::disk(static::DISK)->put(self::FILENAME, $lines);
    }
}
