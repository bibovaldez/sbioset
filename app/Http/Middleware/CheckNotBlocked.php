<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\BlockedEntity;

class CheckNotBlocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next )
    {

        $email = $request->user() ? $request->user()->email : null;
        $ip = $request->ip();
        // display access denied 
        if (BlockedEntity::isBlocked($email, $ip)) {
            $message = __('Access denied. Your account or IP address has been blocked.');
            abort(403, $message);
        }
        return $next($request);
    }
}
