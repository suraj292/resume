<?php

namespace Webvio\FilamentSeo\Traits;

use Webvio\FilamentSeo\Models\SeoMetadata;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Cache;

trait HasSeo
{
    /**
     * Boot the trait.
     */
    protected static function bootHasSeo(): void
    {
        static::deleted(function ($model) {
            $model->seo()?->delete();
        });
    }

    /**
     * Get the SEO metadata for the model.
     */
    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMetadata::class, 'seoable');
    }

    /**
     * Get or create SEO metadata.
     */
    public function getOrCreateSeo(): SeoMetadata
    {
        return $this->seo ?? $this->seo()->create([
            'index' => true,
            'follow' => true,
        ]);
    }

    /**
     * Get cached SEO data.
     */
    public function getCachedSeo(): ?SeoMetadata
    {
        $cacheKey = "seo." . get_class($this) . ".{$this->id}";
        
        return Cache::remember($cacheKey, config('filament-seo.cache_duration', 3600), function () {
            return $this->seo;
        });
    }

    /**
     * Clear SEO cache for this model.
     */
    public function clearSeoCache(): void
    {
        $cacheKey = "seo." . get_class($this) . ".{$this->id}";
        Cache::forget($cacheKey);
    }

    /**
     * Update SEO metadata.
     */
    public function updateSeo(array $data): SeoMetadata
    {
        $seo = $this->getOrCreateSeo();
        $seo->update($data);
        $this->clearSeoCache();
        
        return $seo->fresh();
    }

    /**
     * Get SEO title attribute (override in models if needed).
     */
    public function getSeoTitleFallback(): ?string
    {
        return $this->title ?? $this->name ?? null;
    }

    /**
     * Get SEO description fallback (override in models if needed).
     */
    public function getSeoDescriptionFallback(): ?string
    {
        return $this->excerpt ?? $this->description ?? null;
    }

    /**
     * Get SEO image fallback (override in models if needed).
     */
    public function getSeoImageFallback(): ?string
    {
        return $this->image ?? $this->featured_image ?? null;
    }
}
