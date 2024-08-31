<?php

use App\Http\Controllers\Admin\AddmemberController;
use App\Http\Controllers\Admin\CalendarController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageCaptureController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\RoutePath;
use App\Http\Controllers\CurrentTeamController;
use App\Http\Controllers\SubadminController;
use Laravel\Jetstream\Jetstream;
use App\Http\Controllers\RegisteredUserController;
use Laravel\Jetstream\Http\Controllers\Livewire\TeamController;
use App\Http\Controllers\TeamInvitationController;


// Security Routes
Route::group(['middleware' => array_merge(
    config('fortify.middleware', ['web']),
    ['check.not.blocked']
)], function () {
    //  Landing page
    Route::get('/', fn() => view('welcome'));

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

    // Admin's Routes
    Route::middleware([
        'auth:sanctum,admin',
        config('jetstream.auth_session'),
        'verified',
        'checkRole',
        'limit.sessions',
        'xframe',
        'secure.headers',
        'hsts',
    ])->group(function () {
        // SUPER ADMIN ROUTES
        Route::get('/admin/dashboard', [DashboardController::class, 'dashboard'])
            ->name('admin.dashboard');
        Route::get('/admin/calendar', [CalendarController::class, 'show'])
            ->name('admin.calendar');
        Route::get('/admin/addmember', [AddmemberController::class, 'addMember'])
            ->name('admin.add-member');
        Route::post('/admin/addmember', [RegisteredUserController::class, 'store'])
            ->name('admin.add-member');

        // SUB ADMIN ROUTES
        Route::get('/subadmin/dashboard', [DashboardController::class, 'dashboard'])
            ->name('subadmin.dashboard');
        Route::get('/subadmin/calendar', [CalendarController::class, 'show'])
            ->name('subadmin.calendar');

        // Team Routes
        Route::group(['middleware' => config('jetstream.middleware', ['web'])], function () {
            Route::group(['middleware' => 'verified'], function () {
                // Teams...
                if (Jetstream::hasTeamFeatures()) {
                    Route::put('/current-team', [CurrentTeamController::class, 'update'])->name('current-team.update');
                    Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create');
                    Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');
                }
            });
        });
    });
    // User Routes
    Route::middleware([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
        'limit.sessions',
        // 'checkRole', fix it later cant upload if this is enabled
        'check.team.status',
        'xframe',
        'secure.headers',
        'hsts',
    ])->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
        Route::post('/image/upload', [ImageCaptureController::class, 'upload'])->name('image.upload');
    });
});



// Teams invite routes
Route::group(['middleware' => config('jetstream.middleware', ['web'])], function () {
    $authMiddleware = config('jetstream.guard')
        ? 'auth:' . config('jetstream.guard')
        : 'auth';
    $authSessionMiddleware = config('jetstream.auth_session', false)
        ? config('jetstream.auth_session')
        : null;
    Route::group(['middleware' => array_values(array_filter([$authMiddleware, $authSessionMiddleware]))], function () {
        Route::group(['middleware' => 'verified'], function () {
            if (Jetstream::hasTeamFeatures()) {
                Route::get('/team-invitations/{invitation}', [TeamInvitationController::class, 'accept'])
                    ->middleware(['signed'])
                    ->name('team-invitations.accept');
            }
        });
    });
});
