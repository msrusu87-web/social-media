<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\PublishToFacebook;
use App\Jobs\PublishToInstagram;
use App\Jobs\PublishToX;
use App\Jobs\PublishToTikTok;
use App\Jobs\PublishToYouTube;
use App\Jobs\PublishToPinterest;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the user's posts.
     */
    public function index(Request $request): JsonResponse
    {
        $posts = Auth::user()
            ->posts()
            ->with('user')
            ->latest()
            ->paginate(20);

        return response()->json($posts);
    }

    /**
     * Store a newly created post.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:5000',
            'media' => 'nullable|array',
            'media.*' => 'nullable|string',
            'platforms' => 'required|array',
            'platforms.*' => 'string|in:facebook,instagram,twitter,tiktok,youtube,pinterest',
            'scheduled_at' => 'nullable|date|after:now',
            'is_ai_generated' => 'nullable|boolean',
        ]);

        $post = Auth::user()->posts()->create([
            'content' => $validated['content'],
            'media' => $validated['media'] ?? null,
            'platforms' => $validated['platforms'],
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'status' => isset($validated['scheduled_at']) ? 'scheduled' : 'published',
            'is_ai_generated' => $validated['is_ai_generated'] ?? false,
        ]);

        // Dispatch publishing jobs
        if ($post->status === 'published') {
            $this->dispatchPublishingJobs($post);
        }

        return response()->json([
            'success' => true,
            'message' => 'Post created successfully',
            'post' => $post,
        ], 201);
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post): JsonResponse
    {
        $this->authorize('view', $post);

        $post->load('user', 'comments.user', 'likes', 'reposts');

        return response()->json($post);
    }

    /**
     * Update the specified post.
     */
    public function update(Request $request, Post $post): JsonResponse
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'content' => 'nullable|string|max:5000',
            'media' => 'nullable|array',
            'media.*' => 'nullable|string',
            'platforms' => 'nullable|array',
            'platforms.*' => 'string|in:facebook,instagram,twitter,tiktok,youtube,pinterest',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $post->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully',
            'post' => $post,
        ]);
    }

    /**
     * Remove the specified post.
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

    /**
     * Dispatch publishing jobs based on selected platforms.
     */
    protected function dispatchPublishingJobs(Post $post): void
    {
        foreach ($post->platforms as $platform) {
            match ($platform) {
                'facebook' => PublishToFacebook::dispatch($post),
                'instagram' => PublishToInstagram::dispatch($post),
                'twitter' => PublishToX::dispatch($post),
                'tiktok' => PublishToTikTok::dispatch($post),
                'youtube' => PublishToYouTube::dispatch($post),
                'pinterest' => PublishToPinterest::dispatch($post),
                default => null,
            };
        }
    }
}
