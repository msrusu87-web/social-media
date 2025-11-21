<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    /**
     * Follow a user.
     */
    public function store(User $user): RedirectResponse
    {
        if (Auth::id() === $user->id) {
            return back()->with('error', 'You cannot follow yourself');
        }

        if (Auth::user()->isFollowing($user)) {
            return back()->with('error', 'You are already following this user');
        }

        Follow::create([
            'follower_id' => Auth::id(),
            'following_id' => $user->id,
        ]);

        return back()->with('success', 'You are now following ' . $user->name);
    }

    /**
     * Unfollow a user.
     */
    public function destroy(User $user): RedirectResponse
    {
        if (Auth::id() === $user->id) {
            return back()->with('error', 'You cannot unfollow yourself');
        }

        Follow::where('follower_id', Auth::id())
            ->where('following_id', $user->id)
            ->delete();

        return back()->with('success', 'You have unfollowed ' . $user->name);
    }
}
