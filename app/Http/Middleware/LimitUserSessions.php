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

    protected $maxSessions = 4;
    public function handle(Request $request, Closure $next)
    {   
        if (Auth::check()) {
            $userId = Auth::id();
            $sessionId = Session::getId();

            // Get all sessions for the user
            $sessions = DB::table('sessions')
                ->where('user_id', $userId)
                ->orderBy('last_activity', 'desc')
                ->get();

            // Check if the user has more than the allowed number of sessions
            if ($sessions->count() > $this->maxSessions) {
                // return redirect()->route('login') with an error message
                return redirect()->route('login')
                    ->with('error', 'You have exceeded the maximum number of allowed sessions.');
            }else{
                return $next($request);
            }
        }
    }
}
