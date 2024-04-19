<?php

namespace App\Services;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Botble\Ecommerce\Models\Order;

class BeezupClient
{
    protected const API_ENDPOINT = "https://api.beezup.com";

    protected $apiKey = '';
    protected $storeId = '';

    public function __construct()
    {
        $this->apiKey = config('beezup.api_key');
        $this->storeId = config('beezup.store_id');
    }

    public function autoimport()
    {
        $this->client()->post("/v2/user/catalogs/{$this->storeId}/autoImport/start");
    }

    public function getOrders(int $pageNumber = 1, Carbon $from = null, Carbon $to = null, int $pageSize = 100)
    {
        $from = $from ?? Carbon::now()->subDay();
        $to = $to ?? Carbon::now();

        if ($to->gt(Carbon::now())) {
            $to = Carbon::now()->subMinute();
        }

        $response = $this->client()->post("/orders/v3/list/full", [
            'json' => [
                'storeIds' => [
                    $this->storeId
                ],
                'beginPeriodUtcDate' => $from->toIso8601ZuluString(),
                'endPeriodUtcDate' => $to->toIso8601ZuluString(),
                'pageSize' => $pageSize,
                'pageNumber' => $pageNumber
            ],
        ]);

        $body = json_decode($response->getBody()->getContents());

        return (object) [
            'orders' => collect($body->orders),
            'hasMoreResults' => $pageNumber < (int) $body->paginationResult->pageCount,
        ];
    }
    
    public function updateOrder(Order $order)
    {
        $response = $this->client()->post("/orders/v3/$order->marketplace_technical_code/$order->marketplace_account_id/$order->external_id/ShipOrder?userName=info@milanihome.it", [
            'json' => [
                'order_Shipping_FulfillmentDate' => date('Y-m-d').'T'.date('H:i:s').'Z',
                'order_Shipping_CarrierName' => config('beezup.carriers_name')[$order->carrier] ?? config('beezup.carriers_name')[1],
                'order_Shipping_Method' => 'express'
            ],
        ]);

        $body = json_decode($response->getBody()->getContents());
     
        if(isset($body->errors->code)){
        	return false;
        }
        
        return true;
    }

    protected function client()
    {
        return new Client([
            'base_uri' => self::API_ENDPOINT,
            'headers' => [
                'Accept' => '*/*',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Content-Type' => 'application/json',
                'Ocp-Apim-Subscription-Key' => $this->apiKey
            ]
        ]);
    }
}
