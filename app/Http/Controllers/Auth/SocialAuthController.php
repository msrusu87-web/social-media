<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to social provider
     */
    public function redirect(string $provider): RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle callback from social provider
     */
    public function callback(string $provider): RedirectResponse
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'username' => $this->generateUsername($socialUser->getName()),
                    'password' => Hash::make(Str::random(32)),
                    'email_verified_at' => now(),
                    'avatar' => $socialUser->getAvatar(),
                ]);

                $user->profile()->create([
                    'bio' => '',
                ]);
            }

            Auth::login($user);

            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Authentication failed');
        }
    }

    /**
     * Generate unique username
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
