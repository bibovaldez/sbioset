<?php

use App\Http\Controllers\{ ImageCaptureController};
use Illuminate\Support\Facades\Route;

// Common middleware groups
$securityMiddleware = ['check.not.blocked', 'http.redirect', 'xframe', 'secure.headers', 'hsts'];
$authMiddleware = [
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'limit.sessions',
    'check.team.status',
    'honeypot',
];

// Security Routes
Route::middleware(array_merge(config('fortify.middleware', ['web']), $securityMiddleware))->group(function () use ($authMiddleware) {
    // User Routes
    Route::middleware($authMiddleware)->group(function () {
        Route::post('/image/upload', [ImageCaptureController::class, 'upload'])->name('image.upload');
    });
});

