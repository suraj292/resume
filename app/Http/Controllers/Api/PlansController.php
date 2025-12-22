<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\GeoLocationService;
use Illuminate\Http\Request;

class PlansController extends Controller
{
    protected $geoService;

    public function __construct(GeoLocationService $geoService)
    {
        $this->geoService = $geoService;
    }

    public function index(Request $request)
    {
        // Get user's IP address
        $ip = $request->ip();
        
        // Detect country from IP
        $country = $this->geoService->getCountryFromIP($ip);
        
        // Get currency code based on country
        $currencyCode = $this->geoService->getCurrencyForCountry($country);
        
        // Fetch ONLY plans matching the user's currency
        $plans = Plan::active()
            ->where('currency_code', $currencyCode)
            ->ordered()
            ->get();
        
        return $plans;
    }

    /**
     * Get a single plan by ID
     */
    public function show($id)
    {
        $plan = Plan::active()->findOrFail($id);
        return response()->json($plan);
    }
}
