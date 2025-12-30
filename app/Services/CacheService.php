<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    /**
     * Default TTL for AI responses (24 hours)
     */
    const AI_RESPONSE_TTL = 86400;

    /**
     * Default TTL for GeoIP data (30 days - IP locations rarely change)
     */
    const GEO_DATA_TTL = 2592000;

    /**
     * Get cached AI response or generate new one
     *
     * @param string $promptHash Unique hash of the prompt/context
     * @param callable $callback Function to generate response if not cached
     * @param int|null $ttl Time to live in seconds
     * @return mixed
     */
    public function rememberAIResponse(string $promptHash, callable $callback, ?int $ttl = null)
    {
        $key = 'ai_response:' . $promptHash;
        $ttl = $ttl ?? self::AI_RESPONSE_TTL;

        return Cache::remember($key, $ttl, function () use ($callback, $key) {
            Log::info("Cache miss for AI response key: {$key}");
            return $callback();
        });
    }

    /**
     * Get cached GeoIP data or fetch new
     *
     * @param string $ip IP address
     * @param callable $callback Function to fetch data if not cached
     * @return mixed
     */
    public function rememberGeoData(string $ip, callable $callback)
    {
        $key = 'geo_ip:' . $ip;
        
        return Cache::remember($key, self::GEO_DATA_TTL, function () use ($callback, $key) {
            Log::info("Cache miss for GeoIP key: {$key}");
            return $callback();
        });
    }

    /**
     * Generate a unique hash for a prompt context
     * 
     * @param array $context Data to hash
     * @return string
     */
    public function generatePromptHash(array $context): string
    {
        // Sort keys to ensure consistent hashing
        ksort($context);
        return md5(json_encode($context));
    }

    /**
     * Clear AI response cache for a specific prompt
     */
    public function forgetAIResponse(string $promptHash): bool
    {
        return Cache::forget('ai_response:' . $promptHash);
    }
}
