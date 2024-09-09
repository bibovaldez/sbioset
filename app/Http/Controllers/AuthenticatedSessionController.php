<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use App\Rules\Recaptcha;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Pipeline;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\CanonicalizeUsername;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\LoginViewResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Response\LoginResponse;
use Laravel\Jetstream\Jetstream;
use App\Actions\CheckAccountIsActive;
use App\Actions\CheckAccountHasTeam;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Notifications\ActivityNotification;
use Illuminate\Support\Facades\Notification;

class AuthenticatedSessionController extends Controller
{
    /**
     * The guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
     * @return void
     */
    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    /**
     * Show the login view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Fortify\Contracts\LoginViewResponse
     */
    public function create(Request $request): LoginViewResponse
    {
        return app(LoginViewResponse::class);
    }

    /**
     * Attempt to authenticate a new session.
     *
     * @param  \Laravel\Fortify\Http\Requests\LoginRequest  $request
     * @return mixed
     */
    public function store(LoginRequest $request)
    {

        // Validate reCAPTCHA and terms and conditions
        $validator = Validator::make($request->all(), [
            'recaptcha_token' => ['required', new Recaptcha],
            // terms and conditions
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        return $this->loginPipeline($request)->then(function ($request) {
            // if login sucessful, remove the cache key
            $email = $request->email;
            $ip = $request->ip();
            // Remove the cache key
            $throttleKey = Str::transliterate($email . '|' . $ip);
            Cache::forget("{$throttleKey}");
            Cache::forget("{$throttleKey}failed_logins");
            Cache::forget("{$throttleKey}:timer");
            Cache::forget("recent_attempts:{$email}");

            $this->Log_activity($email, $ip);
            return app(LoginResponse::class);
        });
    }

    /**
     * Get the authentication pipeline instance.
     *
     * @param  \Laravel\Fortify\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Pipeline\Pipeline
     */
    protected function loginPipeline(LoginRequest $request)
    {
        if (Fortify::$authenticateThroughCallback) {
            return (new Pipeline(app()))->send($request)->through(array_filter(
                call_user_func(Fortify::$authenticateThroughCallback, $request)
            ));
        }

        if (is_array(config('fortify.pipelines.login'))) {
            return (new Pipeline(app()))->send($request)->through(array_filter(
                config('fortify.pipelines.login')
            ));
        }
        // Check if the user is email verified
        $user = \App\Models\User::where('email', $request->email)->first();
        $isVerified = $user && $user->hasVerifiedEmail();
        // check if the user is in invited state
        $isInvited = $user && \App\Models\TeamInvitation::where('email', $user->email)->exists();
        return (new Pipeline(app()))->send($request)->through(array_filter([
            config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
            config('fortify.lowercase_usernames') ? CanonicalizeUsername::class : null,
            Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
            // Include CheckAccountHasTeam and CheckAccountIsActive only if the user is verified and not invited
            (!$isInvited && $isVerified) ? CheckAccountHasTeam::class : null,
            (!$isInvited && $isVerified) ? CheckAccountIsActive::class : null,
            AttemptToAuthenticate::class,
            PrepareAuthenticatedSession::class,
        ]));
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Fortify\Contracts\LogoutResponse
     */
    public function destroy(Request $request): LogoutResponse
    {
        $this->guard->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return app(LogoutResponse::class);
    }

    public function Log_activity($email, $ip)
    {
        Log::info("User with email: $email and IP: $ip has performed a successful login.");
        // notify admin about the login
        $subject = "User Login";
        $message = sprintf(
            "User Activity Report:\n\n- Email: %s\n- IP Address: %s\n- Status: Successful Login\n- Timestamp: %s",
            $email,
            $ip,
            now()->toDateTimeString()
        );
        Notification::route('mail', env('ADMIN_EMAIL'))
            ->notify(new ActivityNotification($subject, $message));
    }
}
