<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default SEO Meta Tags
    |--------------------------------------------------------------------------
    |
    | These values will be used as fallbacks when SEO metadata is not set
    | for a specific page or when fields are left empty.
    |
    */

    'default_title' => env('SEO_DEFAULT_TITLE', config('app.name')),
    
    'default_description' => env('SEO_DEFAULT_DESCRIPTION', 'Welcome to our website'),
    
    'default_keywords' => env('SEO_DEFAULT_KEYWORDS', ''),
    
    'default_og_image' => env('SEO_DEFAULT_OG_IMAGE', '/images/default-og-image.jpg'),
    
    'default_og_type' => env('SEO_DEFAULT_OG_TYPE', 'website'),
    
    'default_twitter_card' => env('SEO_DEFAULT_TWITTER_CARD', 'summary_large_image'),
    
    'default_locale' => env('SEO_DEFAULT_LOCALE', 'en_US'),
    
    'default_robots' => env('SEO_DEFAULT_ROBOTS', 'index, follow'),
    
    /*
    |--------------------------------------------------------------------------
    | Site Information
    |--------------------------------------------------------------------------
    */
    
    'site_name' => env('SEO_SITE_NAME', config('app.name')),
    
    'twitter_site' => env('SEO_TWITTER_SITE', ''),
    
    'twitter_creator' => env('SEO_TWITTER_CREATOR', ''),
    
    /*
    |--------------------------------------------------------------------------
    | Title Templates
    |--------------------------------------------------------------------------
    |
    | Define how page titles should be formatted
    |
    */
    
    'title_separator' => env('SEO_TITLE_SEPARATOR', ' - '),
    
    'append_site_name' => env('SEO_APPEND_SITE_NAME', true),
    
    /*
    |--------------------------------------------------------------------------
    | Storage Configuration
    |--------------------------------------------------------------------------
    */
    
    'storage_disk' => env('SEO_STORAGE_DISK', 'public'),
    
    'og_images_directory' => env('SEO_OG_IMAGES_DIR', 'seo/og-images'),
    
    'twitter_images_directory' => env('SEO_TWITTER_IMAGES_DIR', 'seo/twitter-images'),
    
    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */
    
    'cache_duration' => env('SEO_CACHE_DURATION', 3600), // 1 hour in seconds
    
    /*
    |--------------------------------------------------------------------------
    | Organization Schema
    |--------------------------------------------------------------------------
    |
    | Default organization information for schema markup
    |
    */
    
    'organization' => [
        'name' => env('SEO_ORG_NAME', config('app.name')),
        'url' => env('SEO_ORG_URL', config('app.url')),
        'logo' => env('SEO_ORG_LOGO', '/images/logo.png'),
        'same_as' => [
            // Add your social media URLs here
            // 'https://facebook.com/yourpage',
            // 'https://twitter.com/yourprofile',
        ],
    ],

];
