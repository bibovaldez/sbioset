<?php

namespace App\Http\Response;

use Closure;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Spatie\Honeypot\SpamResponder\SpamResponder;
use Illuminate\Support\Facades\Log;


class SpamResponse implements SpamResponder
{
    public function respond(Request $request, Closure $next)
    {
        $message = __('A spammer has been detected. You are not allowed to submit the form.');
        // log the spam attempt
        Log::info('Spam attempt detected', [
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
            'form_data' => $request->except('_token'),
        ]);
        // Invalidate the session
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        abort(403, $message);
        // terminate login attempt
        
    }
}
