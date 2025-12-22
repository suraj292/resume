<?php

namespace Webvio\FilamentSeo\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Webvio\FilamentSeo\Helpers\SeoHelper
 * 
 * @method static \Webvio\FilamentSeo\Models\SeoMetadata|null getSeoForModel(\Illuminate\Database\Eloquent\Model $model)
 * @method static \Webvio\FilamentSeo\Models\SeoMetadata|null getSeoForCurrentRoute()
 * @method static \Webvio\FilamentSeo\Models\SeoMetadata|null getSeoForPage(string $routeName)
 * @method static void clearAllCaches()
 * @method static array generateDefaultSchema(string $type = 'WebPage', array $data = [])
 * @method static array generateOrganizationSchema(array $data = [])
 * @method static array generateBreadcrumbSchema(array $items)
 * @method static array generateArticleSchema(\Illuminate\Database\Eloquent\Model $model, array $data = [])
 * @method static array generateFaqSchema(array $questions)
 * @method static string|null getDefaultTitle(\Illuminate\Database\Eloquent\Model $model)
 * @method static string|null getDefaultDescription(\Illuminate\Database\Eloquent\Model $model)
 * @method static string|null getDefaultImage(\Illuminate\Database\Eloquent\Model $model)
 * @method static string truncateForMeta(string $text, int $length = 160)
 * @method static string cleanMetaText(string $text)
 */
class SeoHelper extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'filament-seo.helper';
    }
}
