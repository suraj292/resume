<?php

use App\Http\Controllers\Api\AIResumeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;
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

// Webhook routes (excluded from CSRF protection - typically unversioned or handled separately)
Route::post('/webhooks/stripe', [\App\Http\Controllers\Api\StripeWebhookController::class, 'handleWebhook']);
Route::post('/webhooks/razorpay', [\App\Http\Controllers\Api\RazorpayWebhookController::class, 'handleWebhook']);

// V1 API Routes
Route::prefix('v1')->group(function () {
    // ...
});

// Legacy/Root Auth Routes (for Social Auth compatibility)
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback']);

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::get('/branding', [\App\Http\Controllers\Api\BrandingController::class, 'index']);

    // User route (supports both session and token auth)
    Route::middleware(['auth:sanctum,web'])->get('/user', [AuthController::class, 'me']);

    // Social authentication routes
    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect']);
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback']);

    // Plans routes
    Route::get('/plans', [PlansController::class, 'index']);
    Route::get('/plans/{id}', [PlansController::class, 'show']);

    // Text extraction
    Route::post('/extract-text', [TextExtractionController::class, 'extract'])
        ->middleware('virus.scan');

    // AI Resume Assistant routes
    Route::prefix('ai')->group(function () {
        Route::post('/generate-resume', [AIResumeController::class, 'generateResume'])
            ->middleware('throttle:10,1');
        Route::post('/optimize-ats', [AIResumeController::class, 'optimizeForATS'])
            ->middleware('throttle:10,1');
        Route::post('/improve-bullets', [AIResumeController::class, 'improveBulletPoints'])
            ->middleware('throttle:20,1');
        Route::post('/analyze-gap', [AIResumeController::class, 'analyzeSkillGap'])
            ->middleware('throttle:10,1');
        Route::post('/parse-resume', [AIResumeController::class, 'parseResume'])
            ->middleware('throttle:5,1'); // Slower throttle for file uploads
        Route::post('/cover-letter', [AIResumeController::class, 'generateCoverLetter'])
            ->middleware('throttle:10,1');
    });

    // Resume Analysis routes
    Route::post('/resume-analysis', [ResumeAnalysisController::class, 'store'])
        ->middleware('virus.scan');
    Route::get('/resume-analysis', [ResumeAnalysisController::class, 'index']);
    Route::get('/resume-analysis/{id}', [ResumeAnalysisController::class, 'show']);
    Route::delete('/resume-analysis/{id}', [ResumeAnalysisController::class, 'destroy']);

    // Standalone Resume Scoring API
    Route::post('/resume/score', [\App\Http\Controllers\Api\ResumeScoringController::class, 'score'])
        ->middleware('throttle:30,1'); // Generic throttle

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
    });

    // PDF Export
    Route::post('/resume/export-pdf', [\App\Http\Controllers\Api\ResumeExportController::class, 'exportPdf']);
    Route::post('/resume/export-with-template', [\App\Http\Controllers\Api\ResumeHTMLExportController::class, 'exportWithTemplate']);

    // Blog routes
    Route::get('/blogs', [BlogController::class, 'index']);
    Route::get('/blogs/featured', [BlogController::class, 'featured']);
    Route::get('/blogs/trending', [BlogController::class, 'trending']);
    Route::get('/blogs/{slug}', [BlogController::class, 'show']);
    Route::get('/blog-categories', [BlogController::class, 'categories']);
    Route::get('/blogs/category/{slug}', [BlogController::class, 'byCategory']);
});
