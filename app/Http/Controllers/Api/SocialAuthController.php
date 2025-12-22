<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to OAuth provider
     */
    public function redirect($provider)
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->stateless()->redirect();
    }

    /**
     * Handle OAuth callback
     */
    public function callback($provider)
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            // Redirect to auth page with error
            return redirect('/auth?error=oauth_failed');
        }

        // Check if social account exists
        $socialAccount = SocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($socialAccount) {
            // Login existing user
            $user = $socialAccount->user;
        } else {
            // Check if user exists with this email
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                // Create new user
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'avatar' => $socialUser->getAvatar(),
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'email_verified_at' => now(), // Auto-verify email for social logins
                    'password' => Hash::make(Str::random(24)), // Random password
                ]);
            }

            // Create social account link
            SocialAccount::create([
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'provider_token' => $socialUser->token,
                'provider_refresh_token' => $socialUser->refreshToken ?? null,
            ]);
        }

        // Log the user in with session
        auth()->login($user, true);

        // Store auth token in session for frontend
        $token = $user->createToken('auth_token')->plainTextToken;
        session([
            'auth_token' => $token,
            'user_id' => $user->id,
            'social_login' => true
        ]);

        // Redirect to frontend builder or intended route
        return redirect('/builder');
    }

    /**
     * Link social account to authenticated user
     */
    public function linkAccount(Request $request, $provider)
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to authenticate with ' . $provider,
                'error' => $e->getMessage(),
            ], 400);
        }

        $user = $request->user();

        // Check if this social account is already linked to another user
        $existingAccount = SocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($existingAccount && $existingAccount->user_id !== $user->id) {
            return response()->json([
                'message' => 'This ' . $provider . ' account is already linked to another user',
            ], 409);
        }

        if (!$existingAccount) {
            // Create new social account link
            SocialAccount::create([
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'provider_token' => $socialUser->token,
                'provider_refresh_token' => $socialUser->refreshToken ?? null,
            ]);
        }

        return response()->json([
            'message' => ucfirst($provider) . ' account linked successfully',
            'user' => $user->load('socialAccounts'),
        ]);
    }

    /**
     * Unlink social account from authenticated user
     */
    public function unlinkAccount(Request $request, $provider)
    {
        $this->validateProvider($provider);

        $user = $request->user();

        $socialAccount = SocialAccount::where('user_id', $user->id)
            ->where('provider', $provider)
            ->first();

        if (!$socialAccount) {
            return response()->json([
                'message' => 'No ' . $provider . ' account linked',
            ], 404);
        }

        // Prevent unlinking if it's the only authentication method and no password
        if ($user->socialAccounts()->count() === 1 && !$user->password) {
            return response()->json([
                'message' => 'Cannot unlink the only authentication method. Please set a password first.',
            ], 400);
        }

        $socialAccount->delete();

        return response()->json([
            'message' => ucfirst($provider) . ' account unlinked successfully',
            'user' => $user->load('socialAccounts'),
        ]);
    }

    /**
     * Validate provider
     */
    protected function validateProvider($provider)
    {
        $allowedProviders = ['google', 'linkedin', 'github'];

        if (!in_array($provider, $allowedProviders)) {
            abort(404, 'Invalid provider');
        }
    }
}
