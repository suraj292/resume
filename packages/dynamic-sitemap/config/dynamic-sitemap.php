<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for your sitemap. Defaults to APP_URL.
    |
    */

    'base_url' => env('SITEMAP_BASE_URL', env('APP_URL')),

    /*
    |--------------------------------------------------------------------------
    | Sitemap Index Path
    |--------------------------------------------------------------------------
    |
    | The route path for the main sitemap index.
    |
    */

    'index_path' => env('SITEMAP_INDEX_PATH', '/sitemap.xml'),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Enable caching to improve performance. Cache will be automatically
    | cleared when related models are created/updated/deleted.
    |
    */

    'cache' => [
        'enabled' => true,
        'ttl' => env('SITEMAP_CACHE_TTL', 3600), // 1 hour in seconds
        'key_prefix' => 'sitemap',
    ],

    /*
    |--------------------------------------------------------------------------
    | Static Routes Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for static routes sitemap (/sitemap-pages.xml)
    |
    */

    'static_routes' => [
        'enabled' => false, // Disabled to use custom sections instead
        'path' => '/sitemap-pages.xml',
        'changefreq' => 'weekly',
        'priority' => 0.8,
        
        // Routes to exclude from sitemap
        'exclude_patterns' => [
            'filament.*',
            'admin.*',
            'login',
            'register',
            'password.*',
            'debugbar.*',
            '_ignition.*',
            'livewire.*',
        ],
        
        // Only include routes with these methods
        'allowed_methods' => ['GET', 'HEAD'],
        
        // Only include named routes
        'named_only' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Dynamic Sections Configuration
    |--------------------------------------------------------------------------
    |
    | Configure dynamic content sitemaps for your models.
    | Each section will generate its own sitemap file.
    |
    */

    'sections' => [
        
        // Companies sitemap
        'companies' => [
            'enabled' => true,
            'type' => 'model',
            'model' => \App\Models\Company::class,
            'route' => 'company.show',
            'route_params' => ['companyName'], // Route parameter names
            'route_param_column' => 'name', // Model column to use for route param
            'path' => '/sitemap-companies.xml',
            'changefreq' => 'daily',
            'priority' => 0.9,
            'date_column' => 'updated_at',
            'query_scope' => 'published', // Optional: model scope to filter records
            'chunk_size' => 1000, // Process records in chunks for large datasets
        ],

        // Home page sitemap
        'home' => [
            'enabled' => true,
            'type' => 'routes',
            'routes' => [
                [
                    'url' => '/',
                    'lastmod' => now()->toW3cString(),
                    'changefreq' => 'daily',
                    'priority' => 1.0,
                ],
            ],
            'path' => '/sitemap-home.xml',
        ],

        // Legal pages sitemap
        'legal' => [
            'enabled' => true,
            'type' => 'routes',
            'routes' => [
                [
                    'url' => '/privacy-policy',
                    'lastmod' => now()->toW3cString(),
                    'changefreq' => 'monthly',
                    'priority' => 0.6,
                ],
                [
                    'url' => '/terms-and-conditions',
                    'lastmod' => now()->toW3cString(),
                    'changefreq' => 'monthly',
                    'priority' => 0.6,
                ],
                [
                    'url' => '/about-us',
                    'lastmod' => now()->toW3cString(),
                    'changefreq' => 'monthly',
                    'priority' => 0.7,
                ],
                [
                    'url' => '/contact-us',
                    'lastmod' => now()->toW3cString(),
                    'changefreq' => 'monthly',
                    'priority' => 0.7,
                ],
            ],
            'path' => '/sitemap-legal.xml',
        ],

        // Blog posts sitemap (example - disabled by default)
        'blog' => [
            'enabled' => false,
            'type' => 'model',
            'model' => null, // Replace with your blog model: \App\Models\BlogPost::class
            'route' => 'blog.show',
            'route_params' => ['slug'],
            'route_param_column' => 'slug',
            'path' => '/sitemap-blog.xml',
            'changefreq' => 'daily',
            'priority' => 0.8,
            'date_column' => 'updated_at',
            'chunk_size' => 1000,
        ],

        // Products sitemap (example - disabled by default)
        'products' => [
            'enabled' => false,
            'type' => 'model',
            'model' => null, // Replace with your product model: \App\Models\Product::class
            'route' => 'products.show',
            'route_params' => ['id'],
            'route_param_column' => 'id',
            'path' => '/sitemap-products.xml',
            'changefreq' => 'daily',
            'priority' => 0.9,
            'date_column' => 'updated_at',
            'chunk_size' => 1000,
        ],

        // Custom routes sitemap (example)
        'custom' => [
            'enabled' => false,
            'type' => 'routes',
            'routes' => [
                // Define custom routes manually
                // [
                //     'url' => '/special-page',
                //     'lastmod' => '2025-01-01',
                //     'changefreq' => 'monthly',
                //     'priority' => 0.5,
                // ],
            ],
            'path' => '/sitemap-custom.xml',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | XML Headers
    |--------------------------------------------------------------------------
    |
    | HTTP headers to send with sitemap responses
    |
    */

    'headers' => [
        'Content-Type' => 'application/xml; charset=utf-8',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Values
    |--------------------------------------------------------------------------
    |
    | Default values for sitemap entries
    |
    */

    'defaults' => [
        'changefreq' => 'weekly',
        'priority' => 0.5,
    ],

];
