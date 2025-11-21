<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Jobs\PublishToFacebook;
use App\Jobs\PublishToInstagram;
use App\Jobs\PublishToX;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the user's posts.
     */
    public function index(): Response
    {
        $posts = Auth::user()->posts()
            ->with(['user.profile', 'comments', 'likes', 'reposts'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Posts/Index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for creating a new post.
     */
    public function create(): Response
    {
        $socialConnections = Auth::user()
            ->socialConnections()
            ->active()
            ->get();

        return Inertia::render('Posts/Create', [
            'socialConnections' => $socialConnections,
        ]);
    }

    /**
     * Store a newly created post.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:5000',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,gif,mp4|max:10240',
            'platforms' => 'required|array',
            'scheduled_at' => 'nullable|date|after:now',
            'ai_generated' => 'boolean',
        ]);

        // Check if user can publish based on subscription
        if (!Auth::user()->canPublishPost()) {
            return back()->with('error', 'You have reached your post limit for this month.');
        }

        $post = Auth::user()->posts()->create([
            'content' => $validated['content'],
            'platforms' => $validated['platforms'],
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'status' => isset($validated['scheduled_at']) ? 'scheduled' : 'draft',
            'ai_generated' => $validated['ai_generated'] ?? false,
        ]);

        // Handle media uploads
        if ($request->hasFile('media')) {
            $mediaUrls = [];
            foreach ($request->file('media') as $file) {
                $path = $file->store('posts', 'public');
                $mediaUrls[] = $path;
            }
            $post->update(['media' => $mediaUrls]);
        }

        // Dispatch publishing jobs if not scheduled
        if (!isset($validated['scheduled_at'])) {
            $this->dispatchPublishJobs($post);
        }

        return redirect()->route('posts.show', $post)
            ->with('success', 'Post created successfully!');
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post): Response
    {
        $post->load([
            'user.profile',
            'comments.user.profile',
            'likes',
            'reposts.user.profile'
        ]);

        return Inertia::render('Posts/Show', [
            'post' => $post,
        ]);
    }

    /**
     * Update the specified post.
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'content' => 'required|string|max:5000',
            'platforms' => 'nullable|array',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $post->update($validated);

        return back()->with('success', 'Post updated successfully!');
    }

    /**
     * Remove the specified post.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully!');
    }

    /**
     * Publish a draft post.
     */
    public function publish(Post $post)
    {
        $this->authorize('update', $post);

        if ($post->status !== 'draft') {
            return back()->with('error', 'Only draft posts can be published.');
        }

        $this->dispatchPublishJobs($post);

        $post->update(['status' => 'published']);

        return back()->with('success', 'Post is being published!');
    }

    /**
     * Dispatch publishing jobs to social media platforms.
     */
    private function dispatchPublishJobs(Post $post): void
    {
        $platforms = $post->platforms ?? [];

        foreach ($platforms as $platform) {
            match($platform) {
                'facebook' => PublishToFacebook::dispatch($post),
                'instagram' => PublishToInstagram::dispatch($post),
                'twitter', 'x' => PublishToX::dispatch($post),
                default => null,
            };
        }

        // Increment subscription posts counter
        $subscription = Auth::user()->subscription;
        if ($subscription) {
            $subscription->incrementPostsUsed();
        }
    }
}
