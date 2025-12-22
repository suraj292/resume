# Dynamic Sitemap for Laravel + Filament

A production-ready Laravel package for automatic dynamic sitemap generation with Filament admin integration.

## Features

- 🗺️ **Automatic Sitemap Generation** - Main sitemap index (`/sitemap.xml`) with sub-sitemaps
- 📄 **Static Routes Support** - Auto-detect GET routes from `routes/web.php`
- 🗃️ **Model-Based Sitemaps** - Generate sitemaps for Companies, Blog Posts, Products, etc.
- ⚡ **Smart Caching** - Configurable TTL with automatic cache invalidation
- 🔄 **Auto-Update** - Sitemaps refresh automatically when models change
- 🎨 **Filament Admin UI** - Manage sitemap settings from your admin panel
- ✅ **XML Standards Compliant** - Follows sitemaps.org specifications
- 🚫 **Route Exclusion** - Exclude admin, login, and custom routes
- 🔧 **Highly Configurable** - Control changefreq, priority, and more

## Requirements

- PHP 8.1 or higher
- Laravel 10.x or 11.x or 12.x
- Filament 3.x or 4.x (optional, for admin UI)

## Installation

### 1. Install via Composer

```bash
composer require webvio/dynamic-sitemap
```

### 2. Publish Configuration

```bash
php artisan vendor:publish --tag=dynamic-sitemap-config
```

This creates `config/dynamic-sitemap.php` with all configuration options.

### 3. (Optional) Publish Views

If you want to customize the Filament admin page:

```bash
php artisan vendor:publish --tag=dynamic-sitemap-views
```

## Configuration

Edit `config/dynamic-sitemap.php`:

```php
return [
    // Enable/disable sitemap generation
    'enabled' => true,

    // Base URL for your site
    'base_url' => env('APP_URL', 'http://localhost'),

    // Main sitemap index path
    'index_path' => '/sitemap.xml',

    // Caching settings
    'cache' => [
        'enabled' => true,
        'ttl' => 3600, // 1 hour
        'key_prefix' => 'sitemap',
    ],

    // Static routes from routes/web.php
    'static_routes' => [
        'enabled' => true,
        'path' => '/sitemap-pages.xml',
        'changefreq' => 'weekly',
        'priority' => 0.5,
        'exclude_patterns' => [
            'filament.*',  // Exclude Filament admin routes
            'login',
            'register',
            'password.*',
            'admin.*',
        ],
        'allowed_methods' => ['GET'],
        'named_only' => false,
    ],

    // Dynamic sections (models)
    'sections' => [
        'companies' => [
            'enabled' => true,
            'type' => 'model',
            'model' => \App\Models\Company::class,
            'route' => 'company.show',
            'route_params' => ['slug'],
            'path' => '/sitemap-companies.xml',
            'changefreq' => 'daily',
            'priority' => 0.9,
            'date_column' => 'updated_at',
            'chunk_size' => 1000,
        ],
        
        // Add more sections as needed
        'blog' => [
            'enabled' => false,
            'type' => 'model',
            'model' => \App\Models\BlogPost::class,
            'route' => 'blog.show',
            'route_params' => ['slug'],
            'path' => '/sitemap-blog.xml',
            'changefreq' => 'weekly',
            'priority' => 0.8,
            'date_column' => 'published_at',
            'chunk_size' => 1000,
        ],
    ],
];
```

## Usage

### Accessing Sitemaps

Once configured, your sitemaps are automatically available:

- **Main Sitemap Index**: `https://yoursite.com/sitemap.xml`
- **Static Pages**: `https://yoursite.com/sitemap-pages.xml`
- **Companies**: `https://yoursite.com/sitemap-companies.xml`
- **Blog Posts**: `https://yoursite.com/sitemap-blog.xml`

### Adding to robots.txt

Add this to your `public/robots.txt`:

```
User-agent: *
Sitemap: https://yoursite.com/sitemap.xml
```

### Filament Admin Integration

The package includes a Filament admin page at **Settings > Sitemap** where you can:

- Enable/disable sitemap generation
- Configure caching settings
- Manage static route settings
- Add/edit dynamic sections
- Control changefreq and priority

### Manual Cache Clearing

Clear sitemap cache programmatically:

```php
use Webvio\DynamicSitemap\Services\SitemapManager;

// Clear all sitemap cache
app(SitemapManager::class)->clearCache();

// Clear cache for specific model
app(SitemapManager::class)->clearModelCache(\App\Models\Company::class);
```

### Adding Custom Models

To add a new model-based sitemap section:

1. **Via Config File** (Recommended for production):

Edit `config/dynamic-sitemap.php`:

```php
'sections' => [
    'products' => [
        'enabled' => true,
        'type' => 'model',
        'model' => \App\Models\Product::class,
        'route' => 'product.show',
        'route_params' => ['slug'],
        'path' => '/sitemap-products.xml',
        'changefreq' => 'daily',
        'priority' => 0.9,
        'date_column' => 'updated_at',
        'chunk_size' => 1000,
    ],
],
```

2. **Via Filament Admin** (Runtime only):

Go to **Settings > Sitemap > Dynamic Sections** and add a new section.

**Note**: Changes in Filament are runtime only. To persist, edit the config file.

### Automatic Cache Invalidation

The package automatically clears sitemap cache when models are:
- Created
- Updated
- Deleted

This happens through the `ModelObserver` which is automatically registered for enabled model sections.

## Advanced Usage

### Custom Route Parameters

If your routes need multiple parameters:

```php
'route_params' => ['category_slug', 'product_slug'],
```

The package will look for `category_slug` and `product_slug` attributes on your model.

### Custom Date Columns

Use different date columns for lastmod:

```php
'date_column' => 'published_at', // or 'created_at', 'modified_at', etc.
```

### Large Datasets

For models with thousands of records, adjust chunk size:

```php
'chunk_size' => 5000, // Process 5000 records at a time
```

### Exclude Specific Routes

Add patterns to exclude routes from static sitemap:

```php
'exclude_patterns' => [
    'filament.*',
    'admin.*',
    'api.*',
    'dashboard',
    'profile',
],
```

## XML Output Example

**Sitemap Index** (`/sitemap.xml`):

```xml
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemap>
        <loc>https://yoursite.com/sitemap-pages.xml</loc>
        <lastmod>2025-01-15T10:30:00+00:00</lastmod>
    </sitemap>
    <sitemap>
        <loc>https://yoursite.com/sitemap-companies.xml</loc>
        <lastmod>2025-01-15T12:45:30+00:00</lastmod>
    </sitemap>
</sitemapindex>
```

**Company Sitemap** (`/sitemap-companies.xml`):

```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>https://yoursite.com/companies/acme-corp</loc>
        <lastmod>2025-01-15T12:45:30+00:00</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <!-- More URLs... -->
</urlset>
```

## Testing

```bash
# Visit sitemap index
curl https://yoursite.test/sitemap.xml

# Visit static pages sitemap
curl https://yoursite.test/sitemap-pages.xml

# Visit companies sitemap
curl https://yoursite.test/sitemap-companies.xml

# Submit to Google Search Console
# Go to: https://search.google.com/search-console
# Add sitemap: https://yoursite.com/sitemap.xml
```

## Troubleshooting

### Sitemap is empty

- Check `enabled` is `true` in config
- Verify routes are named (if `named_only` is true)
- Check exclude patterns aren't too broad

### Models not appearing

- Ensure model class exists and is correct
- Verify route name exists
- Check `route_params` match model attributes
- Enable section in config

### Cache not clearing

- Check model observer is registered
- Verify model is in enabled sections
- Manually clear cache: `app(SitemapManager::class)->clearCache()`

### Filament page not showing

- Clear config cache: `php artisan config:clear`
- Clear view cache: `php artisan view:clear`
- Ensure Filament is installed and configured

## Performance Tips

1. **Enable caching** for high-traffic sites
2. **Increase TTL** if content doesn't change frequently
3. **Adjust chunk_size** based on available memory
4. **Use specific exclude patterns** to reduce processing
5. **Disable unused sections** in config

## Credits

Developed by **Webvio**

## License

MIT License. See LICENSE file for details.

## Support

For issues, questions, or contributions, please visit:
https://github.com/webvio/dynamic-sitemap
