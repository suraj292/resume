<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // Detect currency from IP
        $geoService = app(\App\Services\GeoLocationService::class);
        $country = $geoService->getCountryFromIP($request->ip());
        $currency = $geoService->getCurrencyForCountry($country);

        // Get free plan for this currency
        $freePlan = \App\Models\Plan::where('slug', 'like', 'free-%')
            ->where('currency_code', $currency)
            ->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'currency' => $currency,
            'plan_id' => $freePlan?->id,
            'plan_started_at' => now(),
        ]);

        event(new Registered($user));

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful. Please check your email to verify your account.',
            'user' => $user->load('plan'),
            'token' => $token,
            'email_verified' => false,
            'verification_sent' => true,
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Regenerate session to prevent session fixation attacks
        $request->session()->regenerate();

        // Revoke all previous tokens
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user->load('plan'),
            'token' => $token,
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        
        // Handle token-based authentication (Sanctum)
        // Only delete if it's an actual PersonalAccessToken, not a TransientToken
        if ($user && $user->currentAccessToken() instanceof \Laravel\Sanctum\PersonalAccessToken) {
            $user->currentAccessToken()->delete();
        }

        // Handle session-based authentication
        if (Auth::check()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get authenticated user with capabilities
     */
    public function me(Request $request)
    {
        $user = $request->user()->load(['socialAccounts', 'plan']);

        return response()->json([
            'user' => $user,
            'capabilities' => [
                'can_create_resume' => $user->canCreateResume(),
                'can_use_ats' => $user->canUseATS(),
                'resumes_remaining' => $user->resumes_remaining,
                'ats_scans_remaining' => $user->ats_scans_remaining,
            ],
        ]);
    }

    /**
     * Verify email
     */
    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json([
                'message' => 'Invalid verification link',
            ], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified',
            ]);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json([
            'message' => 'Email verified successfully',
        ]);
    }

    /**
     * Resend verification email
     */
    public function resendVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified',
            ]);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Verification email sent',
        ]);
    }
}
