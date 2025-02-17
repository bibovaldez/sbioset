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
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class FortifyServiceProvider extends ServiceProvider
{
    protected $failedLoginThreshold = 3; // 3 failed login attempts
    protected $failedLoginDecayMinutes = 5; // 5 minutes lockout
    protected $systemAttackThreshold = 10; // Increased from 3 to 10
    protected $blockDurationHours = 48; // Increased from 24 to 48 hours
    protected $passwordExpirationDays = 30; // New: Password expiration after 30 days

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        $this->configureRateLimiting();


        $this->schedulePasswordExpirationCheck();
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $email = Str::lower($request->input(Fortify::username()));
            $ip = $request->ip();
            $this->LogSinactivity($email, $ip);

            if ($this->isBlockedOrSuspicious($email, $ip)) {
                return $this->blockResponse();
            }

            $throttleKey = $this->generateThrottleKey($email, $ip);

            return Limit::perMinutes($this->failedLoginDecayMinutes, $this->failedLoginThreshold)
                ->by($throttleKey)
                ->response(function () use ($request, $email, $ip, $throttleKey) {
                    $this->handleFailedLogin($email, $ip, $throttleKey);
                    return $this->tooManyAttemptsResponse();
                });
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(3)->by($request->session()->get('login.id'));
        });
    }
    protected function schedulePasswordExpirationCheck(): void
    {
        // Schedule a daily job to check for expired passwords
        $schedule = $this->app->make(\Illuminate\Console\Scheduling\Schedule::class);
        $schedule->call(function () {
            User::where('password_changed_at', '<', now()->subDays($this->passwordExpirationDays))
                ->each(function ($user) {
                    $user->notify(new PasswordUpdateNotification());
                });
        })->daily();
    }

    protected function isBlockedOrSuspicious($email, $ip): bool
    {
        return BlockedEntity::isBlocked($email, $ip) || $this->isSuspiciousActivity($email, $ip);
    }


    protected function isSuspiciousActivity($email, $ip): bool
    {
        // Check for multiple failed attempts from different IPs for the same email
        $recentEmailAttempts = Cache::get("recent_attempts:{$email}", 0);

        // Check for multiple failed attempts from the same IP for different emails
        $recentIpAttempts = Cache::get("recent_ip_attempts:{$ip}", 0);

        // Check for rapid login attempts from the same IP
        $recentIpTimestamps = Cache::get("recent_ip_timestamps:{$ip}", []);
        $recentIpTimestamps[] = now();
        Cache::put("recent_ip_timestamps:{$ip}", $recentIpTimestamps, now()->addMinutes(5));

        $emailThreshold = $this->systemAttackThreshold;
        $ipThreshold = 5; //  threshold for IP-based attempts
        $rapidAttemptThreshold = 10; //  threshold for rapid attempts
        $rapidAttemptTimeFrame = 60; // Time frame in seconds

        // Check if any threshold is exceeded
        $isEmailSuspicious = $recentEmailAttempts > $emailThreshold;
        $isIpSuspicious = $recentIpAttempts > $ipThreshold;
        $isRapidAttemptSuspicious = count(array_filter($recentIpTimestamps, function ($timestamp) use ($rapidAttemptTimeFrame) {
            return $timestamp->diffInSeconds(now()) <= $rapidAttemptTimeFrame;
        })) > $rapidAttemptThreshold;

        return $isEmailSuspicious || $isIpSuspicious || $isRapidAttemptSuspicious;
    }

    protected function generateThrottleKey($email, $ip): string
    {
        return Str::transliterate(Str::lower($email . '|' . $ip));
    }

    protected function handleFailedLogin($email, $ip, $throttleKey): void
    {
        $this->incrementFailedLoginCounter($email, $ip, $throttleKey);
        $this->notifyAdminOfMaxLoginAttempts($email, $ip);
        $this->checkForSystemAttack($email, $ip);
    }

    protected function incrementFailedLoginCounter($email, $ip, $throttleKey): void
    {
        $failedLogins = Cache::get("{$throttleKey}:failed_logins", 0) + 1;
        Cache::put("{$throttleKey}:failed_logins", $failedLogins, now()->addMinutes($this->failedLoginDecayMinutes));

        // Track recent attempts for the email
        $recentAttempts = Cache::get("recent_attempts:{$email}", 0) + 1;
        Cache::put("recent_attempts:{$email}", $recentAttempts, now()->addHours(1));
    }
    // New: Notify admin of max login attempts
    protected function notifyAdminOfMaxLoginAttempts(string $email, string $ip): void
    {
        $adminEmail = config('mail.admin_email');
        Notification::route('mail', $adminEmail)
            ->notify(new MaxLoginAttemptsReached($email, $ip));
    }

    protected function checkForSystemAttack(string $email, string $ip): void
    {
        $recentAttempts = Cache::get("recent_attempts:{$email}", 0);

        if ($recentAttempts >= $this->systemAttackThreshold) {
            $this->notifyAdminOfSystemAttack();
            $this->blockEntity($email, $ip);
            $this->implementSecurityMeasures();
            $this->LogSuspiciousActivity($email, $ip);
        }
    }

    protected function blockEntity($email, $ip): void
    {
        BlockedEntity::block($email, $ip, $this->blockDurationHours);
    }

    protected function notifyAdminOfSystemAttack(): void
    {
        $adminEmail = config('mail.admin_email');
        Notification::route('mail', $adminEmail)
            ->notify(new SystemUnderAttack());
    }

    protected function implementSecurityMeasures(): void // Implement additional security measures
    {
        Auth::guard('web')->logout();
        Artisan::call('backup:run');
        $this->emailUsersToUpdatePassword();

        // log this activity
        Log::info('System under attack. Additional security measures implemented.');


        // Consider additional measures like temporarily disabling new user registrations

        // $mysqldumpPath = shell_exec('which mysqldump'); // Check if mysqldump is found
        // Log::info('mysqldump path: ' . $mysqldumpPath); // Log it to check
    }

    protected function emailUsersToUpdatePassword(): void // Email users to update password
    {
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                try {
                    $user->notify(new PasswordUpdateNotification());
                } catch (Exception $e) {
                    Log::error("Failed to send password update notification to {$user->email}: " . $e->getMessage());
                }
            }
        });
    }

    protected function blockResponse() //  Block response
    {
        $message = __('Access denied. Your Email address has been blocked.');
        abort(403, $message);
    }

    protected function tooManyAttemptsResponse()
    {
        return redirect()->route('login')
            ->with('error', 'Too many login attempts. Please try again later or contact support if you believe this is an error.');
    }

    protected function LogSuspiciousActivity($email, $ip): void // New: Log suspicious activity
    {
        Log::warning("Login activity detected for email: {$email} and IP: {$ip}");
    }

    protected function LogSinactivity($email, $ip): void // New: Log Login activity
    {
        Log::info("Login activity detected for email: {$email} and IP: {$ip}");
    }
}
