<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    /**
     * Follow a user.
     */
    public function follow(Request $request, User $user)
    {
        if ($request->user()->id === $user->id) {
            return back()->with('error', 'You cannot follow yourself');
        }

        $request->user()->following()->syncWithoutDetaching($user->id);

        return back()->with('success', 'Successfully followed ' . $user->name);
    }

    /**
     * Unfollow a user.
     */
    public function unfollow(Request $request, User $user)
    {
        $request->user()->following()->detach($user->id);

        return back()->with('success', 'Successfully unfollowed ' . $user->name);
    }

    /**
     * Get followers of a user.
     */
    public function followers(User $user)
    {
        $followers = $user->followers()->with('profile')->paginate(20);

        return view('follow.followers', compact('user', 'followers'));
    }

    /**
     * Get users that a user is following.
     */
    public function following(User $user)
    {
        $following = $user->following()->with('profile')->paginate(20);

        return view('follow.following', compact('user', 'following'));
    }
}
