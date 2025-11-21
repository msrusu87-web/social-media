<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class FeedController extends Controller
{
    /**
     * Display the main feed.
     */
    public function index(): Response
    {
        $user = Auth::user();

        // Get posts from users the current user is following
        $followingIds = $user->following()->pluck('users.id');

        $posts = Post::whereIn('user_id', $followingIds)
            ->orWhere('user_id', $user->id)
            ->with([
                'user.profile',
                'comments.user.profile',
                'likes',
                'reposts.user.profile'
            ])
            ->published()
            ->latest()
            ->paginate(20);

        return Inertia::render('Feed/Index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Display the explore feed with trending posts.
     */
    public function explore(): Response
    {
        $posts = Post::withCount(['likes', 'comments', 'reposts'])
            ->with([
                'user.profile',
                'comments.user.profile',
                'likes',
                'reposts.user.profile'
            ])
            ->published()
            ->orderByDesc('likes_count')
            ->orderByDesc('comments_count')
            ->orderByDesc('created_at')
            ->paginate(20);

        return Inertia::render('Feed/Explore', [
            'posts' => $posts,
        ]);
    }

    /**
     * Display a user's profile feed.
     */
    public function userFeed(User $user): Response
    {
        $posts = $user->posts()
            ->with([
                'user.profile',
                'comments.user.profile',
                'likes',
                'reposts.user.profile'
            ])
            ->published()
            ->latest()
            ->paginate(20);

        $isFollowing = Auth::user()->isFollowing($user);

        return Inertia::render('Feed/UserFeed', [
            'user' => $user->load('profile'),
            'posts' => $posts,
            'isFollowing' => $isFollowing,
            'followersCount' => $user->followers()->count(),
            'followingCount' => $user->following()->count(),
        ]);
    }
}
