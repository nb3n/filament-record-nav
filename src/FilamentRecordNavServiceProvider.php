<?php

namespace Nben\FilamentRecordNav;

use Illuminate\Support\ServiceProvider;

/**
 * Registers the package with the Laravel application.
 *
 * Handles two responsibilities:
 *  1. register() - merges the package config so defaults are always available
 *     even if the user has not published the config file.
 *  2. boot()     - publishes the config file to the application's config/
 *     directory when running in the console (php artisan vendor:publish).
 */
class FilamentRecordNavServiceProvider extends ServiceProvider
{
    /**
     * Publish the config file so users can customise order_column and directions.
     *
     * Run: php artisan vendor:publish --tag=filament-record-nav-config
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/filament-record-nav.php' => config_path('filament-record-nav.php'),
            ], 'filament-record-nav-config');
        }
    }

    /**
     * Merge package config with application config.
     *
     * mergeConfigFrom() ensures the package defaults are available under the
     * 'filament-record-nav' key even before the user publishes the config.
     * If the user has published and customised the config, their values take
     * precedence over the package defaults.
     */
    public function register(): void
    {
        $configPath = __DIR__ . '/../config/filament-record-nav.php';

        if (file_exists($configPath)) {
            $this->mergeConfigFrom($configPath, 'filament-record-nav');
        }
    }
}