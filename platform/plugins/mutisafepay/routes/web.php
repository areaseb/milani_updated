<?php

Route::post('payment/multisafepay/status/post', 'Botble\MultiSafepay\Http\Controllers\MultiSafepayController@getCallbackPost')->name('payments.multisafepay.status.post');

Route::group(['namespace' => 'Botble\MultiSafepay\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::get('payment/multisafepay/status', 'MultiSafepayController@getCallback')->name('payments.multisafepay.status');
});
