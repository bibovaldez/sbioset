<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckTeamStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // check if the user has a team and if the user is active
        $user = $request->user();
        

        // Check if the user has a team and if the user is active
        if ( ! $user->hasTeam() || ! $user->isActive()) {
            // Log out the user
            return response()->view('auth.logout');
        }

        // If everything is okay, proceed with the request
        return $next($request);
    }
}
