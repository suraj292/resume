<?php

namespace Webvio\DynamicSitemap\Observers;

use Illuminate\Database\Eloquent\Model;
use Webvio\DynamicSitemap\Services\SitemapManager;

class ModelObserver
{
    protected SitemapManager $sitemapManager;

    public function __construct(SitemapManager $sitemapManager)
    {
        $this->sitemapManager = $sitemapManager;
    }

    /**
     * Handle the model "created" event.
     */
    public function created(Model $model): void
    {
        $this->clearModelCache($model);
    }

    /**
     * Handle the model "updated" event.
     */
    public function updated(Model $model): void
    {
        $this->clearModelCache($model);
    }

    /**
     * Handle the model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $this->clearModelCache($model);
    }

    /**
     * Clear sitemap cache for the given model.
     */
    protected function clearModelCache(Model $model): void
    {
        $this->sitemapManager->clearModelCache(get_class($model));
    }
}
