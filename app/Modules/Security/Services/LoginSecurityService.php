<?php

declare(strict_types=1);

namespace App\Modules\Security\Services;

use App\Models\User;
use App\Modules\Security\Models\LoginAttempt;
use App\Modules\Security\Models\SecuritySetting;

class LoginSecurityService
{
    protected SecuritySetting $settings;

    public function __construct()
    {
        $this->settings = SecuritySetting::getCurrent();
    }

    public function recordLoginAttempt(
        string $email,
        bool $success,
        string $ip,
        ?string $userAgent = null,
        ?string $failureReason = null,
        array $systemInfo = []
    ): LoginAttempt {
        return LoginAttempt::create([
            'user_id'        => User::where('email', $email)->value('id'),
            'identifier'     => $email,
            'method'         => 'password',
            'ip_address'     => $ip,
            'user_agent'     => $userAgent ? mb_substr($userAgent, 0, 500) : null,
            'is_success'     => $success,
            'failure_reason' => $failureReason,
            'created_at'     => now(),
        ]);
    }

    public function isAccountLocked(User $user): bool
    {
        if ($user->is_locked && $user->locked_until && $user->locked_until > now()) {
            return true;
        }

        if ($user->is_locked && (! $user->locked_until || $user->locked_until <= now())) {
            $user->update(['is_locked' => false, 'locked_until' => null]);
            return false;
        }

        return false;
    }

    public function recordFailedAttempt(User $user): void
    {
        $user->increment('failed_login_attempts');
        $user->update(['last_failed_attempt' => now()]);

        $user->refresh();

        if ($user->failed_login_attempts >= $this->settings->failed_attempts_threshold) {
            $user->update([
                'is_locked'    => true,
                'locked_until' => now()->addMinutes($this->settings->lockout_duration_minutes),
            ]);
        }
    }

    public function resetFailedAttempts(User $user): void
    {
        $user->update([
            'failed_login_attempts' => 0,
            'last_failed_attempt'   => null,
            'is_locked'             => false,
            'locked_until'          => null,
        ]);
    }

    public function shouldShowCaptcha(string $email): bool
    {
        if (! $this->settings->captcha_enabled) {
            return false;
        }

        return LoginAttempt::getFailedAttempts($email, 30) >= $this->settings->captcha_trigger_attempts;
    }

    public function shouldShowCaptchaByIp(string $ip): bool
    {
        if (! $this->settings->captcha_enabled) {
            return false;
        }

        return LoginAttempt::getFailedAttemptsByIp($ip, 30) >= $this->settings->captcha_trigger_attempts;
    }

    public function getRecentFailedAttempts(string $email, int $minutes = 30): int
    {
        return LoginAttempt::getFailedAttempts($email, $minutes);
    }

    public function getRecentFailedAttemptsByIp(string $ip, int $minutes = 30): int
    {
        return LoginAttempt::getFailedAttemptsByIp($ip, $minutes);
    }

    public function cleanupOldAttempts(int $daysToKeep = 90): int
    {
        return LoginAttempt::where('attempted_at', '<', now()->subDays($daysToKeep))
            ->where('is_success', false)
            ->delete();
    }
}
