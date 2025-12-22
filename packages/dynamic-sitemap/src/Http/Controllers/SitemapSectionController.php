<?php

namespace Webvio\DynamicSitemap\Http\Controllers;

use Illuminate\Http\Response;
use Webvio\DynamicSitemap\Services\SitemapManager;

class SitemapSectionController
{
    protected SitemapManager $sitemapManager;

    public function __construct(SitemapManager $sitemapManager)
    {
        $this->sitemapManager = $sitemapManager;
    }

    /**
     * Display a specific sitemap section.
     */
    public function __invoke(string $path): Response
    {
        // Add .xml extension for path lookup
        $fullPath = '/sitemap-' . $path . '.xml';

        $xml = $this->sitemapManager->getSectionByPath($fullPath);

        if (!$xml) {
            abort(404, 'Sitemap section not found');
        }

        return response($xml, 200, config('dynamic-sitemap.headers'));
    }
}
