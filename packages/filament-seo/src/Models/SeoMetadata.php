<?php

namespace Webvio\FilamentSeo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Cache;

class SeoMetadata extends Model
{
    protected $table = 'seo_metadata';

    protected $fillable = [
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'og_type',
        'twitter_card',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'twitter_site',
        'twitter_creator',
        'canonical_url',
        'index',
        'follow',
        'schema_markup',
        'locale',
        'priority',
    ];

    protected $casts = [
        'index' => 'boolean',
        'follow' => 'boolean',
        'schema_markup' => 'array',
    ];

    /**
     * Get the parent seoable model.
     */
    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get robots meta tag value.
     */
    public function getRobotsAttribute(): string
    {
        $index = $this->index ? 'index' : 'noindex';
        $follow = $this->follow ? 'follow' : 'nofollow';
        
        return "{$index}, {$follow}";
    }

    /**
     * Get the meta title with fallback.
     */
    public function getMetaTitleWithFallback(?string $fallback = null): string
    {
        return $this->meta_title 
            ?? $fallback 
            ?? config('filament-seo.default_title', config('app.name'));
    }

    /**
     * Get the meta description with fallback.
     */
    public function getMetaDescriptionWithFallback(?string $fallback = null): string
    {
        return $this->meta_description 
            ?? $fallback 
            ?? config('filament-seo.default_description', '');
    }

    /**
     * Get the OG title with fallback.
     */
    public function getOgTitleWithFallback(?string $fallback = null): string
    {
        return $this->og_title 
            ?? $this->meta_title 
            ?? $fallback 
            ?? config('filament-seo.default_title', config('app.name'));
    }

    /**
     * Get the OG description with fallback.
     */
    public function getOgDescriptionWithFallback(?string $fallback = null): string
    {
        return $this->og_description 
            ?? $this->meta_description 
            ?? $fallback 
            ?? config('filament-seo.default_description', '');
    }

    /**
     * Get the OG image with fallback.
     */
    public function getOgImageWithFallback(?string $fallback = null): string
    {
        $image = $this->og_image ?? $fallback;
        
        if ($image) {
            if (str_starts_with($image, 'http')) {
                return $image;
            }
            return asset($image);
        }
        
        return asset(config('filament-seo.default_og_image', 'images/default-og-image.jpg'));
    }

    /**
     * Clear SEO cache when model is saved.
     */
    protected static function booted(): void
    {
        static::saved(function (SeoMetadata $seo) {
            Cache::forget("seo.{$seo->seoable_type}.{$seo->seoable_id}");
        });

        static::deleted(function (SeoMetadata $seo) {
            Cache::forget("seo.{$seo->seoable_type}.{$seo->seoable_id}");
        });
    }
}
