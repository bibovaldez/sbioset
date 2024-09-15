<?php

namespace App\Http\Middleware;

use Bepsvpt\SecureHeaders\SecureHeadersMiddleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
class SecureHeaders extends SecureHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        // Call the next middleware and get the response
        $response = $next($request);
        // Ensure the response is valid before accessing headers
        if ($response instanceof SymfonyResponse) {
            // Call parent middleware to apply the secure headers
            $response = parent::handle($request, function() use ($response) {
                return $response;
            });
             // Update Permissions-Policy header to remove unrecognized features
             $response->headers->set('Permissions-Policy', 'camera=*');
            } else {
            // If the response is null or invalid, create a new Response instance
            $response = new SymfonyResponse();
            $response->setStatusCode(200); // Default status code
            $response->setContent('OK'); // Default content
            // Apply secure headers
            $response = parent::handle($request, function() use ($response) {
                return $response;
            });
             // Update Permissions-Policy header to remove unrecognized features
             $response->headers->set('Permissions-Policy', 'camera=()');
        }

        return $response;
    }
}
