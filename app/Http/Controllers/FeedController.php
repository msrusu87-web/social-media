<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    /**
     * Display the feed.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Get posts from users the current user is following
        $followingIds = $user->following()->pluck('id');
        $followingIds->push($user->id); // Include user's own posts

        $posts = Post::with(['user.profile', 'likes', 'comments', 'reposts'])
            ->whereIn('user_id', $followingIds)
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate(20);

        return view('feed.index', compact('posts'));
    }

    /**
     * Display the discover feed.
     */
    public function discover(Request $request)
    {
        $posts = Post::with(['user.profile', 'likes', 'comments', 'reposts'])
            ->where('status', 'published')
            ->withCount('likes')
            ->orderBy('likes_count', 'desc')
            ->latest('published_at')
            ->paginate(20);

        return view('feed.discover', compact('posts'));
    }

    /**
     * Display trending posts.
     */
    public function trending()
    {
        $posts = Post::with(['user.profile', 'likes', 'comments', 'reposts'])
            ->where('status', 'published')
            ->where('published_at', '>=', now()->subDays(7))
            ->withCount(['likes', 'comments', 'reposts'])
            ->orderByRaw('(likes_count * 2 + comments_count * 3 + reposts_count * 4) DESC')
            ->paginate(20);

        return view('feed.trending', compact('posts'));
    }
}
