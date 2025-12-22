<?php

namespace Webvio\DynamicSitemap;

use Illuminate\Support\ServiceProvider;
use Webvio\DynamicSitemap\Services\SitemapGenerator;
use Webvio\DynamicSitemap\Services\SitemapManager;
use Webvio\DynamicSitemap\Observers\ModelObserver;

class DynamicSitemapServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/dynamic-sitemap.php',
            'dynamic-sitemap'
        );

        // Register services
        $this->app->singleton(SitemapGenerator::class);
        $this->app->singleton(SitemapManager::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'dynamic-sitemap');

        // Publish config
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/dynamic-sitemap.php' => config_path('dynamic-sitemap.php'),
            ], 'dynamic-sitemap-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/dynamic-sitemap'),
            ], 'dynamic-sitemap-views');
        }

        // Register model observers
        $this->registerModelObservers();
    }

    /**
     * Register model observers for automatic cache clearing.
     */
    protected function registerModelObservers(): void
    {
        $sections = config('dynamic-sitemap.sections', []);

        foreach ($sections as $section) {
            if (
                ($section['enabled'] ?? true) &&
                ($section['type'] ?? null) === 'model' &&
                !empty($section['model']) &&
                class_exists($section['model'])
            ) {
                $section['model']::observe(ModelObserver::class);
            }
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            SitemapGenerator::class,
            SitemapManager::class,
        ];
    }
}
