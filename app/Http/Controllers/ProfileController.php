<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile
     */
    public function show(User $user): Response
    {
        return Inertia::render('Profile/Show', [
            'user' => $user->load(['profile', 'posts', 'followers', 'following']),
            'postsCount' => $user->posts()->count(),
            'followersCount' => $user->followers()->count(),
            'followingCount' => $user->following()->count(),
        ]);
    }

    /**
     * Show the form for editing the profile
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'user' => $request->user()->load('profile'),
        ]);
    }

    /**
     * Update the user's profile
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $request->user()->id,
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $user = $request->user();
        $user->update($request->only(['name', 'username']));

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $path]);
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['bio' => $request->input('bio')]
        );

        return redirect()->route('profile.show', $user)->with('success', 'Profile updated successfully');
    }
}
