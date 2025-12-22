<?php

namespace Webvio\DynamicSitemap\Http\Controllers;

use Illuminate\Http\Response;
use Webvio\DynamicSitemap\Services\SitemapManager;

class SitemapIndexController
{
    protected SitemapManager $sitemapManager;

    public function __construct(SitemapManager $sitemapManager)
    {
        $this->sitemapManager = $sitemapManager;
    }

    /**
     * Display the sitemap index.
     */
    public function __invoke(): Response
    {
        $xml = $this->sitemapManager->getIndex();

        return response($xml, 200, config('dynamic-sitemap.headers'));
    }
}
