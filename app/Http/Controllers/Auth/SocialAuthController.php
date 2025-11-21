<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to social provider.
     */
    public function redirect(string $provider): RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle social provider callback.
     */
    public function callback(string $provider): RedirectResponse
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            // Find or create user
            $user = User::where('email', $socialUser->email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->name,
                    'email' => $socialUser->email,
                    'username' => $this->generateUsername($socialUser->name ?? $socialUser->email),
                    'password' => Hash::make(Str::random(32)),
                    'email_verified_at' => now(),
                ]);

                // Create profile
                $user->profile()->create([
                    'avatar' => $socialUser->avatar,
                ]);
            }

            Auth::login($user, true);

            return redirect()->intended(route('dashboard'));
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Authentication failed. Please try again.');
        }
    }

    /**
     * Generate unique username.
     */
    protected function generateUsername(string $name): string
    {
        $username = Str::slug($name);
        $count = 1;

        while (User::where('username', $username)->exists()) {
            $username = Str::slug($name) . $count;
            $count++;
        }

        return $username;
    }
}
