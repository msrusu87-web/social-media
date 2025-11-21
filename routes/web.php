<?php

use App\Http\Controllers\InstallController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Installer routes
Route::prefix('install')->name('install.')->group(function () {
    Route::get('/', [InstallController::class, 'requirements'])->name('requirements');
    Route::get('/configuration', [InstallController::class, 'configuration'])->name('configuration');
    Route::post('/configuration', [InstallController::class, 'saveConfiguration'])->name('save-configuration');
    Route::get('/install', [InstallController::class, 'install'])->name('install');
    Route::post('/run', [InstallController::class, 'runInstall'])->name('run');
    Route::get('/complete', [InstallController::class, 'complete'])->name('complete');
});
