<?php

use App\Http\Controllers\API\AIContentController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\SocialConnectionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // User
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Posts
    Route::apiResource('posts', PostController::class);

    // AI Content
    Route::post('/ai/generate', [AIContentController::class, 'generate']);
    Route::post('/ai/hashtags', [AIContentController::class, 'generateHashtags']);

    // Social Connections
    Route::get('/social/connections', [SocialConnectionController::class, 'index']);
    Route::post('/social/connect', [SocialConnectionController::class, 'connect']);
    Route::post('/social/disconnect/{platform}', [SocialConnectionController::class, 'disconnect']);
    Route::get('/social/auth-url/{platform}', [SocialConnectionController::class, 'getAuthUrl']);
});
