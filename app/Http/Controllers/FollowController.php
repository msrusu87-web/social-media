<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    /**
     * Follow a user
     */
    public function follow(Request $request, User $user): JsonResponse
    {
        $follower = $request->user();

        if ($follower->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot follow yourself',
            ], 400);
        }

        if ($follower->following()->where('following_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You are already following this user',
            ], 400);
        }

        $follower->following()->attach($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Successfully followed user',
        ]);
    }

    /**
     * Unfollow a user
     */
    public function unfollow(Request $request, User $user): JsonResponse
    {
        $follower = $request->user();

        if (!$follower->following()->where('following_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not following this user',
            ], 400);
        }

        $follower->following()->detach($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Successfully unfollowed user',
        ]);
    }

    /**
     * Get user's followers
     */
    public function followers(User $user): JsonResponse
    {
        $followers = $user->followers()->with('profile')->get();

        return response()->json([
            'followers' => $followers,
        ]);
    }

    /**
     * Get users that the user is following
     */
    public function following(User $user): JsonResponse
    {
        $following = $user->following()->with('profile')->get();

        return response()->json([
            'following' => $following,
        ]);
    }
}
