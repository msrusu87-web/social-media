<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Display the specified profile.
     */
    public function show($username)
    {
        $profile = Profile::with(['user.posts', 'user.followers', 'user.following'])
            ->where('username', $username)
            ->firstOrFail();

        return view('profile.show', compact('profile'));
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit(Request $request)
    {
        $profile = $request->user()->profile;

        return view('profile.edit', compact('profile'));
    }

    /**
     * Update the specified profile.
     */
    public function update(Request $request)
    {
        $profile = $request->user()->profile;

        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|required|string|max:255|unique:profiles,username,' . $profile->id,
            'bio' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'birth_date' => 'nullable|date|before:today',
            'avatar' => 'nullable|image|max:2048',
            'cover_image' => 'nullable|image|max:5120',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['username', 'bio', 'location', 'website', 'birth_date']);

        if ($request->hasFile('avatar')) {
            if ($profile->avatar) {
                Storage::delete($profile->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->hasFile('cover_image')) {
            if ($profile->cover_image) {
                Storage::delete($profile->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $profile->update($data);

        return redirect()->route('profile.show', $profile->username)
            ->with('success', 'Profile updated successfully');
    }
}
