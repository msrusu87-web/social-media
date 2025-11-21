<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RepostController extends Controller
{
    /**
     * Repost a post
     */
    public function repost(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();

        if ($post->reposts()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reposted this post',
            ], 400);
        }

        $repost = $post->reposts()->create([
            'user_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Post reposted successfully',
            'repost' => $repost,
            'reposts_count' => $post->reposts()->count(),
        ], 201);
    }

    /**
     * Remove a repost
     */
    public function unrepost(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();

        $repost = $post->reposts()->where('user_id', $user->id)->first();

        if (!$repost) {
            return response()->json([
                'success' => false,
                'message' => 'You have not reposted this post',
            ], 400);
        }

        $repost->delete();

        return response()->json([
            'success' => true,
            'message' => 'Repost removed successfully',
            'reposts_count' => $post->reposts()->count(),
        ]);
    }
}
