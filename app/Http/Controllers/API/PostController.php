<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of posts
     */
    public function index(Request $request): JsonResponse
    {
        $posts = Post::with(['user', 'comments', 'likes'])
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json($posts);
    }

    /**
     * Store a newly created post
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'content' => 'required|string',
            'media_url' => 'nullable|url',
            'platforms' => 'nullable|array',
            'scheduled_at' => 'nullable|date',
        ]);

        $post = $request->user()->posts()->create($request->all());

        return response()->json([
            'success' => true,
            'post' => $post->load(['user']),
        ], 201);
    }

    /**
     * Display the specified post
     */
    public function show(Post $post): JsonResponse
    {
        return response()->json(
            $post->load(['user', 'comments.user', 'likes.user'])
        );
    }

    /**
     * Update the specified post
     */
    public function update(Request $request, Post $post): JsonResponse
    {
        $this->authorize('update', $post);

        $request->validate([
            'content' => 'sometimes|required|string',
            'media_url' => 'nullable|url',
        ]);

        $post->update($request->all());

        return response()->json([
            'success' => true,
            'post' => $post->load(['user']),
        ]);
    }

    /**
     * Remove the specified post
     */
    public function destroy(Post $post): JsonResponse
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully',
        ]);
    }
}
