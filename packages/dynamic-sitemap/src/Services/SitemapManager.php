<?php

namespace Webvio\DynamicSitemap\Services;

use Illuminate\Support\Facades\Cache;

class SitemapManager
{
    protected SitemapGenerator $generator;

    public function __construct(SitemapGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Get sitemap index.
     */
    public function getIndex(): string
    {
        $cacheKey = config('dynamic-sitemap.cache.key_prefix') . '.index';
        $cacheTtl = config('dynamic-sitemap.cache.ttl');

        if (config('dynamic-sitemap.cache.enabled')) {
            return Cache::remember($cacheKey, $cacheTtl, function () {
                return $this->generator->generateIndex();
            });
        }

        return $this->generator->generateIndex();
    }

    /**
     * Get section sitemap.
     */
    public function getSection(string $sectionKey): ?string
    {
        // Check for static routes
        if ($sectionKey === 'static' || $sectionKey === 'pages') {
            return $this->generator->generateStaticRoutes();
        }

        // Get section config
        $config = config("dynamic-sitemap.sections.{$sectionKey}");

        if (!$config || !($config['enabled'] ?? true)) {
            return null;
        }

        // Generate based on type
        if ($config['type'] === 'model') {
            return $this->generator->generateModelSitemap($sectionKey, $config);
        }

        if ($config['type'] === 'routes') {
            return $this->generator->generateCustomRoutes($config);
        }

        return null;
    }

    /**
     * Get section by path.
     */
    public function getSectionByPath(string $path): ?string
    {
        // Normalize path
        $path = '/' . ltrim($path, '/');

        // Check static routes
        if ($path === config('dynamic-sitemap.static_routes.path')) {
            return $this->generator->generateStaticRoutes();
        }

        // Find matching section
        foreach (config('dynamic-sitemap.sections', []) as $key => $config) {
            if (($config['path'] ?? null) === $path && ($config['enabled'] ?? true)) {
                if ($config['type'] === 'model') {
                    return $this->generator->generateModelSitemap($key, $config);
                }

                if ($config['type'] === 'routes') {
                    return $this->generator->generateCustomRoutes($config);
                }
            }
        }

        return null;
    }

    /**
     * Clear all sitemap caches.
     */
    public function clearCache(?string $section = null): void
    {
        $this->generator->clearCache($section);
    }

    /**
     * Clear cache for a specific model.
     */
    public function clearModelCache(string $modelClass): void
    {
        foreach (config('dynamic-sitemap.sections', []) as $key => $config) {
            if (($config['model'] ?? null) === $modelClass) {
                $this->clearCache($key);
                // Also clear the index cache since section lastmod changed
                $this->clearCache();
                break;
            }
        }
    }

    /**
     * Get all enabled sections.
     */
    public function getEnabledSections(): array
    {
        $sections = [];

        // Add static routes
        if (config('dynamic-sitemap.static_routes.enabled')) {
            $sections['static'] = config('dynamic-sitemap.static_routes');
        }

        // Add configured sections
        foreach (config('dynamic-sitemap.sections', []) as $key => $config) {
            if ($config['enabled'] ?? true) {
                $sections[$key] = $config;
            }
        }

        return $sections;
    }

    /**
     * Check if sitemap is enabled.
     */
    public function isEnabled(): bool
    {
        return !empty($this->getEnabledSections());
    }
}
