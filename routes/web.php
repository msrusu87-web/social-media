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

Route::get('/', function () {
    return redirect('/dashboard');
});

// Social Authentication
Route::prefix('auth')->group(function () {
    Route::get('/{provider}', [SocialAuthController::class, 'redirect']);
    Route::get('/{provider}/callback', [SocialAuthController::class, 'callback']);
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [FeedController::class, 'index'])->name('dashboard');
    
    // Explore
    Route::get('/explore', [FeedController::class, 'explore'])->name('explore');

    // Profile
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/{user}/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/{user}', [ProfileController::class, 'update'])->name('profile.update');

    // Posts
    Route::resource('posts', PostController::class);
    
    // Comments
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    // Likes
    Route::post('/posts/{post}/like', [LikeController::class, 'like']);
    Route::delete('/posts/{post}/like', [LikeController::class, 'unlike']);

    // Reposts
    Route::post('/posts/{post}/repost', [RepostController::class, 'repost']);
    Route::delete('/posts/{post}/repost', [RepostController::class, 'unrepost']);

    // Follow
    Route::post('/users/{user}/follow', [FollowController::class, 'follow']);
    Route::post('/users/{user}/unfollow', [FollowController::class, 'unfollow']);
    Route::get('/users/{user}/followers', [FollowController::class, 'followers']);
    Route::get('/users/{user}/following', [FollowController::class, 'following']);

    // Subscriptions
    Route::prefix('subscription')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index'])->name('subscription.index');
        Route::post('/subscribe/{plan}', [SubscriptionController::class, 'subscribe']);
        Route::post('/cancel', [SubscriptionController::class, 'cancel']);
        Route::post('/change-plan/{plan}', [SubscriptionController::class, 'changePlan']);
    });
});

// Email Verification
Route::middleware('auth')->group(function () {
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->name('verification.send');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->name('verification.verify');
});

require __DIR__.'/auth.php';
