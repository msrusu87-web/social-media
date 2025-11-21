<?php

namespace App\Http\Controllers;

use App\Events\PostPublished;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $posts = Post::with(['user.profile', 'comments', 'likes'])
            ->withCount(['comments', 'likes', 'reposts'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Posts/Index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Posts/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
            'media_url' => 'nullable|url',
            'platforms' => 'nullable|array',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $post = $request->user()->posts()->create([
            'content' => $request->input('content'),
            'media_url' => $request->input('media_url'),
            'platforms' => $request->input('platforms'),
            'scheduled_at' => $request->input('scheduled_at'),
            'is_published' => !$request->has('scheduled_at'),
        ]);

        if (!$request->has('scheduled_at')) {
            PostPublished::dispatch($post);
        }

        return redirect()->route('posts.show', $post)->with('success', 'Post created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): Response
    {
        $post->load(['user.profile', 'comments.user.profile', 'likes', 'reposts']);
        $post->loadCount(['comments', 'likes', 'reposts']);

        return Inertia::render('Posts/Show', [
            'post' => $post,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $request->validate([
            'content' => 'required|string|max:5000',
            'media_url' => 'nullable|url',
        ]);

        $post->update($request->only(['content', 'media_url']));

        return redirect()->route('posts.show', $post)->with('success', 'Post updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()->route('dashboard')->with('success', 'Post deleted successfully');
    }
}
