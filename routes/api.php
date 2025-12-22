<?php

use App\Http\Controllers\Api\AIResumeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PlansController;
use App\Http\Controllers\Api\ResumeAnalysisController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\TextExtractionController;
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

// User route (supports both session and token auth)
Route::middleware(['auth:sanctum,web'])->get('/user', [AuthController::class, 'me']);

// Social authentication routes
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback']);

// Plans routes
Route::get('/plans', [PlansController::class, 'index']);

// Text extraction for Builder (resume/job upload)
Route::post('/extract-text', [TextExtractionController::class, 'extract']);

// AI Resume Assistant routes
Route::post('/ai/generate-resume', [AIResumeController::class, 'generateResume']);
Route::post('/ai/optimize-ats', [AIResumeController::class, 'optimizeForATS']);
Route::post('/ai/improve-bullets', [AIResumeController::class, 'improveBulletPoints']);
Route::post('/ai/skill-gap', [AIResumeController::class, 'analyzeSkillGap']);

// Resume Analysis routes (available for both authenticated and guest users)
Route::post('/resume-analysis', [ResumeAnalysisController::class, 'store']);
Route::get('/resume-analysis', [ResumeAnalysisController::class, 'index']);
Route::get('/resume-analysis/{id}', [ResumeAnalysisController::class, 'show']);
Route::delete('/resume-analysis/{id}', [ResumeAnalysisController::class, 'destroy']);

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
    
    // Payment routes
    Route::post('/payment/create-order', [\App\Http\Controllers\Api\PaymentController::class, 'createOrder']);
    Route::post('/payment/verify', [\App\Http\Controllers\Api\PaymentController::class, 'verifyPayment']);
    Route::get('/payment/transactions', [\App\Http\Controllers\Api\PaymentController::class, 'getTransactions']);
    
    // PDF Export
    Route::post('/resume/export-pdf', [\App\Http\Controllers\Api\ResumeExportController::class, 'exportPdf']);
});

// Get single plan
Route::get('/plans/{id}', [PlansController::class, 'show']);
