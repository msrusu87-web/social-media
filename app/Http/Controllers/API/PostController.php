<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of posts.
     */
    public function index(Request $request)
    {
        $posts = Post::with(['user', 'likes', 'comments'])
            ->when($request->user_id, function ($query, $userId) {
                return $query->where('user_id', $userId);
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'posts' => $posts
        ]);
    }

    /**
     * Store a newly created post.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:5000',
            'media' => 'nullable|array',
            'media.*' => 'string',
            'platforms' => 'nullable|array',
            'platforms.*' => 'in:facebook,instagram,x,tiktok,youtube,pinterest',
            'scheduled_at' => 'nullable|date|after:now',
            'ai_generated' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $post = $request->user()->posts()->create([
            'content' => $request->content,
            'media' => $request->media,
            'platforms' => $request->platforms,
            'status' => $request->scheduled_at ? 'scheduled' : 'draft',
            'scheduled_at' => $request->scheduled_at,
            'ai_generated' => $request->ai_generated ?? false,
        ]);

        return response()->json([
            'success' => true,
            'post' => $post
        ], 201);
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post)
    {
        $post->load(['user', 'comments.user', 'likes', 'reposts']);

        return response()->json([
            'success' => true,
            'post' => $post
        ]);
    }

    /**
     * Update the specified post.
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validator = Validator::make($request->all(), [
            'content' => 'sometimes|required|string|max:5000',
            'media' => 'nullable|array',
            'platforms' => 'nullable|array',
            'status' => 'sometimes|in:draft,scheduled,published,failed',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $post->update($request->only([
            'content', 'media', 'platforms', 'status', 'scheduled_at'
        ]));

        return response()->json([
            'success' => true,
            'post' => $post
        ]);
    }

    /**
     * Remove the specified post.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully'
        ]);
    }
}
