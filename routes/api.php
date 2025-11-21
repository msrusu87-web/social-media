<?php

use App\Http\Controllers\API\AIContentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// AI Content routes
Route::middleware('auth:sanctum')->prefix('ai')->group(function () {
    Route::post('/generate', [AIContentController::class, 'generate']);
    Route::post('/improve', [AIContentController::class, 'improve']);
    Route::post('/hashtags', [AIContentController::class, 'generateHashtags']);
});
