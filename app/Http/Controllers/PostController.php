<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Display a listing of the user's posts.
     */
    public function index(): View
    {
        $posts = Auth::user()
            ->posts()
            ->with('user.profile')
            ->latest()
            ->paginate(20);

        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create(): View
    {
        return view('posts.create');
    }

    /**
     * Store a newly created post.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:5000',
            'media' => 'nullable|array',
            'media.*' => 'nullable|file|max:10240',
            'platforms' => 'required|array',
            'platforms.*' => 'string|in:facebook,instagram,twitter,tiktok,youtube,pinterest',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        // Handle media uploads
        $mediaUrls = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $mediaUrls[] = $file->store('media', 'public');
            }
        }

        $post = Auth::user()->posts()->create([
            'content' => $validated['content'],
            'media' => $mediaUrls,
            'platforms' => $validated['platforms'],
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'status' => isset($validated['scheduled_at']) ? 'scheduled' : 'published',
        ]);

        return redirect()->route('posts.show', $post)->with('success', 'Post created successfully');
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post): View
    {
        $post->load(['user.profile', 'comments.user.profile', 'likes', 'reposts.user']);

        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the post.
     */
    public function edit(Post $post): View
    {
        $this->authorize('update', $post);

        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified post.
     */
    public function update(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'content' => 'required|string|max:5000',
            'platforms' => 'required|array',
            'platforms.*' => 'string|in:facebook,instagram,twitter,tiktok,youtube,pinterest',
        ]);

        $post->update($validated);

        return redirect()->route('posts.show', $post)->with('success', 'Post updated successfully');
    }

    /**
     * Remove the specified post.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        // Delete associated media
        if ($post->media) {
            foreach ($post->media as $media) {
                Storage::disk('public')->delete($media);
            }
        }

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully');
    }
}
