<?php

use App\Http\Controllers\Admin\{AddmemberController, CalendarController, DashboardController};
use App\Http\Controllers\{AuthenticatedSessionController, CurrentTeamController, ImageCaptureController, LogoutController, RegisteredUserController, TeamInvitationController};
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\RoutePath;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Http\Controllers\Livewire\TeamController;

// Common middleware groups

// Security Routes
Route::middleware(array_merge(config('fortify.middleware', ['web'])))->group(function () use ($authMiddleware) {
    // Landing page
    Route::get('/', fn() => view('welcome'));
    // Logout other sessions
    Route::get('/logout-other-sessions/{token}', [LogoutController::class, 'logoutOtherSessions'])
        ->name('logout.other.sessions');

    // Authentication Routes
    if (config('fortify.views', true)) {
        Route::get(RoutePath::for('login', '/login'), [AuthenticatedSessionController::class, 'create'])
            ->middleware(['guest:' . config('fortify.guard')])
            ->name('login');
    }

    Route::post(RoutePath::for('login', '/login'), [AuthenticatedSessionController::class, 'store'])
        ->middleware(array_filter([
            'guest:' . config('fortify.guard'),
            config('fortify.limiters.login') ? 'throttle:' . config('fortify.limiters.login') : null,
            'honeypot',
        ]));

    Route::post(RoutePath::for('logout', '/logout'), [AuthenticatedSessionController::class, 'destroy'])
        ->middleware([config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard')])
        ->name('logout');

    // Admin Routes
    Route::middleware(array_merge($authMiddleware, ['checkRole']))->group(function () {
        // Super Admin Routes
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
            Route::get('/calendar', [CalendarController::class, 'show'])->name('calendar');
            Route::get('/addmember', [AddmemberController::class, 'addMember'])->name('add-member');
            Route::post('/addmember/save', [RegisteredUserController::class, 'store'])->name('add-member.save');
        });

      
    });
       // Sub Admin Routes
       Route::prefix('subadmin')->name('subadmin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/calendar', [CalendarController::class, 'show'])->name('calendar');
    });

    // User Routes
    Route::middleware($authMiddleware)->group(function () {
        Route::view('/dashboard', 'dashboard')->name('dashboard');
    });
    Route::middleware($authMiddleware)->group(function () {
        Route::post('/image/upload', [ImageCaptureController::class, 'upload'])->name('image.upload');
    });

    // Team Routes
    if (Jetstream::hasTeamFeatures()) {
        Route::middleware(array_merge(config('jetstream.middleware', ['web']), ['verified']))->group(function () {
            Route::put('/current-team', [CurrentTeamController::class, 'update'])->name('current-team.update');
            Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create');
            Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');
        });

        // Team Invitation Routes
        Route::middleware(array_filter([
            config('jetstream.guard') ? 'auth:' . config('jetstream.guard') : 'auth',
            config('jetstream.auth_session', false),
            'verified',
        ]))->group(function () {
            Route::get('/team-invitations/{invitation}', [TeamInvitationController::class, 'accept'])
                ->middleware(['signed'])
                ->name('team-invitations.accept');
        });
    }
});
