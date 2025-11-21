<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FollowController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard', [
            'stats' => [
                'totalPosts' => auth()->user()->posts()->count(),
                'postsThisMonth' => auth()->user()->posts()
                    ->whereMonth('created_at', now()->month)->count(),
                'followers' => auth()->user()->followers()->count(),
                'newFollowers' => auth()->user()->followers()
                    ->wherePivot('created_at', '>=', now()->subWeek())->count(),
                'engagement' => 8.5, // Calculate based on likes, comments, etc.
                'postsRemaining' => auth()->user()->subscription 
                    ? (auth()->user()->subscription->plan->posts_per_month - auth()->user()->subscription->posts_used)
                    : 0,
                'postsLimit' => auth()->user()->subscription
                    ? auth()->user()->subscription->plan->posts_per_month
                    : 0,
            ],
            'recentPosts' => auth()->user()->posts()
                ->with(['likes', 'comments'])
                ->latest()
                ->take(5)
                ->get(),
            'scheduledPosts' => auth()->user()->posts()
                ->scheduled()
                ->orderBy('scheduled_at')
                ->take(5)
                ->get(),
            'socialConnections' => auth()->user()->socialConnections()->get(),
        ]);
    })->name('dashboard');

    // Post routes
    Route::resource('posts', PostController::class);
    Route::post('/posts/{post}/publish', [PostController::class, 'publish'])->name('posts.publish');

    // Feed routes
    Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');
    Route::get('/explore', [FeedController::class, 'explore'])->name('feed.explore');
    Route::get('/users/{user}', [FeedController::class, 'userFeed'])->name('users.feed');

    // Follow routes
    Route::post('/users/{user}/follow', [FollowController::class, 'follow'])->name('users.follow');
    Route::delete('/users/{user}/unfollow', [FollowController::class, 'unfollow'])->name('users.unfollow');
    Route::get('/users/{user}/followers', [FollowController::class, 'followers'])->name('users.followers');
    Route::get('/users/{user}/following', [FollowController::class, 'following'])->name('users.following');
});
