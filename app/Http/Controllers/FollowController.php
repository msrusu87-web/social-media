<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    /**
     * Follow a user.
     */
    public function follow(User $user)
    {
        $currentUser = Auth::user();

        if ($currentUser->id === $user->id) {
            return back()->with('error', 'You cannot follow yourself.');
        }

        if ($currentUser->isFollowing($user)) {
            return back()->with('error', 'You are already following this user.');
        }

        $currentUser->following()->attach($user->id);

        return back()->with('success', "You are now following {$user->name}.");
    }

    /**
     * Unfollow a user.
     */
    public function unfollow(User $user)
    {
        $currentUser = Auth::user();

        if (!$currentUser->isFollowing($user)) {
            return back()->with('error', 'You are not following this user.');
        }

        $currentUser->following()->detach($user->id);

        return back()->with('success', "You have unfollowed {$user->name}.");
    }

    /**
     * Get the list of followers for a user.
     */
    public function followers(User $user)
    {
        $followers = $user->followers()
            ->with('profile')
            ->paginate(20);

        return response()->json($followers);
    }

    /**
     * Get the list of users a user is following.
     */
    public function following(User $user)
    {
        $following = $user->following()
            ->with('profile')
            ->paginate(20);

        return response()->json($following);
    }
}
