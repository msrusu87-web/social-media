<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to social provider.
     */
    public function redirect($provider)
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle social provider callback.
     */
    public function callback($provider)
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();

            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(16)),
                    'email_verified_at' => now(),
                ]);

                $user->profile()->create([
                    'username' => $this->generateUsername($socialUser->getName()),
                    'avatar' => $socialUser->getAvatar(),
                ]);
            }

            // Store or update social connection
            $user->socialConnections()->updateOrCreate(
                ['platform' => $provider],
                [
                    'platform_user_id' => $socialUser->getId(),
                    'access_token' => $socialUser->token,
                    'refresh_token' => $socialUser->refreshToken,
                    'is_active' => true,
                ]
            );

            Auth::login($user);

            return redirect('/dashboard')->with('success', 'Successfully logged in with ' . ucfirst($provider));
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Failed to authenticate with ' . ucfirst($provider));
        }
    }

    /**
     * Validate the social provider.
     */
    protected function validateProvider($provider)
    {
        $allowedProviders = ['facebook', 'google', 'twitter', 'github'];

        if (!in_array($provider, $allowedProviders)) {
            abort(404);
        }
    }

    /**
     * Generate a unique username.
     */
    protected function generateUsername($name)
    {
        $username = Str::slug($name);
        $count = 1;

        while (\App\Models\Profile::where('username', $username)->exists()) {
            $username = Str::slug($name) . $count;
            $count++;
        }

        return $username;
    }
}
