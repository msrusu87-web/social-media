<?php

use App\Http\Controllers\API\AIContentController;
use App\Http\Controllers\API\PostController as APIPostController;
use App\Http\Controllers\API\SocialConnectionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    // AI Content Generation
    Route::prefix('ai')->group(function () {
        Route::post('/generate', [AIContentController::class, 'generate']);
        Route::post('/enhance', [AIContentController::class, 'enhance']);
        Route::post('/hashtags', [AIContentController::class, 'hashtags']);
    });

    // Posts
    Route::apiResource('posts', APIPostController::class);

    // Social Connections
    Route::prefix('social-connections')->group(function () {
        Route::get('/', [SocialConnectionController::class, 'index']);
        Route::post('/connect', [SocialConnectionController::class, 'connect']);
        Route::delete('/{connection}', [SocialConnectionController::class, 'disconnect']);
        Route::post('/{connection}/refresh', [SocialConnectionController::class, 'refresh']);
    });
});
