<?php

namespace App\Providers;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Swagger\Client\Api\MarketplacesOrdersListApi;
use Swagger\Client\Configuration;
use Swagger\Client\Model\OrderListRequest;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
//    public function boot(): void
//    {
//
////
//// A very simple PHP example that sends a HTTP POST to a remote site
////
//
//        $ch = curl_init();
//
//        curl_setopt( $ch, CURLOPT_ENCODING, 'UTF-8' );
//        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );
//        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
//        curl_setopt($ch, CURLOPT_URL, "https://api.beezup.com/v2/public/security/login");
//        curl_setopt($ch, CURLOPT_POST, 1);
//        curl_setopt($ch, CURLOPT_POSTFIELDS,  'login=info@milanihome.it&password=Milani2020');
//        curl_setopt($ch, CURLOPT_HTTPHEADER, ['ASDASDASD']);
//
//// In real life you should use something like:
//// curl_setopt($ch, CURLOPT_POSTFIELDS,
////          http_build_query(array('postvar1' => 'value1')));
//
//// Receive server response ...
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//        $server_output1 = curl_exec($ch);
//
//        curl_close($ch);
//
//
//
//
//
//
//        $headers = array(
//            'Content-Type: application/json',
//            'Ocp-Apim-Subscription-Key: 8D9B566B900551574180f1ab4df49479f87b5f5dcae8592'
//        );
//
//
//        $ch = curl_init();
//
//        curl_setopt( $ch, CURLOPT_ENCODING, 'UTF-8' );
//        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );
//        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
//        curl_setopt($ch, CURLOPT_URL, "https://api.beezup.com/orders/v3/list/full");
//        curl_setopt($ch, CURLOPT_POST, 1);
////        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET');
//        curl_setopt($ch, CURLOPT_POSTFIELDS,  'login=info@milanihome.it&password=Milani2020');
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//
//// In real life you should use something like:
//// curl_setopt($ch, CURLOPT_POSTFIELDS,
////          http_build_query(array('postvar1' => 'value1')));
//
//// Receive server response ...
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//        $server_output2 = curl_exec($ch);
//
//        curl_close($ch);
//
//// Further processing ...
//        dd($server_output2 );
//
//    }

    public function boot()
    {
        //
}
}
