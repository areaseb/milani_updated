<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use App\Http\Controllers\Api\BeezupController;
use Illuminate\Support\Facades\Route;

Route::get('beezup/catalog', [BeezupController::class, 'catalog'])->name('api.beezup.catalog');
