<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageCaptureController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\RoutePath;
use App\Http\Controllers\CurrentTeamController;
use App\Http\Controllers\SubadminController;
use Laravel\Jetstream\Jetstream;
use App\Http\Controllers\RegisteredUserController;


Route::get('/', function () {
    return view('welcome');
});


// Security Routes
Route::group(['middleware' => config('fortify.middleware', ['web'])], function () {
    $enableViews = config('fortify.views', true);
    $limiter = config('fortify.limiters.login');
   
    // Authentication Routes
    if ($enableViews) {
        Route::get(RoutePath::for('login', '/login'), [AuthenticatedSessionController::class, 'create'])
            ->middleware(['guest:' . config('fortify.guard')])
            ->name('login');
    }

    Route::post(RoutePath::for('login', '/login'), [AuthenticatedSessionController::class, 'store'])
        ->middleware(array_filter([
            'guest:' . config('fortify.guard'),
            $limiter ? 'throttle:' . $limiter : null,
        ]));
    Route::post(RoutePath::for('logout', '/logout'), [AuthenticatedSessionController::class, 'destroy'])
        ->middleware([config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard')])
        ->name('logout');

    // Admin Routes
    Route::middleware([
        'auth:sanctum,admin',
        config('jetstream.auth_session'),
        'verified',
        'checkRole',
    ])->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
            ->name('admin.dashboard');
        Route::get('/admin/calendar', [AdminController::class, 'calendar'])
            ->name('admin.calendar');
        Route::get('/admin/upload', [AdminController::class, 'upload'])
            ->name('admin.upload');
        Route::get('/admin/addmember', [AdminController::class, 'addMember'])
            ->name('admin.add-member');
        Route::post('/admin/addmember', [RegisteredUserController::class, 'store'])
            ->name('admin.add-member');
    });
    // User Routes
    Route::middleware([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
        'checkRole',
    ])->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
        Route::post('/image/upload', [ImageCaptureController::class, 'upload'])->name('image.upload');
    });
    // Sub-Admin Routes
    Route::middleware([
        'auth:sanctum,admin',
        config('jetstream.auth_session'),
        'verified',
        'checkRole',
    ])->group(function () {
        Route::get('/subadmin/dashboard', [SubadminController::class, 'subadminDashboard'])
            ->name('subadmin.dashboard');
        Route::get('/subadmin/calendar', [SubadminController::class, 'subadminCalendar'])
            ->name('subadmin.calendar');
    });
});


// Team Routes
Route::group(['middleware' => config('jetstream.middleware', ['web'])], function () {
    Route::group(['middleware' => 'verified'], function () {
        // Teams...
        if (Jetstream::hasTeamFeatures()) {
            Route::put('/current-team', [CurrentTeamController::class, 'update'])->name('current-team.update');
        }
    });
});
