<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BrandingController extends Controller
{
    /**
     * Get public branding configuration.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'app_name' => config('app.name', 'Resume Builder'),
            'primary_color' => env('APP_PRIMARY_COLOR', '#4f46e5'), // Default Indigo-600
            'show_branding_footer' => env('APP_SHOW_BRANDING_FOOTER', true),
        ]);
    }
}
