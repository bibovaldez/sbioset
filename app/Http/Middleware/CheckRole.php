<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Team;

class CheckRole
{
    private const ROLE_ROUTES = [
        'admin' => 'admin.dashboard',
        'sub-admin' => 'subadmin.dashboard',
        'user' => 'dashboard'
    ];

    private const ADMIN_ALLOWED_ROUTES = ['admin.dashboard', 'admin.calendar', 'admin.upload'];

    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        if (!$user instanceof User) {
            return abort(403, 'Invalid user type.');
        }

        $userRole = $this->getUserRole($user);

        if ($userRole && isset(self::ROLE_ROUTES[$userRole])) {
            $intendedRoute = self::ROLE_ROUTES[$userRole];
            if ($userRole === 'admin' && !$request->routeIs(self::ADMIN_ALLOWED_ROUTES)) {
                return $this->redirectToRoleDashboard($intendedRoute, $userRole);
            } elseif ($userRole !== 'admin' && !$request->routeIs($intendedRoute)) {
                return $this->redirectToRoleDashboard($intendedRoute, $userRole);
            }
            
            return $next($request);
        }

        if (!empty($roles) && !$user->hasAnyRole($roles)) {
            return abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }

    private function getUserRole(User $user): ?string
    {
        if ($user->isAdmin()) {
            return 'admin';
        }

        $team = Team::find($user->current_team_id);
        if ($user->hasTeamRole($team, 'admin')) {
            return 'sub-admin';
        }

        if ($user->isUser()) {
            return 'user';
        }

        return null;
    }

    public function redirectToRoleDashboard(string $route, string $role)
    {
        return redirect()->route($route)->with('role', $role);
    }
}