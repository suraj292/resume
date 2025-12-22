<?php

namespace Webvio\DynamicSitemap\Services;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class SitemapGenerator
{
    /**
     * Generate sitemap index XML.
     */
    public function generateIndex(): string
    {
        $sections = $this->getEnabledSections();
        $baseUrl = config('dynamic-sitemap.base_url');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($sections as $key => $section) {
            $lastmod = $this->getSectionLastModified($key, $section);
            
            $xml .= '<sitemap>';
            $xml .= '<loc>' . htmlspecialchars($baseUrl . $section['path']) . '</loc>';
            if ($lastmod) {
                $xml .= '<lastmod>' . $lastmod . '</lastmod>';
            }
            $xml .= '</sitemap>';
        }

        $xml .= '</sitemapindex>';

        return $xml;
    }

    /**
     * Generate static routes sitemap XML.
     */
    public function generateStaticRoutes(): string
    {
        $cacheKey = config('dynamic-sitemap.cache.key_prefix') . '.static_routes';
        $cacheTtl = config('dynamic-sitemap.cache.ttl');

        if (config('dynamic-sitemap.cache.enabled')) {
            return Cache::remember($cacheKey, $cacheTtl, function () {
                return $this->buildStaticRoutesXml();
            });
        }

        return $this->buildStaticRoutesXml();
    }

    /**
     * Generate model-based sitemap XML.
     */
    public function generateModelSitemap(string $sectionKey, array $config): string
    {
        $cacheKey = config('dynamic-sitemap.cache.key_prefix') . ".section.{$sectionKey}";
        $cacheTtl = config('dynamic-sitemap.cache.ttl');

        if (config('dynamic-sitemap.cache.enabled')) {
            return Cache::remember($cacheKey, $cacheTtl, function () use ($config) {
                return $this->buildModelSitemapXml($config);
            });
        }

        return $this->buildModelSitemapXml($config);
    }

    /**
     * Generate custom routes sitemap XML.
     */
    public function generateCustomRoutes(array $config): string
    {
        $baseUrl = config('dynamic-sitemap.base_url');

        $xml = $this->getXmlHeader();

        foreach ($config['routes'] as $route) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($baseUrl . $route['url']) . '</loc>';
            
            if (isset($route['lastmod'])) {
                $xml .= '<lastmod>' . $route['lastmod'] . '</lastmod>';
            }
            
            if (isset($route['priority'])) {
                $xml .= '<priority>' . $route['priority'] . '</priority>';
            }
            
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Build static routes XML.
     */
    protected function buildStaticRoutesXml(): string
    {
        $routes = $this->getStaticRoutes();
        $baseUrl = config('dynamic-sitemap.base_url');
        $config = config('dynamic-sitemap.static_routes');

        $xml = $this->getXmlHeader();

        foreach ($routes as $route) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($baseUrl . $route['uri']) . '</loc>';
            $xml .= '<lastmod>' . now()->toW3cString() . '</lastmod>';
            
            // Only add priority if it's set in config
            if (isset($config['priority'])) {
                $xml .= '<priority>' . $config['priority'] . '</priority>';
            }
            
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Build model sitemap XML.
     */
    protected function buildModelSitemapXml(array $config): string
    {
        $modelClass = $config['model'];
        $baseUrl = config('dynamic-sitemap.base_url');

        if (!class_exists($modelClass)) {
            return $this->getXmlHeader() . '</urlset>';
        }

        $xml = $this->getXmlHeader();

        $query = $modelClass::query();

        // Apply scope if specified
        if (isset($config['query_scope']) && method_exists($modelClass, 'scope' . ucfirst($config['query_scope']))) {
            $query = $query->{$config['query_scope']}();
        }

        // Process in chunks for large datasets
        $chunkSize = $config['chunk_size'] ?? 1000;

        $query->chunk($chunkSize, function ($records) use (&$xml, $config, $baseUrl) {
            foreach ($records as $record) {
                // Build route parameters
                $routeParams = [];
                if (isset($config['route_param_values']) && is_callable($config['route_param_values'])) {
                    $routeParams = call_user_func($config['route_param_values'], $record);
                } else {
                    foreach ($config['route_params'] as $param) {
                        $column = $param === $config['route_params'][0] 
                            ? $config['route_param_column'] 
                            : $param;
                        $routeParams[$param] = $record->$column;
                    }
                }

                // Generate URL
                try {
                    $url = route($config['route'], $routeParams, false);
                } catch (\Exception $e) {
                    continue; // Skip if route doesn't exist
                }

                $xml .= '<url>';
                $xml .= '<loc>' . htmlspecialchars($baseUrl . $url) . '</loc>';
                
                // Last modified date
                if (isset($config['date_column']) && $record->{$config['date_column']}) {
                    $xml .= '<lastmod>' . Carbon::parse($record->{$config['date_column']})->toW3cString() . '</lastmod>';
                }
                
                // Only add priority if it's set in config
                if (isset($config['priority'])) {
                    $xml .= '<priority>' . $config['priority'] . '</priority>';
                }
                
                $xml .= '</url>';
            }
        });

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Get static routes from application.
     */
    protected function getStaticRoutes(): array
    {
        $config = config('dynamic-sitemap.static_routes');
        $routes = [];

        foreach (Route::getRoutes() as $route) {
            // Skip if not named and named_only is true
            if ($config['named_only'] && !$route->getName()) {
                continue;
            }

            // Skip if method not allowed
            if (!empty($config['allowed_methods'])) {
                $hasAllowedMethod = false;
                foreach ($route->methods() as $method) {
                    if (in_array($method, $config['allowed_methods'])) {
                        $hasAllowedMethod = true;
                        break;
                    }
                }
                if (!$hasAllowedMethod) {
                    continue;
                }
            }

            // Skip if matches exclude pattern
            $routeName = $route->getName() ?? '';
            if ($this->shouldExcludeRoute($routeName, $config['exclude_patterns'])) {
                continue;
            }

            // Skip routes with parameters
            if (preg_match('/\{.*\}/', $route->uri())) {
                continue;
            }

            $routes[] = [
                'uri' => '/' . ltrim($route->uri(), '/'),
                'name' => $routeName,
            ];
        }

        return $routes;
    }

    /**
     * Check if route should be excluded.
     */
    protected function shouldExcludeRoute(string $routeName, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            // Convert wildcard pattern to regex
            $regex = '/^' . str_replace('\*', '.*', preg_quote($pattern, '/')) . '$/';
            if (preg_match($regex, $routeName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get enabled sections.
     */
    protected function getEnabledSections(): array
    {
        $sections = config('dynamic-sitemap.sections', []);
        
        // Add static routes
        if (config('dynamic-sitemap.static_routes.enabled')) {
            $sections = array_merge([
                'static' => config('dynamic-sitemap.static_routes')
            ], $sections);
        }

        return array_filter($sections, function ($section) {
            return $section['enabled'] ?? true;
        });
    }

    /**
     * Get last modified date for a section.
     */
    protected function getSectionLastModified(string $key, array $section): ?string
    {
        // Check if a static lastmod is defined in the section config
        if (isset($section['lastmod'])) {
            return $section['lastmod'];
        }

        if (($section['type'] ?? 'model') === 'model' && isset($section['model']) && class_exists($section['model'])) {
            $modelClass = $section['model'];
            $dateColumn = $section['date_column'] ?? 'updated_at';

            try {
                $query = $modelClass::query();

                // Apply scope if specified
                if (isset($section['query_scope']) && method_exists($modelClass, 'scope' . ucfirst($section['query_scope']))) {
                    $query = $query->{$section['query_scope']}();
                }

                $latest = $query->latest($dateColumn)->first();

                if ($latest && $latest->$dateColumn) {
                    return Carbon::parse($latest->$dateColumn)->toW3cString();
                }
            } catch (\Exception $e) {
                // Silent fail
            }
        }

        return now()->toW3cString();
    }

    /**
     * Get XML header.
     */
    protected function getXmlHeader(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>' .
               '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    }

    /**
     * Clear sitemap cache.
     */
    public function clearCache(?string $section = null): void
    {
        $prefix = config('dynamic-sitemap.cache.key_prefix');

        if ($section) {
            Cache::forget("{$prefix}.section.{$section}");
        } else {
            // Clear all sitemap caches
            Cache::forget("{$prefix}.index");
            Cache::forget("{$prefix}.static_routes");
            
            foreach (array_keys(config('dynamic-sitemap.sections', [])) as $key) {
                Cache::forget("{$prefix}.section.{$key}");
            }
        }
    }
}
