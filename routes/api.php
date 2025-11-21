<?php

use App\Http\Controllers\API\AIContentController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\SocialConnectionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // User Route
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Post Routes
    Route::apiResource('posts', PostController::class);

    // AI Content Routes
    Route::prefix('ai')->group(function () {
        Route::post('/generate', [AIContentController::class, 'generate']);
        Route::post('/hashtags', [AIContentController::class, 'generateHashtags']);
        Route::post('/improve', [AIContentController::class, 'improve']);
    });

    // Social Connection Routes
    Route::prefix('connections')->group(function () {
        Route::get('/', [SocialConnectionController::class, 'index']);
        Route::get('/auth-url', [SocialConnectionController::class, 'getAuthUrl']);
        Route::post('/callback', [SocialConnectionController::class, 'callback']);
        Route::delete('/{connection}', [SocialConnectionController::class, 'disconnect']);
        Route::post('/{connection}/refresh', [SocialConnectionController::class, 'refresh']);
    });
});

// Public API Routes
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});
