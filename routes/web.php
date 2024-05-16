<?php

use App\Http\Controllers\DiscountImportController;
use App\Http\Controllers\GoogleCloudService;
use Illuminate\Support\Facades\Route;

Route::get('/import-product-images', [GoogleCloudService::class, 'importImage']);

Route::group(['namespace' => 'App\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix() . '/ecommerce', 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'discount-import', 'as' => 'ecommerce.discount-import.'], function () {
            Route::get('/', [
                'as'   => 'index',
                'uses' => 'DiscountImportController@index',
            ]);
            Route::post('/import', [
                'as'   => 'import',
                'uses' => 'DiscountImportController@import',
            ]);
        });
    });
});


use GuzzleHttp\Client;
use App\Services\BeezupClient;

Route::get('test-update', function(){
		$beezup = new BeezupClient;
		
		try{
			
	        $response = $beezup->client()->post("/orders/v3/Amazon/21215/8D61A57C5A67000559f1648fd2e5d88bacf590d3c1a02b6/ShipOrder?userName=info@milanihome.it", [
	            'json' => [
	                'order_Shipping_FulfillmentDate' => date('Y-m-d').'T'.date('H:i:s', mktime(date('H')-2,date('i')-1,date('s'),0,0,0)).'Z',
	                'order_Shipping_CarrierName' => 'BRT',		//config('beezup.carriers_name')[$order->carrier] ?? config('beezup.carriers_name')[1],
	                'order_Shipping_Method' => 'express'
	            ],
	        ]);

	        $code = json_decode($response->getResponse()->getStatusCode(),true);
	        	        
	    }
	    catch(\GuzzleHttp\Exception\RequestException $e){
	    	\Log::error('Error update order to Beezup: code->'.json_decode($e->getResponse()->getBody(),true)['errors'][0]['code'].' - message->'.json_decode($e->getResponse()->getBody(),true)['errors'][0]['message']);
	    	return false;
	    }
	    
	    return true;
	            
});