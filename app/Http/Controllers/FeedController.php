<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeedController extends Controller
{
    /**
     * Display the user's feed
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        // Get posts from followed users and own posts
        $followingIds = $user->following()->pluck('following_id')->toArray();
        $feedIds = array_merge($followingIds, [$user->id]);

        $posts = Post::whereIn('user_id', $feedIds)
            ->with(['user.profile', 'comments.user', 'likes', 'reposts'])
            ->withCount(['comments', 'likes', 'reposts'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Dashboard', [
            'posts' => $posts,
        ]);
    }

    /**
     * Display the explore/discover feed
     */
    public function explore(): Response
    {
        $posts = Post::with(['user.profile', 'comments.user', 'likes', 'reposts'])
            ->withCount(['comments', 'likes', 'reposts'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Feed/Explore', [
            'posts' => $posts,
        ]);
    }
}
