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

