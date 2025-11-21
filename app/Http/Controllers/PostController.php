<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\SocialMediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    protected $socialMediaService;

    public function __construct(SocialMediaService $socialMediaService)
    {
        $this->socialMediaService = $socialMediaService;
    }

    /**
     * Display a listing of posts.
     */
    public function index(Request $request)
    {
        $posts = $request->user()->posts()
            ->with(['likes', 'comments', 'reposts'])
            ->latest()
            ->paginate(20);

        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created post.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:5000',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,gif,mp4,mov|max:51200',
            'platforms' => 'nullable|array',
            'platforms.*' => 'in:facebook,instagram,x,tiktok,youtube,pinterest',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $mediaFiles = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $mediaFiles[] = $file->store('posts', 'public');
            }
        }

        $post = $request->user()->posts()->create([
            'content' => $request->content,
            'media' => $mediaFiles,
            'platforms' => $request->platforms,
            'status' => $request->scheduled_at ? 'scheduled' : 'draft',
            'scheduled_at' => $request->scheduled_at,
        ]);

        // If no scheduled time, publish immediately
        if (!$request->scheduled_at && $request->platforms) {
            $this->socialMediaService->publishPost($post);
        }

        return redirect()->route('posts.show', $post)
            ->with('success', 'Post created successfully');
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post)
    {
        $post->load(['user.profile', 'comments.user.profile', 'likes', 'reposts']);

        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified post.
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:5000',
            'platforms' => 'nullable|array',
            'status' => 'sometimes|in:draft,scheduled,published',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $post->update($request->only([
            'content', 'platforms', 'status', 'scheduled_at'
        ]));

        return redirect()->route('posts.show', $post)
            ->with('success', 'Post updated successfully');
    }

    /**
     * Remove the specified post.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully');
    }

    /**
     * Publish a draft post.
     */
    public function publish(Post $post)
    {
        $this->authorize('update', $post);

        if ($post->platforms) {
            $this->socialMediaService->publishPost($post);
        }

        $post->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return back()->with('success', 'Post published successfully');
    }
}
