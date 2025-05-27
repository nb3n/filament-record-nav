<?php

namespace Nben\FilamentRecordNav;

use Illuminate\Support\ServiceProvider;

class FilamentRecordNavServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/filament-record-nav.php' => config_path('filament-record-nav.php'),
            ], 'filament-record-nav-config');
        }
    }

    public function register(): void
    {
        $configPath = __DIR__.'/../config/filament-record-nav.php';
        
        if (file_exists($configPath)) {
            $this->mergeConfigFrom($configPath, 'filament-record-nav');
        }
    }
}