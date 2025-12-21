<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PlansController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');

// Social authentication routes
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback']);

// Plans routes
Route::get('/plans', [PlansController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/email/resend', [AuthController::class, 'resendVerification']);
    
    // Profile management
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::put('/password', [UserController::class, 'updatePassword']);
    Route::post('/avatar', [UserController::class, 'uploadAvatar']);
    Route::put('/preferences', [UserController::class, 'updatePreferences']);
    Route::delete('/account', [UserController::class, 'deleteAccount']);
    
    // Social account management
    Route::post('/auth/{provider}/link', [SocialAuthController::class, 'linkAccount']);
    Route::delete('/auth/{provider}/unlink', [SocialAuthController::class, 'unlinkAccount']);
});
