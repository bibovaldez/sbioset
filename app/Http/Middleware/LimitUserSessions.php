<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LimitUserSessions
{
    protected $maxSessions = 1;

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {

            // Get all sessions for the user with the same device identifier
            $sessions = DB::table('sessions')
                ->where('user_id', Auth::id())
                ->where('user_agent', $request->userAgent())
                ->where('ip_address', $request->ip())
                ->get();

            // Check if the user has more than the allowed number of sessions for this device
            if ($sessions->count() > $this->maxSessions) {
                // Logout the user from the current session
                Auth::guard('web')->logout();

                return redirect()->route('login')
                    ->with('error', 'Your account is already logged in on another device');
            } else {
                return $next($request);
            }
        }

        return $next($request);
    }

    protected function getuserAgent(Request $request)
    {
        // Generate a unique identifier based on user-agent and IP address
        return md5($request->userAgent() . $request->ip());
    }
}
