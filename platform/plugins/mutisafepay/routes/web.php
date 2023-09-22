<?php

Route::group(['namespace' => 'Botble\MultiSafepay\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::get('payment/multisafepay/status', 'MultiSafepayController@getCallback')->name('payments.multisafepay.status');
});
