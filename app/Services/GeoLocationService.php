<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoLocationService
{
    /**
     * Get country code from IP address
     *
     * @param string $ip
     * @return string Country code (e.g., 'IN', 'US') or 'IN' as default for localhost
     */
    public function getCountryFromIP(string $ip): string
    {
        // For localhost/private IPs in development, detect actual public IP
        if ($this->isPrivateIP($ip)) {
            Log::info("Detected private IP: {$ip}, attempting to fetch public IP");
            
            try {
                $publicIP = trim(Http::timeout(5)->get('https://api.ipify.org')->body());
                Log::info("Fetched public IP: {$publicIP}");
                
                if ($publicIP && !$this->isPrivateIP($publicIP) && filter_var($publicIP, FILTER_VALIDATE_IP)) {
                   $ip = $publicIP;
                } else {
                    // For development in India, default to IN
                    Log::info("Public IP fetch failed or invalid, defaulting to IN for testing");
                    return 'IN';
                }
            } catch (\Exception $e) {
                Log::warning("Could not fetch public IP: " . $e->getMessage());
                // Default to IN for testing in India
                return 'IN';
            }
        }

        // Check cache first (cache for 24 hours)
        $cacheKey = "geo_country_{$ip}";
        
        return Cache::remember($cacheKey, 86400, function () use ($ip) {
            try {
                // Use ipapi.co free API
                Log::info("Fetching country for IP: {$ip}");
                $response = Http::timeout(5)->get("https://ipapi.co/{$ip}/country/");
                
                if ($response->successful()) {
                    $country = trim($response->body());
                    
                    // Validate country code format
                    if (strlen($country) === 2 && ctype_alpha($country)) {
                        Log::info("Detected country: {$country} for IP: {$ip}");
                        return strtoupper($country);
                    }
                }
                
                Log::warning("Invalid response from geolocation API for IP {$ip}");
            } catch (\Exception $e) {
                Log::warning("GeoLocation API failed for IP {$ip}: " . $e->getMessage());
            }

            // Fallback to IN for development (change to US for production default)
            return 'IN';
        });
    }

    /**
     * Check if IP is private/local
     *
     * @param string $ip
     * @return bool
     */
    private function isPrivateIP(string $ip): bool
    {
        // Check for localhost
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
            return true;
        }

        // Check for private IP ranges
        $privateRanges = [
            '10.0.0.0|10.255.255.255',
            '172.16.0.0|172.31.255.255',
            '192.168.0.0|192.168.255.255',
        ];

        $longIP = ip2long($ip);
        if ($longIP === false) {
            return true; // Invalid IP, treat as private
        }

        foreach ($privateRanges as $range) {
            list($start, $end) = explode('|', $range);
            if ($longIP >= ip2long($start) && $longIP <= ip2long($end)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get currency code based on country
     *
     * @param string $countryCode
     * @return string Currency code ('INR' or 'USD')
     */
    public function getCurrencyForCountry(string $countryCode): string
    {
        return $countryCode === 'IN' ? 'INR' : 'USD';
    }

    /**
     * Get currency symbol based on country
     *
     * @param string $countryCode
     * @return string Currency symbol ('₹' or '$')
     */
    public function getCurrencySymbol(string $countryCode): string
    {
        return $countryCode === 'IN' ? '₹' : '$';
    }
}
