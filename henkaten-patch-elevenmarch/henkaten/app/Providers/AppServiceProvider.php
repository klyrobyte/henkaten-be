<?php

namespace App\Providers;

use App\Services\FactoryConfigService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // FactoryConfigService sebagai singleton
        // (menggantikan const factoryConfig & circleCountConfig di JS)
        $this->app->singleton(FactoryConfigService::class);
    }

    public function boot(): void
    {
        //
    }
}
