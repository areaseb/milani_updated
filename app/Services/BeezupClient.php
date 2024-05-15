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
        $from = $from ?? Carbon::now()->subDays(7);
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
                'pageNumber' => $pageNumber,
                'beezUPOrderStatuses' => [
                	"InProgress"
                ]
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
    	if($order->source != 'WEB'){
    		
	    	try{
				
		        $response = $this->client()->post("/orders/v3/$order->marketplace_technical_code/$order->marketplace_account_id/$order->external_id/ShipOrder?userName=info@milanihome.it", [
		            'json' => [
		                'order_Shipping_FulfillmentDate' => date('Y-m-d').'T'.date('H:i:s', mktime(date('H')-2,date('i')-1,date('s'),0,0,0)).'Z',
		                'order_Shipping_CarrierName' => config('beezup.carriers_name')[$order->carrier] ?? config('beezup.carriers_name')[1],
		                'order_Shipping_Method' => 'express'
		            ],
		        ]);

		        $code = json_decode($response->getStatusCode(),true);
		        \Log::info('Update order Beezup: code->'.$code);
		        	        
		    }
		    catch(\GuzzleHttp\Exception\RequestException $e){
		    	\Log::error('Error update order to Beezup: code->'.json_decode($e->getResponse()->getBody(),true)['errors'][0]['code'].' - message->'.json_decode($e->getResponse()->getBody(),true)['errors'][0]['message']);
		    }
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
