<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show(User $user): View
    {
        $user->load(['profile', 'posts' => function ($query) {
            $query->published()->latest()->limit(10);
        }]);

        $followersCount = $user->followers()->count();
        $followingCount = $user->following()->count();
        $postsCount = $user->posts()->published()->count();

        $isFollowing = Auth::check() && Auth::user()->isFollowing($user);

        return view('profile.show', compact('user', 'followersCount', 'followingCount', 'postsCount', 'isFollowing'));
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit(): View
    {
        $user = Auth::user();
        $user->load('profile');

        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'bio' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'birth_date' => 'nullable|date|before:today',
            'avatar' => 'nullable|image|max:2048',
            'cover_photo' => 'nullable|image|max:5120',
        ]);

        // Update user
        $user->update([
            'name' => $validated['name'],
            'username' => $validated['username'],
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($user->profile->avatar) {
                Storage::disk('public')->delete($user->profile->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Handle cover photo upload
        if ($request->hasFile('cover_photo')) {
            if ($user->profile->cover_photo) {
                Storage::disk('public')->delete($user->profile->cover_photo);
            }
            $validated['cover_photo'] = $request->file('cover_photo')->store('covers', 'public');
        }

        // Update or create profile
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'bio' => $validated['bio'] ?? null,
                'location' => $validated['location'] ?? null,
                'website' => $validated['website'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
                'avatar' => $validated['avatar'] ?? $user->profile->avatar ?? null,
                'cover_photo' => $validated['cover_photo'] ?? $user->profile->cover_photo ?? null,
            ]
        );

        return redirect()->route('profile.show', $user)->with('success', 'Profile updated successfully');
    }
}
