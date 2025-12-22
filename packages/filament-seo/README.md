# Filament SEO

Complete SEO meta tag management package for Laravel Filament with polymorphic support. Easily add SEO fields to any Filament resource - blogs, companies, products, static pages, or any custom model.

## Features

✅ **Polymorphic SEO** - Attach to any model (Blog, Company, Product, Page, etc.)  
✅ **Complete Meta Tags** - Title, Description, Keywords  
✅ **Open Graph** - Social sharing for Facebook, LinkedIn  
✅ **Twitter Cards** - Optimized Twitter sharing  
✅ **Schema.org JSON-LD** - Structured data markup  
✅ **Technical SEO** - Robots meta, Canonical URLs, Sitemap priority  
✅ **Intelligent Fallbacks** - Uses model data when SEO fields are empty  
✅ **Performance Caching** - Automatic caching with smart invalidation  
✅ **Filament Integration** - Beautiful tabbed form component  
✅ **Image Uploads** - OG and Twitter images with aspect ratio validation  
✅ **Blade Component** - Easy meta tag rendering  
✅ **SEO Helper** - Utility functions and schema generators  

## Requirements

- PHP 8.1+
- Laravel 10.x, 11.x, or 12.x
- Filament 3.x or 4.x

## Installation

Install via Composer:

```bash
composer require webvio/filament-seo
```

Publish and run migrations:

```bash
php artisan vendor:publish --tag="filament-seo-migrations"
php artisan migrate
```

Publish configuration (optional):

```bash
php artisan vendor:publish --tag="filament-seo-config"
```

Publish views (optional):

```bash
php artisan vendor:publish --tag="filament-seo-views"
```

## Quick Start

### 1. Add SEO to Your Model

```php
use Webvio\FilamentSeo\Traits\HasSeo;

class BlogPost extends Model
{
    use HasSeo;
    
    // Optional: Customize fallback methods
    public function getSeoTitleFallback(): ?string
    {
        return $this->title;
    }
    
    public function getSeoDescriptionFallback(): ?string
    {
        return $this->excerpt;
    }
    
    public function getSeoImageFallback(): ?string
    {
        return $this->featured_image;
    }
}
```

### 2. Add to Filament Resource

```php
use Webvio\FilamentSeo\Forms\Components\SeoForm;

public static function form(Schema $schema): Schema
{
    return $schema->schema([
        // Your existing form fields...
        
        SeoForm::make(), // Full SEO form
        // OR
        SeoForm::minimal(), // Minimal SEO form
    ]);
}
```

### 3. Handle SEO Data in Resource Pages

**CreateRecord Page:**

```php
use Webvio\FilamentSeo\Concerns\HandlesFilamentSeo;

class CreateBlogPost extends CreateRecord
{
    use HandlesFilamentSeo;
    
    protected static string $resource = BlogPostResource::class;
}
```

**EditRecord Page:**

```php
use Webvio\FilamentSeo\Concerns\HandlesFilamentSeo;

class EditBlogPost extends EditRecord
{
    use HandlesFilamentSeo;
    
    protected static string $resource = BlogPostResource::class;
}
```

### 4. Pass SEO to Views

```php
public function show(BlogPost $post)
{
    $seo = $post->getCachedSeo();
    
    return view('blog.show', compact('post', 'seo'));
}
```

### 5. Render Meta Tags in Blade

```blade
<!DOCTYPE html>
<html>
<head>
    <x-filament-seo::meta-tags :seo="$seo" />
    
    <!-- Your other head tags -->
</head>
<body>
    @yield('content')
</body>
</html>
```

## Usage Examples

### Blog Posts

```php
class BlogPost extends Model
{
    use HasSeo;
    
    public function getSeoTitleFallback(): ?string
    {
        return $this->title;
    }
    
    public function getSeoDescriptionFallback(): ?string
    {
        return $this->excerpt ?? strip_tags(substr($this->content, 0, 160));
    }
}
```

### E-commerce Products

```php
class Product extends Model
{
    use HasSeo;
    
    public function getSeoTitleFallback(): ?string
    {
        return $this->name . ' - ' . $this->category->name;
    }
    
    public function getSeoDescriptionFallback(): ?string
    {
        return $this->short_description;
    }
    
    public function getSeoImageFallback(): ?string
    {
        return $this->images()->first()?->url;
    }
}
```

### Static Pages

```php
class Page extends Model
{
    use HasSeo;
    
    public function getSeoTitleFallback(): ?string
    {
        return $this->title;
    }
}
```

### Company Directory

```php
class Company extends Model
{
    use HasSeo;
    
    public function getSeoTitleFallback(): ?string
    {
        return $this->name . ' - ' . $this->category->name;
    }
    
    public function getSeoDescriptionFallback(): ?string
    {
        return strip_tags(substr($this->description, 0, 160));
    }
}
```

## Configuration

Edit `config/filament-seo.php` to customize defaults:

```php
return [
    'default_title' => 'Your Site Name',
    'default_description' => 'Your default description',
    'default_og_image' => '/images/default-og.jpg',
    'twitter_site' => '@yourhandle',
    'append_site_name' => true,
    'title_separator' => ' - ',
];
```

## SEO Helper Functions

```php
use Webvio\FilamentSeo\Facades\SeoHelper;

// Get SEO for a model
$seo = SeoHelper::getSeoForModel($model);

// Generate schema markup
$schema = SeoHelper::generateArticleSchema($article, [
    'headline' => $article->title,
    'image' => $article->image,
]);

// FAQ Schema
$faqSchema = SeoHelper::generateFaqSchema([
    ['question' => 'Question 1?', 'answer' => 'Answer 1'],
    ['question' => 'Question 2?', 'answer' => 'Answer 2'],
]);

// Organization Schema
$orgSchema = SeoHelper::generateOrganizationSchema([
    'name' => 'Your Company',
    'url' => 'https://yoursite.com',
    'logo' => 'https://yoursite.com/logo.png',
]);

// Breadcrumb Schema
$breadcrumbSchema = SeoHelper::generateBreadcrumbSchema([
    ['name' => 'Home', 'url' => url('/')],
    ['name' => 'Blog', 'url' => url('/blog')],
    ['name' => $post->title, 'url' => url('/blog/'.$post->slug)],
]);

// Text utilities
$cleaned = SeoHelper::cleanMetaText($html);
$truncated = SeoHelper::truncateForMeta($text, 160);
```

## Filament Form Options

### Full Form with All Tabs

```php
SeoForm::make()
```

Includes:
- Basic Meta (Title, Description, Keywords)
- Open Graph (Social sharing)
- Twitter Card
- Technical SEO (Robots, Canonical, Priority)
- Schema Markup

### Minimal Form

```php
SeoForm::minimal()
```

Includes only:
- Meta Title
- Meta Description
- Social Share Image
- Index Toggle

### Custom Form

```php
use Webvio\FilamentSeo\Forms\Components\SeoForm;

SeoForm::make()
    ->collapsible()
    ->collapsed(true)
```

## Blade Component Options

```blade
{{-- Basic usage --}}
<x-filament-seo::meta-tags :seo="$seo" />

{{-- With custom defaults --}}
<x-filament-seo::meta-tags 
    :seo="$seo"
    :defaultTitle="'Custom Default Title'"
    :defaultDescription="'Custom description'"
/>
```

## Model Methods

```php
// Get or create SEO metadata
$seo = $model->getOrCreateSeo();

// Get cached SEO (recommended)
$seo = $model->getCachedSeo();

// Update SEO
$model->updateSeo([
    'meta_title' => 'New Title',
    'meta_description' => 'New description',
    'og_image' => 'path/to/image.jpg',
]);

// Clear SEO cache
$model->clearSeoCache();

// Get SEO with relationship
$model->load('seo');
$model->seo; // Eloquent relationship
```

## Database Structure

The package creates a `seo_metadata` table with polymorphic relationships:

- `seoable_type` & `seoable_id` - Polymorphic relationship
- `meta_title`, `meta_description`, `meta_keywords`
- `og_title`, `og_description`, `og_image`, `og_type`
- `twitter_card`, `twitter_title`, `twitter_description`, `twitter_image`, `twitter_site`, `twitter_creator`
- `canonical_url`, `index`, `follow`, `locale`, `priority`
- `schema_markup` - JSON field for custom schema

## Environment Variables

```env
SEO_DEFAULT_TITLE="Your Site Name"
SEO_DEFAULT_DESCRIPTION="Your default description"
SEO_DEFAULT_KEYWORDS="keyword1, keyword2"
SEO_DEFAULT_OG_IMAGE="/images/default-og.jpg"
SEO_TWITTER_SITE="@yoursite"
SEO_TWITTER_CREATOR="@yourcreator"
SEO_APPEND_SITE_NAME=true
SEO_TITLE_SEPARATOR=" - "
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email info@webvio.com instead of using the issue tracker.

## Credits

- [Webvio](https://github.com/webvio)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
