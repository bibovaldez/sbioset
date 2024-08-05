<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Team;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login');
        }
        
        $user = Auth::user();
        $team = Team::find($user->current_team_id);

        if (!$user instanceof User) {
            return abort(403, 'Invalid user type.');
        }
        // super admin 
        if ($user->isAdmin()) {
            // Check if the current route is already 'admin.dashboard' or 'admin.calendar'
            if (!$request->routeIs('admin.dashboard') && !$request->routeIs('admin.calendar') && !$request->routeIs('admin.upload')) {
                return redirect()->route('admin.dashboard');
            }
            return $next($request);
        }
        // sub-admin
        if ($user->hasTeamRole($team, 'admin')) {
            if (!$request->routeIs('subadmin.dashboard')) {
                return redirect()->route('subadmin.dashboard');
            }
            return $next($request);
        }


        if ($user->isUser()) {
            if (!$request->routeIs('dashboard')) {
                return redirect()->route('dashboard');
            }
            return $next($request);
        }
        
        if (!empty($roles) && !$user->hasAnyRole($roles)) {
            return abort(403, 'Unauthorized action.');
        }
        
        return $next($request);
    }
}