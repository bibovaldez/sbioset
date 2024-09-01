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
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\BlockedEntity;
use App\Notifications\MaxLoginAttemptsReached;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SystemUnderAttack;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use App\Notifications\PasswordUpdateNotification;
use Exception;

class FortifyServiceProvider extends ServiceProvider
{
    protected $failedLoginThreshold = 3; // 3 failed login attempts
    protected $failedlogindecayMinutes = 5; // 5 minutes
    protected $systemAttackThreshold = 3; // 3 failed login attempts para tuluyan ng ma block yung account
    protected $blockDurationHours = 24; // 24 hours syang blocl sa system

    public function register(): void {}

    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $email = Str::lower($request->input(Fortify::username()));
            $ip = $request->ip();

            if (BlockedEntity::isBlocked($email, $ip)) {
                return $this->blockResponse();
            }

            $throttleKey = Str::transliterate($email . '|' . $ip);

            return Limit::perMinutes($this->failedlogindecayMinutes, $this->failedLoginThreshold)->by($throttleKey)
                ->response(function () use ($request, $email, $ip, $throttleKey) {
                    $this->notifyAdminOfMaxLoginAttempts($email, $ip, $throttleKey);
                    $this->checkForSystemAttack($email, $ip, $throttleKey);
                    return redirect()->route('login')
                        ->with('error', 'Too many login attempts. Your account has been temporarily blocked.');
                });
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }

    protected function blockResponse()
    {
        return redirect()->route('login')
            ->with('error', 'Access denied. Your account or IP is temporarily blocked.');
    }

    protected function blockEntity($email, $ip): void
    {
        BlockedEntity::block($email, $ip, $this->blockDurationHours);
    }

    protected function notifyAdminOfMaxLoginAttempts(string $email, string $ip, string $throttleKey): void
    {
        $adminEmail = config('mail.admin_email');

        Notification::route('mail', $adminEmail)
            ->notify(new MaxLoginAttemptsReached($email, $ip));

        $this->incrementFailedLoginCounter($throttleKey);
    }

    protected function incrementFailedLoginCounter($throttleKey): void
    {
        $failedLogins = Cache::get("{$throttleKey}failed_logins", 0) + 1;
        Cache::put("{$throttleKey}failed_logins", $failedLogins, now()->addMinutes(5));
    }

    protected function checkForSystemAttack(string $email, string $ip, $throttleKey): void
    {
        $failedLogins = Cache::get("{$throttleKey}failed_logins", 0);

        if ($failedLogins >= $this->systemAttackThreshold) {
            $this->notifyAdminOfSystemAttack();
            $this->blockEntity($email, $ip);
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
        Auth::guard('web')->logout();
        Artisan::call('backup:run');
        $this->emailUsersToUpdatePassword();
    }
    protected function emailUsersToUpdatePassword(): void
    {
        $users = User::all()->pluck('email')->toArray();
        foreach ($users as $email) {
            try {
                Notification::route('mail', $email)
                    ->notify(new PasswordUpdateNotification());
            } catch (Exception $e) {
                Log::error('Failed to send password update notification to ' . $email);
            }
        }
    }
}
