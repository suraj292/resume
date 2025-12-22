{{-- SEO Meta Tags --}}
@php
    $seoData = $seo ?? null;
    $defaultTitle = config('filament-seo.default_title', config('app.name'));
    $defaultDescription = config('filament-seo.default_description');
    $defaultImage = asset(config('filament-seo.default_og_image'));
    $defaultKeywords = config('filament-seo.default_keywords');
    $appendSiteName = config('filament-seo.append_site_name', true);
    $titleSeparator = config('filament-seo.title_separator', ' - ');
    
    // Format title with site name if configured
    $pageTitle = $seoData?->getMetaTitleWithFallback() ?? $defaultTitle;
    if ($appendSiteName && !str_contains($pageTitle, config('filament-seo.site_name', config('app.name')))) {
        $pageTitle = $pageTitle . $titleSeparator . config('filament-seo.site_name', config('app.name'));
    }
@endphp

{{-- Basic Meta Tags --}}
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>{{ $pageTitle }}</title>

<meta name="description" content="{{ $seoData?->getMetaDescriptionWithFallback() ?? $defaultDescription }}">

<meta name="keywords" content="{{ $seoData?->meta_keywords ?? $defaultKeywords }}">

{{-- Robots Meta Tag --}}
<meta name="robots" content="{{ $seoData?->robots ?? config('filament-seo.default_robots', 'index, follow') }}">

{{-- Canonical URL --}}
@if($seoData?->canonical_url)
    <link rel="canonical" href="{{ $seoData->canonical_url }}">
@else
    <link rel="canonical" href="{{ url()->current() }}">
@endif

{{-- Open Graph Tags --}}
<meta property="og:title" content="{{ $seoData?->getOgTitleWithFallback() ?? $defaultTitle }}">
<meta property="og:description" content="{{ $seoData?->getOgDescriptionWithFallback() ?? $defaultDescription }}">
<meta property="og:image" content="{{ $seoData?->getOgImageWithFallback() ?? $defaultImage }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="{{ $seoData?->og_type ?? config('filament-seo.default_og_type', 'website') }}">
<meta property="og:locale" content="{{ $seoData?->locale ?? config('filament-seo.default_locale', 'en_US') }}">
<meta property="og:site_name" content="{{ config('filament-seo.site_name', config('app.name')) }}">

{{-- Twitter Card Tags --}}
<meta name="twitter:card" content="{{ $seoData?->twitter_card ?? config('filament-seo.default_twitter_card', 'summary_large_image') }}">
<meta name="twitter:title" content="{{ $seoData?->twitter_title ?? $seoData?->getOgTitleWithFallback() ?? $defaultTitle }}">
<meta name="twitter:description" content="{{ $seoData?->twitter_description ?? $seoData?->getOgDescriptionWithFallback() ?? $defaultDescription }}">
<meta name="twitter:image" content="{{ $seoData?->twitter_image ?? $seoData?->getOgImageWithFallback() ?? $defaultImage }}">
<meta name="twitter:site" content="{{ $seoData?->twitter_site ?? config('filament-seo.twitter_site') }}">
<meta name="twitter:creator" content="{{ $seoData?->twitter_creator ?? config('filament-seo.twitter_creator') }}">

{{-- Schema.org JSON-LD --}}
@if($seoData?->schema_markup && is_array($seoData->schema_markup))
    <script type="application/ld+json">
    {!! json_encode($seoData->schema_markup, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endif

{{-- Additional Meta Tags --}}
<meta name="author" content="{{ config('app.name') }}">
<meta name="generator" content="Laravel {{ app()->version() }}">
