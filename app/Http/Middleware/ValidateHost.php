<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ValidateHost
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Define the allowed hosts (whitelisted domains)
        $allowedHosts = [
            'sbioset.com',
            'www.sbioset.com',
        ];

        // Get the Host header from the request
        $host = $request->header('Host');

        // Check if the host is in the allowed hosts list
        if (!in_array($host, $allowedHosts)) {
            // If the host is not allowed, return a 403 Forbidden response
            return response('Forbidden', 403);
        }

        // Proceed with the request if the host is valid
        return $next($request);
    }
}
