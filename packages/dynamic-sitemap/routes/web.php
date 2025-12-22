<?php

use Illuminate\Support\Facades\Route;
use Webvio\DynamicSitemap\Http\Controllers\SitemapIndexController;
use Webvio\DynamicSitemap\Http\Controllers\SitemapSectionController;

// Sitemap index
Route::get(
    config('dynamic-sitemap.index_path', '/sitemap.xml'),
    SitemapIndexController::class
)->name('sitemap.index');

// Sitemap sections
Route::get(
    'sitemap-{path}.xml',
    SitemapSectionController::class
)->name('sitemap.section');
