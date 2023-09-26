<?php

Route::group(['namespace' => 'Botble\Klarna\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::get('payment/klarna/status', 'KlarnaController@getCallback')->name('payments.klarna.status');
});
