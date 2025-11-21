<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FeedController extends Controller
{
    /**
     * Display the main feed.
     */
    public function index(): View
    {
        $user = Auth::user();

        // Get posts from users the current user follows, plus their own posts
        $followingIds = $user->following()->pluck('users.id');
        $feedIds = $followingIds->push($user->id);

        $posts = Post::whereIn('user_id', $feedIds)
            ->published()
            ->with(['user.profile', 'comments.user', 'likes', 'reposts'])
            ->latest()
            ->paginate(20);

        return view('feed.index', compact('posts'));
    }

    /**
     * Display the discover feed (public posts).
     */
    public function discover(): View
    {
        $posts = Post::published()
            ->with(['user.profile', 'comments.user', 'likes', 'reposts'])
            ->latest()
            ->paginate(20);

        return view('feed.discover', compact('posts'));
    }

    /**
     * Display trending posts.
     */
    public function trending(): View
    {
        $posts = Post::published()
            ->with(['user.profile', 'comments.user', 'likes', 'reposts'])
            ->orderByDesc('likes_count')
            ->orderByDesc('comments_count')
            ->orderByDesc('reposts_count')
            ->paginate(20);

        return view('feed.trending', compact('posts'));
    }
}
