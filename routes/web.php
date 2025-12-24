<?php

use Illuminate\Support\Facades\Route;

/**
 * Web Routes
 * 
 * This is an API-only backend. The frontend is a separate Vue.js SPA.
 * 
 * Frontend: http://localhost:5173 (development)
 * Backend API: http://localhost:8000/api
 */

Route::get('/', function () {
    return response()->json([
        'message' => 'Resume Builder API',
        'version' => '1.0.0',
        'frontend_url' => env('FRONTEND_URL', 'http://localhost:5173'),
        'api_base' => url('/api'),
        'documentation' => url('/api/docs'),
        'endpoints' => [
            'resumes' => '/api/resumes',
            'ai' => '/api/ai/*',
            'export' => '/api/export/*',
            'upload' => '/api/upload/*',
            'ats' => '/api/ats-check',
        ],
        'status' => 'online'
    ]);
});

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Redirect any other web routes to frontend
Route::fallback(function () {
    return response()->json([
        'message' => 'This is an API-only backend. Please use the frontend at ' . env('FRONTEND_URL', 'http://localhost:5173'),
        'frontend_url' => env('FRONTEND_URL', 'http://localhost:5173'),
    ], 404);
});
