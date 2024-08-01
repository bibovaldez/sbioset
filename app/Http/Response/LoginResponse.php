<?php

namespace App\Http\Response;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        if ($request->wantsJson()) {
            return response()->json(['two_factor' => false]);
        }
        // Check if the authenticated user is an admin
        if (Auth::user() && Auth::user()->isAdmin()) {
            return redirect()->intended(config('fortify.admin_home'));
        }
        // For regular users
        return redirect()->intended(Fortify::redirects('login', '/dashboard'));
    }
}
