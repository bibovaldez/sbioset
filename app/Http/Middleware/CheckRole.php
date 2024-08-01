<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login');
        }
        $user = Auth::user();
        if (!$user instanceof User) {
            return abort(403, 'Invalid user type.');
        }
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }
        
        if ($user->isAdmin()) {
            return $next($request);
        }
        if ($user->isUser()) {
            return redirect()->route('dashboard');
        }
        return abort(403, 'Unauthorized action.');
    }
}