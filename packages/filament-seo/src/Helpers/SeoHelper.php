<?php

namespace Webvio\FilamentSeo\Helpers;

use Webvio\FilamentSeo\Models\SeoMetadata;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class SeoHelper
{
    /**
     * Get SEO data for a model instance.
     */
    public static function getSeoForModel(Model $model): ?SeoMetadata
    {
        if (!method_exists($model, 'getCachedSeo')) {
            return null;
        }

        return $model->getCachedSeo();
    }

    /**
     * Get SEO data for current route.
     */
    public static function getSeoForCurrentRoute(): ?SeoMetadata
    {
        $routeName = Route::currentRouteName();
        
        if (!$routeName) {
            return null;
        }

        $cacheKey = "seo.route.{$routeName}";
        
        return Cache::remember($cacheKey, config('filament-seo.cache_duration', 3600), function () use ($routeName) {
            return SeoMetadata::where('seoable_type', 'route')
                ->where('seoable_id', $routeName)
                ->first();
        });
    }

    /**
     * Get SEO data for a static page by route name.
     */
    public static function getSeoForPage(string $routeName): ?SeoMetadata
    {
        $cacheKey = "seo.page.{$routeName}";
        
        return Cache::remember($cacheKey, config('filament-seo.cache_duration', 3600), function () use ($routeName) {
            return SeoMetadata::where('seoable_type', 'page')
                ->where('seoable_id', $routeName)
                ->first();
        });
    }

    /**
     * Clear all SEO caches.
     */
    public static function clearAllCaches(): void
    {
        Cache::flush();
    }

    /**
     * Generate default schema markup for a page.
     */
    public static function generateDefaultSchema(string $type = 'WebPage', array $data = []): array
    {
        $baseSchema = [
            '@context' => 'https://schema.org',
            '@type' => $type,
            'name' => $data['name'] ?? config('app.name'),
            'url' => $data['url'] ?? url()->current(),
        ];

        if (isset($data['description'])) {
            $baseSchema['description'] = $data['description'];
        }

        if (isset($data['image'])) {
            $baseSchema['image'] = $data['image'];
        }

        return $baseSchema;
    }

    /**
     * Generate organization schema markup.
     */
    public static function generateOrganizationSchema(array $data = []): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $data['name'] ?? config('filament-seo.organization.name', config('app.name')),
            'url' => $data['url'] ?? config('filament-seo.organization.url', url('/')),
            'logo' => $data['logo'] ?? config('filament-seo.organization.logo', asset('images/logo.png')),
            'sameAs' => $data['sameAs'] ?? config('filament-seo.organization.same_as', []),
        ];
    }

    /**
     * Generate breadcrumb schema markup.
     */
    public static function generateBreadcrumbSchema(array $items): array
    {
        $listItems = [];
        
        foreach ($items as $position => $item) {
            $listItems[] = [
                '@type' => 'ListItem',
                'position' => $position + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $listItems,
        ];
    }

    /**
     * Generate article schema markup.
     */
    public static function generateArticleSchema(Model $model, array $data = []): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $data['headline'] ?? $model->title ?? $model->name,
            'description' => $data['description'] ?? $model->excerpt ?? $model->description,
            'image' => $data['image'] ?? $model->image ?? $model->featured_image,
            'datePublished' => $data['datePublished'] ?? $model->created_at?->toIso8601String(),
            'dateModified' => $data['dateModified'] ?? $model->updated_at?->toIso8601String(),
            'author' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/logo.png'),
                ],
            ],
        ];
    }

    /**
     * Generate FAQ schema markup.
     */
    public static function generateFaqSchema(array $questions): array
    {
        $mainEntity = [];
        
        foreach ($questions as $question) {
            $mainEntity[] = [
                '@type' => 'Question',
                'name' => $question['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $question['answer'],
                ],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity,
        ];
    }

    /**
     * Get default meta title for a model.
     */
    public static function getDefaultTitle(Model $model): ?string
    {
        return $model->title ?? $model->name ?? null;
    }

    /**
     * Get default meta description for a model.
     */
    public static function getDefaultDescription(Model $model): ?string
    {
        return $model->excerpt ?? $model->description ?? null;
    }

    /**
     * Get default OG image for a model.
     */
    public static function getDefaultImage(Model $model): ?string
    {
        return $model->image ?? $model->featured_image ?? null;
    }

    /**
     * Truncate text to specified length for meta descriptions.
     */
    public static function truncateForMeta(string $text, int $length = 160): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length - 3) . '...';
    }

    /**
     * Clean text for meta tags (remove HTML, extra spaces).
     */
    public static function cleanMetaText(string $text): string
    {
        $text = strip_tags($text);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        
        return $text;
    }
}
