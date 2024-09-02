<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\LogoutNotification;

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
                
                // Check if a logout token already exists for the user
                $logoutToken = DB::table('logout_tokens')
                    ->where('user_id', Auth::id())
                    ->first();

                if (!$logoutToken) {
                    // Generate a new token if none exists
                    $token = Str::random(60);
                    DB::table('logout_tokens')->insert([
                        'user_id' => Auth::id(),
                        'token' => $token,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    // Use the existing token
                    $token = $logoutToken->token;
                }

                // Generate the logout link
                $logoutLink = route('logout.other.sessions', $token);

                // Send the logout email with the link
                Mail::to(Auth::user()->email)->send(new LogoutNotification($logoutLink));

                // Log out the user from the current session
                Auth::guard('web')->logout();

                // Redirect to the login page with an error message
                return redirect()->route('login')->with('error', 'Multiple sessions detected. Check your email to log out from other sessions.');
            }
        }

        return $next($request);
    }
}
