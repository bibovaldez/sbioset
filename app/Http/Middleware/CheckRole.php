<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Team;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class CheckRole
{
    private const ROLE_ROUTES = [
        'admin' => 'admin.dashboard',
        'sub-admin' => 'admin.dashboard',
        'user' => 'dashboard'
    ];

    private const ADMIN_ALLOWED_ROUTES = [
        'admin.dashboard',
        'admin.calendar',
        'admin.add-member',
        'admin.add-member.save',
        'current-team.update',
        'teams.create',
        'teams.show',
        'image.upload',
        'dashboard'
    ];
    private const SUBADMIN_ALLOWED_ROUTES = [
        'admin.dashboard',
        'admin.calendar',
        'image.upload',
        'dashboard'
    ];
    private const USER_ALLOWED_ROUTES = [
        'image.upload',
        'dashboard'
    ];

    public function handle(Request $request, Closure $next)
    {
        // $mysqldumpPath = shell_exec('which mysqldump'); // Check if mysqldump is found
        // Log::info('mysqldump path: ' . $mysqldumpPath);
        // Artisan::call('backup:run');

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user instanceof User) {
            return abort(403, 'Invalid user type.');
        }

        $userRole = $this->getUserRole($user);

        // Check if the user's role is defined in ROLE_ROUTES
        if ($userRole && isset(self::ROLE_ROUTES[$userRole])) {
            $intendedRoute = self::ROLE_ROUTES[$userRole];

            // Handle redirection based on role
            if ($userRole === 'admin') {
                // Admin can access specified routes
                if (!$request->routeIs(self::ADMIN_ALLOWED_ROUTES)) {
                    return $this->redirectToRoleDashboard($intendedRoute);
                }
            
            } elseif ($userRole === 'sub-admin') {
                // Sub-admin can access specified routes
                if (!$request->routeIs(self::SUBADMIN_ALLOWED_ROUTES)) {
                    return $this->redirectToRoleDashboard($intendedRoute);
                }
            } elseif ($userRole === 'user') {
                // User can access specified routes
                if (!$request->routeIs(self::USER_ALLOWED_ROUTES)) {
                    return $this->redirectToRoleDashboard($intendedRoute);
                }
            }

            return $next($request);
        }

        // Check if the user has one of the required roles for the request
        if (!empty($roles) && !$user->hasAnyRole($roles)) {
            return abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }

    private function getUserRole(User $user): ?string
    {
        // Determine the user's role
        if ($user->isAdmin()) {
            return 'admin';
        }

        $team = Team::find($user->current_team_id);
        if ($user->isUser() && $user->hasTeamRole($team, 'admin')) {
            return 'sub-admin';
        }

        if ($user->isUser()) {
            return 'user';
        }
        return null;
    }

    private function redirectToRoleDashboard(string $route)
    {
        // Redirect to the dashboard based on the role
        return redirect()->route($route);
    }
}
