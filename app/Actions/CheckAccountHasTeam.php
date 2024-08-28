<?php

namespace App\Actions;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\LoginRateLimiter;
use Laravel\Fortify\Fortify;

class CheckAccountHasTeam
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  callable  $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user && !$user->current_team_id) {
            throw ValidationException::withMessages([
                Fortify::username() => __('Your account is either inactive or you are not part of any team.'),
            ]);
        }

        return $next($request);
    }
}
