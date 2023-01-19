<?php

namespace App\Providers;

use App\Http\Controllers\BeezupOrdersController;
use Illuminate\Support\ServiceProvider;

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

    public function boot()
    {
        $beez = new BeezupOrdersController();
        dd($beez->getOrders());
    }
}
