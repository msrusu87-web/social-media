<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RepostController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile
    Route::get('/profile/{username}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Posts
    Route::resource('posts', PostController::class);
    Route::post('/posts/{post}/publish', [PostController::class, 'publish'])->name('posts.publish');

    // Comments
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Likes
    Route::post('/likes', [LikeController::class, 'store'])->name('likes.store');
    Route::delete('/likes', [LikeController::class, 'destroy'])->name('likes.destroy');

    // Reposts
    Route::post('/posts/{post}/repost', [RepostController::class, 'store'])->name('reposts.store');
    Route::delete('/posts/{post}/repost', [RepostController::class, 'destroy'])->name('reposts.destroy');

    // Follow
    Route::post('/users/{user}/follow', [FollowController::class, 'follow'])->name('follow');
    Route::delete('/users/{user}/follow', [FollowController::class, 'unfollow'])->name('unfollow');
    Route::get('/users/{user}/followers', [FollowController::class, 'followers'])->name('followers');
    Route::get('/users/{user}/following', [FollowController::class, 'following'])->name('following');

    // Feed
    Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');
    Route::get('/discover', [FeedController::class, 'discover'])->name('feed.discover');
    Route::get('/trending', [FeedController::class, 'trending'])->name('feed.trending');

    // Subscriptions
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('/subscriptions/{plan}', [SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');
    Route::post('/subscriptions/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::post('/subscriptions/resume', [SubscriptionController::class, 'resume'])->name('subscriptions.resume');
});

// Email Verification
Route::middleware('auth')->group(function () {
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->name('verification.send');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->name('verification.verify');
});

// Social Authentication
Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

// Stripe Webhooks
Route::post('/webhooks/stripe', [SubscriptionController::class, 'webhook'])->name('webhooks.stripe');

require __DIR__.'/auth.php';
