<?php

namespace App\Providers;

use App\Services\GoogleCloudService;
use Illuminate\Support\ServiceProvider;

class GoogleDriveServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('gcs', function ($app) {
            return new GoogleCloudService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        app('gcs')->importFile();
        app('gcs')->exportFile();
    }
}
