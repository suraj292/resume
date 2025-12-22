<?php

namespace Webvio\FilamentSeo;

use Illuminate\Support\ServiceProvider;
use Webvio\FilamentSeo\Helpers\SeoHelper;

class FilamentSeoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/filament-seo.php', 'filament-seo'
        );

        $this->app->singleton('filament-seo.helper', function () {
            return new SeoHelper();
        });
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filament-seo');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/filament-seo.php' => config_path('filament-seo.php'),
            ], 'filament-seo-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'filament-seo-migrations');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/filament-seo'),
            ], 'filament-seo-views');
        }

        // Register Blade components
        $this->loadViewComponentsAs('filament-seo', [
            \Illuminate\Support\Facades\View::class,
        ]);
    }

    public function provides(): array
    {
        return ['filament-seo.helper'];
    }
}
