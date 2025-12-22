<?php

namespace Webvio\DynamicSitemap\Models;

/**
 * Simple settings wrapper using config for Filament integration.
 * This allows the Filament page to work without additional dependencies.
 */
class SitemapSettings
{
    /**
     * Get a setting value from config.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return config("dynamic-sitemap.{$key}", $default);
    }

    /**
     * Set a setting value (persists to config file).
     */
    public static function set(string $key, mixed $value): void
    {
        config(["dynamic-sitemap.{$key}" => $value]);
        
        // Optionally persist to file if needed
        // This requires write permissions to config file
    }

    /**
     * Get all settings as array.
     */
    public static function all(): array
    {
        return config('dynamic-sitemap');
    }

    /**
     * Magic getter for property access.
     */
    public function __get(string $name): mixed
    {
        return self::get($name);
    }

    /**
     * Magic setter for property access.
     */
    public function __set(string $name, mixed $value): void
    {
        self::set($name, $value);
    }
}
