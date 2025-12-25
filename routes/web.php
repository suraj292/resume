<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This application uses API-only architecture with a standalone frontend.
| All frontend routes are handled by the separate frontend application.
| Web routes are minimal and only used for settings/admin panel.
|
*/

use App\Http\Controllers\Api\AuthController;

// Email verification route (for links from emails)
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['throttle:6,1'])
    ->name('verification.verify.web');

require __DIR__.'/settings.php';
