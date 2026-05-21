<?php

namespace App\Providers;

use App\Http\View\Composers\FactoryComposer;
use App\Services\FactoryConfigService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // FactoryConfigService sebagai singleton
        $this->app->singleton(FactoryConfigService::class);
    }

    public function boot(): void
    {
        // Inject $factories into every view that uses layouts.admin
        // so the layout's factory picker, tvDropdown, tvPickerSheet are always dynamic
        View::composer([
            'layouts.admin',
            'admin.group.index',
            'admin.section.index',
            'admin.status.index',
            'admin.tv_picker',
        ], FactoryComposer::class);
    }
}
