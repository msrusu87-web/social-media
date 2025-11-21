<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RepostController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes (handled by Laravel Breeze/Fortify)
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [FeedController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Follow Routes
    Route::post('/follow/{user}', [FollowController::class, 'store'])->name('follow.store');
    Route::delete('/unfollow/{user}', [FollowController::class, 'destroy'])->name('follow.destroy');

    // Feed Routes
    Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');
    Route::get('/discover', [FeedController::class, 'discover'])->name('feed.discover');
    Route::get('/trending', [FeedController::class, 'trending'])->name('feed.trending');

    // Post Routes
    Route::resource('posts', PostController::class);

    // Comment Routes
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::patch('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Like Routes
    Route::post('/likes', [LikeController::class, 'store'])->name('likes.store');
    Route::delete('/likes', [LikeController::class, 'destroy'])->name('likes.destroy');

    // Repost Routes
    Route::post('/posts/{post}/repost', [RepostController::class, 'store'])->name('reposts.store');
    Route::delete('/posts/{post}/repost', [RepostController::class, 'destroy'])->name('reposts.destroy');

    // Subscription Routes
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('/subscriptions/{plan}', [SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');
    Route::post('/subscriptions/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::post('/subscriptions/resume', [SubscriptionController::class, 'resume'])->name('subscriptions.resume');
    Route::post('/subscriptions/{plan}/change', [SubscriptionController::class, 'change'])->name('subscriptions.change');
});

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');
    
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// Social Authentication Routes
Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');
