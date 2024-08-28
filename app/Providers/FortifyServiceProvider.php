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
use App\Models\User;
use App\Notifications\MaxLoginAttemptsReached;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SystemUnderAttack;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use \App\Notifications\PasswordUpdateNotification;
use Exception;



class FortifyServiceProvider extends ServiceProvider
{
    protected $failedLoginThreshold = 5; // 5 failed login attempts ako naglagay dito wag oa hahaah
    protected $failedlogindecayMinutes = 5; // 5 minutes before the failed login counter resets
    protected $systemAttackThreshold = 10; // when 10 user failed to login in 5 minutes, the system may under attack using brute force or other attack
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

            return Limit::perMinutes($this->failedlogindecayMinutes, $this->failedLoginThreshold)->by($throttleKey)
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
        $this->SystemShutdown();
        // logout all users
        Auth::guard('web')->logout();
        // run backup
        Artisan::call('backup:run');
        // email all user to update their password once system is live
        $this->emailUsersToUpdatePassword();
    }
    protected function emailUsersToUpdatePassword(): void
    {
        $users =  User::all()->pluck('email')->toArray();
        foreach ($users as $email) {
            try {
                Notification::route('mail', $email)
                    ->notify(new PasswordUpdateNotification());
            } catch (Exception $e) {
                Log::error('Failed to send password update notification to ' . $email);
            }
        }
    }
    protected function SystemShutdown(): void
    {
        // Log the initiation of security measures
        Log::info('Initiating security measures: placing the application in maintenance mode.');

        // Run the Artisan command to put the application in maintenance mode with a custom error view
        Artisan::call('down', ['--render' => 'errors::503']);

        // Log the successful execution of the command
        Log::info('Application successfully placed in maintenance mode.');
    }
}
