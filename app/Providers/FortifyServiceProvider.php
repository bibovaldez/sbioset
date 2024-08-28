<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;
use App\Actions\Fortify\AttemptToAuthenticate;
use App\Actions\Fortify\RedirectIfTwoFactorAuthenticatable;
use App\Notifications\MaxLoginAttemptsReached;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SystemUnderAttack;
use Illuminate\Support\Facades\Cache;


class FortifyServiceProvider extends ServiceProvider
{
    protected $failedLoginThreshold = 2;
    protected $failedlogindecayMinutes = 5;
    protected $systemAttackThreshold = 2;
    /**
     * Register any application services.
     */
    public function register(): void {}

    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $email = Str::lower($request->input(Fortify::username()));
            $throttleKey = Str::transliterate($email . '|' . $request->ip());

            return Limit::perMinutes($this->failedlogindecayMinutes,$this->failedLoginThreshold)->by($throttleKey)
                ->response(function () use ($request, $email) {
                    $this->notifyAdminOfMaxLoginAttempts($email, $request->ip());
                    $this->checkForSystemAttack();
                    return redirect()->route('login')
                        ->with('error', 'Too many login attempts. Please try again in 5 minutes.');
                });
           

        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }


    protected function notifyAdminOfMaxLoginAttempts(string $email, string $ip): void
    {
        $adminEmail = config('mail.admin_email', 'admin@example.com');

        Notification::route('mail', $adminEmail)
            ->notify(new MaxLoginAttemptsReached($email, $ip));

        $this->incrementFailedLoginCounter();
    }

    protected function incrementFailedLoginCounter(): void
    {
        $failedLogins = Cache::get('failed_logins', 0) + 1;
        Cache::put('failed_logins', $failedLogins, now()->addMinutes(5));
    }

    protected function checkForSystemAttack(): void
    {
        $failedLogins = Cache::get('failed_logins', 0);

        if ($failedLogins >= $this->systemAttackThreshold) {
            $this->notifyAdminOfSystemAttack();
            $this->implementSecurityMeasures();
        }
    }

    protected function notifyAdminOfSystemAttack(): void
    {
        $adminEmail = config('mail.admin_email');

        Notification::route('mail', $adminEmail)
            ->notify(new SystemUnderAttack());
    }

    protected function implementSecurityMeasures(): void
    {
        // Implement your security measures here
        // For example, you could:
        // 1. Increase the rate limiting threshold
        // 2. Enable additional logging
        // 3. Temporarily disable login for all users
        // 4. Notify your security team


    }
}
