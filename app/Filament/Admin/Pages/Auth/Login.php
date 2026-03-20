<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages\Auth;

use App\Mail\LoginOtpMail;
use App\Models\LoginActivity;
use App\Models\User;
use App\Models\UserSession;
use App\Services\SmsService;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected string $view = 'filament.admin.pages.auth.login';

    // Per-account lockout: 5 failed attempts → locked for 15 minutes
    private const MAX_FAILED_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES     = 15;

    // 2FA brute-force: 3 wrong codes → session invalidated
    private const MAX_OTP_ATTEMPTS   = 3;
    private const OTP_EXPIRY_MINUTES  = 10;

    // ── Multi-step auth state ──────────────────────────────────────────
    public string $step            = 'credentials'; // 'credentials' | 'two_factor'
    public string $pendingToken    = '';
    public string $twoFactorCode   = '';
    public string $twoFactorError  = '';
    public string $twoFactorMethod = ''; // 'email_otp' | 'mobile_otp' | 'static_code'
    public string $twoFactorHint   = ''; // masked destination shown to user

    public function getHeading(): string
    {
        return '';
    }

    public function hasLogo(): bool
    {
        return false;
    }

    // ──────────────────────────────────────────────────────────────────
    // FORM SCHEMA — simplified: user_type + email + password
    // ──────────────────────────────────────────────────────────────────

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Select::make('user_type')
                    ->label('Sign in as')
                    ->options([
                        'admin'   => 'Administrator',
                        'teacher' => 'Teacher',
                        'student' => 'Student',
                    ])
                    ->default('admin')
                    ->required()
                    ->selectablePlaceholder(false),

                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->prefixIcon('heroicon-o-envelope')
                    ->placeholder('you@school.edu')
                    ->required(),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->prefixIcon('heroicon-o-lock-closed')
                    ->placeholder('Your password')
                    ->required(),
            ]);
    }

    // ──────────────────────────────────────────────────────────────────
    // AUTHENTICATE — email + password → check tenant 2FA config
    // ──────────────────────────────────────────────────────────────────

    public function authenticate(): ?\Filament\Auth\Http\Responses\Contracts\LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->logAttempt(method: 'password', success: false, reason: 'rate_limited');
            Notification::make()
                ->title("Too many requests. Please wait {$exception->secondsUntilAvailable} seconds.")
                ->danger()
                ->send();
            return null;
        }

        return $this->authenticatePassword();
    }

    private function authenticatePassword(): ?\Filament\Auth\Http\Responses\Contracts\LoginResponse
    {
        $data     = $this->form->getState();
        $email    = (string) ($data['email'] ?? '');
        $password = (string) ($data['password'] ?? '');

        $this->checkAccountLockout($email, 'data.email');

        // ── Manually verify credentials WITHOUT touching the session ──
        // (Using attempt()+logout() destroys the session and invalidates
        //  Livewire's CSRF token, causing "page has expired" on the 2FA step.)
        /** @var User|null $user */
        $user = User::query()->where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            $this->recordFailedAttempt($email);
            $this->logAttempt(method: 'password', identifier: $email, success: false, reason: 'invalid_credentials');

            $remaining = $this->remainingAttempts($email);
            $message   = $remaining > 0
                ? __('filament-panels::auth/pages/login.messages.failed') . " ({$remaining} attempts remaining)"
                : 'Account locked for ' . self::LOCKOUT_MINUTES . ' minutes due to too many failed attempts.';

            throw ValidationException::withMessages(['data.email' => $message]);
        }

        if (! $user->is_active) {
            $this->logAttempt(method: 'password', user: $user, identifier: $email, success: false, reason: 'inactive');
            throw ValidationException::withMessages(['data.email' => 'Your account has been disabled. Contact your administrator.']);
        }

        $this->clearFailedAttempts($email);

        // ── Check tenant 2FA config ──────────────────────────────────
        $twoFa = DB::connection('central')
            ->table('tenant_2fa_configs')
            ->where('tenant_id', tenant('id'))
            ->first();

        if ($twoFa && $twoFa->enabled && $twoFa->method) {
            // Store pending state — session is NOT touched here
            $this->pendingToken    = Str::random(40);
            $this->twoFactorCode   = '';
            $this->twoFactorError  = '';
            $this->twoFactorMethod = $twoFa->method;

            Cache::put(
                '2fa:pending:' . tenant('id') . ':' . $this->pendingToken,
                $user->id,
                now()->addMinutes(self::OTP_EXPIRY_MINUTES)
            );

            $this->initializeTwoFactor($user, $twoFa);
            $this->step = 'two_factor';

            $this->logAttempt(method: 'password', user: $user, identifier: $email, success: false, reason: '2fa_required');
            return null;
        }

        // ── No 2FA — create session and log in now ────────────────────
        Filament::auth()->login($user, remember: false);
        $this->afterSuccessfulLogin($user, method: 'password', identifier: $email);
        session()->regenerate();

        return app(\Filament\Auth\Http\Responses\Contracts\LoginResponse::class);
    }

    // ──────────────────────────────────────────────────────────────────
    // 2FA — INITIALIZE (send OTP or prepare static code)
    // ──────────────────────────────────────────────────────────────────

    private function initializeTwoFactor(User $user, object $twoFa): void
    {
        match ($twoFa->method) {
            'email_otp'   => $this->initEmailOtp($user, $twoFa),
            'mobile_otp'  => $this->initMobileOtp($user, $twoFa),
            'static_code' => $this->initStaticCode(),
            default       => null,
        };
    }

    private function initEmailOtp(User $user, object $twoFa): void
    {
        $dest = ($twoFa->email_target === 'fixed' && $twoFa->fixed_email)
            ? $twoFa->fixed_email
            : $user->email;

        try {
            $this->sendEmailOtp($dest);
        } catch (\Throwable) {
            throw ValidationException::withMessages([
                'data.email' => 'Could not send verification email. Please try again.',
            ]);
        }

        $this->twoFactorHint = $this->maskEmail($dest);

        Notification::make()
            ->title('Verification code sent')
            ->body("A 6-digit code was sent to {$this->twoFactorHint}")
            ->success()
            ->send();
    }

    private function initMobileOtp(User $user, object $twoFa): void
    {
        $sms  = app(SmsService::class);
        $dest = ($twoFa->mobile_target === 'fixed' && $twoFa->fixed_mobile)
            ? $twoFa->fixed_mobile
            : ($user->phone ?? '');

        if ($dest && $sms->isConfigured()) {
            $this->sendSmsOtp($dest, $sms);
        }

        $this->twoFactorHint = $dest ? $this->maskPhone($dest) : 'your registered number';

        Notification::make()
            ->title('Verification code sent')
            ->body("A 6-digit code was sent to {$this->twoFactorHint}")
            ->success()
            ->send();
    }

    private function initStaticCode(): void
    {
        $this->twoFactorHint = '';

        Notification::make()
            ->title('Security verification required')
            ->body('Enter the static security code provided by your administrator.')
            ->info()
            ->send();
    }

    // ──────────────────────────────────────────────────────────────────
    // 2FA — VERIFY CODE
    // ──────────────────────────────────────────────────────────────────

    public function verifyTwoFactor(): ?\Filament\Auth\Http\Responses\Contracts\LoginResponse
    {
        $code = trim($this->twoFactorCode);

        if (empty($code)) {
            $this->twoFactorError = 'Please enter the verification code.';
            return null;
        }

        $cacheKey = '2fa:pending:' . tenant('id') . ':' . $this->pendingToken;
        $userId   = Cache::get($cacheKey);

        if (! $userId) {
            $this->twoFactorError = 'Session expired. Please sign in again.';
            $this->step           = 'credentials';
            $this->pendingToken   = '';
            return null;
        }

        /** @var User|null $user */
        $user = User::find($userId);

        if (! $user) {
            $this->step = 'credentials';
            return null;
        }

        $twoFa = DB::connection('central')
            ->table('tenant_2fa_configs')
            ->where('tenant_id', tenant('id'))
            ->first();

        if (! $twoFa) {
            $this->step = 'credentials';
            return null;
        }

        // Brute-force protection
        $attemptsKey = '2fa:attempts:' . tenant('id') . ':' . $this->pendingToken;
        $attempts    = (int) Cache::get($attemptsKey, 0);

        if ($attempts >= self::MAX_OTP_ATTEMPTS) {
            Cache::forget($cacheKey);
            Cache::forget($attemptsKey);
            $this->twoFactorError = 'Too many incorrect attempts. Please sign in again.';
            $this->step           = 'credentials';
            $this->pendingToken   = '';
            $this->logAttempt(method: 'password+2fa', identifier: $user->email, success: false, reason: '2fa_max_attempts');
            return null;
        }

        $verified = match ($twoFa->method) {
            'email_otp', 'mobile_otp' => $this->verifyOtpCode($code, $user, $twoFa),
            'static_code'             => $this->verifyStaticCode($code, $twoFa),
            default                   => false,
        };

        if (! $verified) {
            Cache::put($attemptsKey, $attempts + 1, now()->addMinutes(self::OTP_EXPIRY_MINUTES));
            $left                 = self::MAX_OTP_ATTEMPTS - ($attempts + 1);
            $this->twoFactorError = $this->twoFactorError ?: "Invalid code. {$left} attempt(s) remaining.";
            $this->logAttempt(method: 'password+2fa', identifier: $user->email, success: false, reason: 'invalid_2fa_code');
            return null;
        }

        // ── Verified — clean up and log in ───────────────────────────
        Cache::forget($cacheKey);
        Cache::forget($attemptsKey);

        if (in_array($twoFa->method, ['email_otp', 'mobile_otp'], true)) {
            $dest   = $this->getOtpDest($user, $twoFa);
            $otpKey = $twoFa->method === 'email_otp'
                ? $this->emailOtpCacheKey($dest)
                : $this->phoneOtpCacheKey($dest);
            Cache::forget($otpKey);
            Cache::forget('auth:otp_tries:' . $otpKey);
        }

        $this->clearFailedAttempts($user->email);
        Filament::auth()->login($user, remember: false);
        $this->afterSuccessfulLogin($user, method: 'password+2fa', identifier: $user->email);
        session()->regenerate();

        return app(\Filament\Auth\Http\Responses\Contracts\LoginResponse::class);
    }

    private function verifyOtpCode(string $code, User $user, object $twoFa): bool
    {
        if (strlen($code) !== 6 || ! ctype_digit($code)) {
            $this->twoFactorError = 'Please enter the complete 6-digit code.';
            return false;
        }

        $dest      = $this->getOtpDest($user, $twoFa);
        $otpKey    = $twoFa->method === 'email_otp'
            ? $this->emailOtpCacheKey($dest)
            : $this->phoneOtpCacheKey($dest);
        $hashedOtp = Cache::get($otpKey);

        if (! is_string($hashedOtp)) {
            $this->twoFactorError = 'Code expired. Click "Resend code" to get a new one.';
            return false;
        }

        return Hash::check($code, $hashedOtp);
    }

    private function verifyStaticCode(string $code, object $twoFa): bool
    {
        return $twoFa->static_code !== null && $code === $twoFa->static_code;
    }

    private function getOtpDest(User $user, object $twoFa): string
    {
        return match ($twoFa->method) {
            'email_otp'  => ($twoFa->email_target === 'fixed' && $twoFa->fixed_email)
                ? $twoFa->fixed_email
                : $user->email,
            'mobile_otp' => ($twoFa->mobile_target === 'fixed' && $twoFa->fixed_mobile)
                ? $twoFa->fixed_mobile
                : ($user->phone ?? ''),
            default      => '',
        };
    }

    // ──────────────────────────────────────────────────────────────────
    // 2FA — RESEND (OTP methods only)
    // ──────────────────────────────────────────────────────────────────

    public function resendTwoFactor(): void
    {
        if ($this->twoFactorMethod === 'static_code') {
            return; // static code cannot be resent
        }

        $cacheKey = '2fa:pending:' . tenant('id') . ':' . $this->pendingToken;
        $userId   = Cache::get($cacheKey);

        if (! $userId) {
            $this->twoFactorError = 'Session expired. Please sign in again.';
            $this->step           = 'credentials';
            $this->pendingToken   = '';
            return;
        }

        $user  = User::find($userId);
        $twoFa = DB::connection('central')
            ->table('tenant_2fa_configs')
            ->where('tenant_id', tenant('id'))
            ->first();

        if (! $user || ! $twoFa) {
            $this->step = 'credentials';
            return;
        }

        try {
            $this->initializeTwoFactor($user, $twoFa);
            $this->twoFactorCode  = '';
            $this->twoFactorError = '';
        } catch (\Throwable) {
            $this->twoFactorError = 'Failed to resend code. Please try again.';
        }
    }

    // ──────────────────────────────────────────────────────────────────
    // 2FA — BACK TO CREDENTIALS
    // ──────────────────────────────────────────────────────────────────

    public function backToCredentials(): void
    {
        if ($this->pendingToken) {
            Cache::forget('2fa:pending:' . tenant('id') . ':' . $this->pendingToken);
            Cache::forget('2fa:attempts:' . tenant('id') . ':' . $this->pendingToken);
        }

        $this->step            = 'credentials';
        $this->pendingToken    = '';
        $this->twoFactorCode   = '';
        $this->twoFactorError  = '';
        $this->twoFactorMethod = '';
        $this->twoFactorHint   = '';
    }

    // ──────────────────────────────────────────────────────────────────
    // OTP SENDERS
    // ──────────────────────────────────────────────────────────────────

    private function sendEmailOtp(string $email): void
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw ValidationException::withMessages(['data.email' => 'Invalid email address for 2FA.']);
        }

        $otp        = (string) random_int(100000, 999999);
        $cacheKey   = $this->emailOtpCacheKey($email);
        $schoolName = tenant('name') ?? 'Simption';

        Cache::put($cacheKey, Hash::make($otp), now()->addMinutes(self::OTP_EXPIRY_MINUTES));
        Cache::forget('auth:otp_tries:' . $cacheKey);

        Mail::to($email)->send(new LoginOtpMail(
            otp:           $otp,
            schoolName:    $schoolName,
            expiryMinutes: self::OTP_EXPIRY_MINUTES,
        ));
    }

    private function sendSmsOtp(string $phone, SmsService $sms): void
    {
        $otp        = (string) random_int(100000, 999999);
        $cacheKey   = $this->phoneOtpCacheKey($phone);
        $schoolName = tenant('name') ?? 'Simption';

        Cache::put($cacheKey, Hash::make($otp), now()->addMinutes(self::OTP_EXPIRY_MINUTES));
        Cache::forget('auth:otp_tries:' . $cacheKey);

        $sms->sendOtp($phone, $otp, $schoolName);
    }

    private function emailOtpCacheKey(string $email): string
    {
        return 'auth:otp_email:' . tenant('id') . ':' . Str::lower($email);
    }

    private function phoneOtpCacheKey(string $phone): string
    {
        return 'auth:otp_phone:' . tenant('id') . ':' . preg_replace('/\s+/', '', $phone);
    }

    // ──────────────────────────────────────────────────────────────────
    // MASKING HELPERS
    // ──────────────────────────────────────────────────────────────────

    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return '***@***.***';
        }
        $local  = $parts[0];
        $masked = mb_substr($local, 0, 1) . str_repeat('*', max(2, mb_strlen($local) - 1));
        return $masked . '@' . $parts[1];
    }

    private function maskPhone(string $phone): string
    {
        $clean = preg_replace('/\D/', '', $phone);
        $len   = strlen($clean);
        if ($len < 4) {
            return '****';
        }
        return str_repeat('*', $len - 4) . substr($clean, -4);
    }

    // ──────────────────────────────────────────────────────────────────
    // PER-ACCOUNT LOCKOUT SYSTEM
    // ──────────────────────────────────────────────────────────────────

    private function checkAccountLockout(string $identifier, string $fieldKey): void
    {
        if (Cache::has($this->lockoutKey($identifier))) {
            $this->logAttempt(method: 'lockout', identifier: $identifier, success: false, reason: 'account_locked');
            throw ValidationException::withMessages([
                $fieldKey => 'Account temporarily locked for ' . self::LOCKOUT_MINUTES . ' minutes due to too many failed attempts.',
            ]);
        }
    }

    private function recordFailedAttempt(string $identifier): void
    {
        $attemptsKey = $this->failedAttemptsKey($identifier);
        $attempts    = (int) Cache::get($attemptsKey, 0) + 1;

        Cache::put($attemptsKey, $attempts, now()->addMinutes(self::LOCKOUT_MINUTES));

        if ($attempts >= self::MAX_FAILED_ATTEMPTS) {
            Cache::put($this->lockoutKey($identifier), true, now()->addMinutes(self::LOCKOUT_MINUTES));
            Cache::forget($attemptsKey);
        }
    }

    private function remainingAttempts(string $identifier): int
    {
        $attempts = (int) Cache::get($this->failedAttemptsKey($identifier), 0);
        return max(0, self::MAX_FAILED_ATTEMPTS - $attempts);
    }

    private function clearFailedAttempts(string $identifier): void
    {
        Cache::forget($this->failedAttemptsKey($identifier));
        Cache::forget($this->lockoutKey($identifier));
    }

    private function failedAttemptsKey(string $identifier): string
    {
        return 'auth:failed:' . tenant('id') . ':' . Str::lower(trim($identifier));
    }

    private function lockoutKey(string $identifier): string
    {
        return 'auth:locked:' . tenant('id') . ':' . Str::lower(trim($identifier));
    }

    // ──────────────────────────────────────────────────────────────────
    // POST-LOGIN ACTIONS
    // ──────────────────────────────────────────────────────────────────

    private function afterSuccessfulLogin(User $user, string $method, string $identifier): void
    {
        $user->forceFill([
            'last_login_at' => Carbon::now(),
            'last_login_ip' => request()->ip(),
        ])->save();

        $this->logAttempt(method: $method, user: $user, identifier: $identifier, success: true);

        UserSession::create([
            'user_id'          => $user->id,
            'session_id'       => session()->getId(),
            'method'           => $method,
            'ip_address'       => request()->ip(),
            'user_agent'       => request()->userAgent(),
            'logged_in_at'     => Carbon::now(),
            'last_activity_at' => Carbon::now(),
        ]);

        activity()
            ->causedBy($user)
            ->withProperties(['method' => $method, 'ip' => request()->ip()])
            ->log('login');
    }

    private function logAttempt(
        string  $method,
        ?User   $user       = null,
        ?string $identifier = null,
        bool    $success    = false,
        ?string $reason     = null,
    ): void {
        LoginActivity::create([
            'user_id'        => $user?->id,
            'identifier'     => $identifier,
            'method'         => $method,
            'is_success'     => $success,
            'failure_reason' => $reason,
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
            'created_at'     => Carbon::now(),
        ]);
    }
}
