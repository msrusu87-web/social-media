<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Like a post
     */
    public function like(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();

        if ($post->likes()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You have already liked this post',
            ], 400);
        }

        $post->likes()->create([
            'user_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Post liked successfully',
            'likes_count' => $post->likes()->count(),
        ]);
    }

    /**
     * Unlike a post
     */
    public function unlike(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();

        $like = $post->likes()->where('user_id', $user->id)->first();

        if (!$like) {
            return response()->json([
                'success' => false,
                'message' => 'You have not liked this post',
            ], 400);
        }

        $like->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post unliked successfully',
            'likes_count' => $post->likes()->count(),
        ]);
    }
}
