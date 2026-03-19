<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages\Auth;

use App\Mail\LoginOtpMail;
use App\Models\LoginActivity;
use App\Models\User;
use App\Models\UserSession;
use App\Services\SmsService;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions as SchemaActions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected string $view = 'filament.admin.pages.auth.login';

    // Per-account lockout: 5 failed attempts → locked for 15 minutes
    private const MAX_FAILED_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES     = 15;

    // OTP brute-force protection: 3 wrong OTPs → OTP invalidated
    private const MAX_OTP_ATTEMPTS = 3;

    // OTP validity window
    private const OTP_EXPIRY_MINUTES = 10;

    public function getHeading(): string
    {
        return '';
    }

    /** Hide Filament's brand header — we render our own school identity zone */
    public function hasLogo(): bool
    {
        return false;
    }

    // ──────────────────────────────────────────────────────────────────
    // FORM SCHEMA
    // ──────────────────────────────────────────────────────────────────

    public function form(Schema $schema): Schema
    {
        // Load enabled methods from central DB
        $methods = DB::connection('central')
            ->table('tenant_login_methods')
            ->where('tenant_id', tenant('id'))
            ->first();

        // Fallback to tenant attributes (Stancl v3 direct access)
        $tenantMethods  = tenant('login_methods') ?? [];
        $isOtpEmail     = $methods->method_otp_email  ?? in_array('otp',      $tenantMethods);
        $isPassword     = $methods->method_password   ?? in_array('password', $tenantMethods) ?: true;
        $isLoginCode    = $methods->method_login_code ?? in_array('code',     $tenantMethods);
        $isOtpMobile    = $methods->method_otp_mobile ?? in_array('mobile',   $tenantMethods);
        $smsConfigured  = app(SmsService::class)->isConfigured();

        $tabs = [];

        if ($isOtpEmail) {
            $tabs[] = Tab::make('OTP Email')
                ->id('otp_email')
                ->icon('heroicon-o-envelope')
                ->schema([
                    Section::make()->schema([
                        TextInput::make('otp_email')
                            ->label('Email Address')
                            ->email()
                            ->prefixIcon('heroicon-o-envelope')
                            ->placeholder('you@school.edu'),
                        SchemaActions::make([
                            Action::make('send_email_otp')
                                ->label('Send OTP to Email')
                                ->icon('heroicon-o-paper-airplane')
                                ->action('doSendEmailOtp')
                                ->color('gray')
                                ->extraAttributes(['class' => 'smp-send-otp-btn']),
                        ])->extraAttributes(['class' => 'smp-send-otp-wrap']),
                        TextInput::make('otp')
                            ->label('One-Time Password')
                            ->helperText('Enter the 6-digit code sent to your email.')
                            ->numeric()
                            ->minLength(6)
                            ->maxLength(6)
                            ->prefixIcon('heroicon-o-shield-check')
                            ->placeholder('_ _ _ _ _ _'),
                    ]),
                ]);
        }

        if ($isPassword) {
            $tabs[] = Tab::make('Password')
                ->id('password')
                ->icon('heroicon-o-lock-closed')
                ->schema([
                    Section::make()->schema([
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->prefixIcon('heroicon-o-envelope')
                            ->placeholder('you@school.edu'),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->prefixIcon('heroicon-o-lock-closed')
                            ->placeholder('Your password'),
                    ]),
                ]);
        }

        if ($isLoginCode) {
            $tabs[] = Tab::make('Code')
                ->id('login_code')
                ->icon('heroicon-o-key')
                ->schema([
                    Section::make()->schema([
                        TextInput::make('login_code_input')
                            ->label('Login Code')
                            ->autocomplete(false)
                            ->prefixIcon('heroicon-o-key')
                            ->placeholder('e.g. ABC123'),
                    ]),
                ]);
        }

        if ($isOtpMobile) {
            $tabs[] = Tab::make('Mobile')
                ->id('otp_mobile')
                ->icon('heroicon-o-device-phone-mobile')
                ->schema([
                    Section::make()->schema([
                        TextInput::make('otp_phone')
                            ->label('Mobile Number')
                            ->tel()
                            ->prefixIcon('heroicon-o-device-phone-mobile')
                            ->placeholder('+91 98765 43210'),
                        SchemaActions::make([
                            Action::make('send_phone_otp')
                                ->label($smsConfigured ? 'Send OTP via SMS' : 'SMS Not Configured')
                                ->icon('heroicon-o-device-phone-mobile')
                                ->action('doSendPhoneOtp')
                                ->color('gray')
                                ->disabled(! $smsConfigured)
                                ->extraAttributes(['class' => 'smp-send-otp-btn']),
                        ])->extraAttributes(['class' => 'smp-send-otp-wrap']),
                        TextInput::make('otp_mobile_input')
                            ->label('One-Time Password')
                            ->numeric()
                            ->minLength(6)
                            ->maxLength(6)
                            ->prefixIcon('heroicon-o-shield-check')
                            ->placeholder('_ _ _ _ _ _')
                            ->helperText('Enter the 6-digit code sent via SMS.'),
                    ]),
                ]);
        }

        // Determine default tab — prefer password when available
        $defaultTab = 'password';
        if ($isPassword)       { $defaultTab = 'password'; }
        elseif ($isOtpEmail)   { $defaultTab = 'otp_email'; }
        elseif ($isLoginCode)  { $defaultTab = 'login_code'; }
        elseif ($isOtpMobile)  { $defaultTab = 'otp_mobile'; }

        return $schema
            ->statePath('data')
            ->components([
                Hidden::make('active_tab')
                    ->id('hidden_active_tab')
                    ->default($defaultTab),

                // Silently sync active tab to the hidden field
                Placeholder::make('tab_sync')
                    ->hiddenLabel()
                    ->content(new HtmlString('
                        <div x-init="$watch(\'tab\', value => {
                            let el = document.getElementById(\'hidden_active_tab\');
                            if (el) {
                                el.value = value;
                                el.dispatchEvent(new Event(\'input\', { bubbles: true }));
                            }
                        })"></div>
                    ')),

                Tabs::make('auth_tabs')
                    ->persistTabInQueryString()
                    ->tabs($tabs),
            ]);
    }

    // ──────────────────────────────────────────────────────────────────
    // MAIN AUTHENTICATE DISPATCHER
    // ──────────────────────────────────────────────────────────────────

    public function authenticate(): ?\Filament\Auth\Http\Responses\Contracts\LoginResponse
    {
        $data      = $this->form->getState();
        $activeTab = $data['active_tab'] ?? 'password';

        // Global rate limit (shared across all methods — prevents flooding)
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->logAttempt(
                method:  $activeTab,
                success: false,
                reason:  'rate_limited',
            );

            Notification::make()
                ->title("Too many requests. Please wait {$exception->secondsUntilAvailable} seconds.")
                ->danger()
                ->send();

            return null;
        }

        return match ($activeTab) {
            'otp_email'  => $this->authenticateOtpEmail(),
            'password'   => $this->authenticatePassword(),
            'login_code' => $this->authenticateLoginCode(),
            'otp_mobile' => $this->authenticateMobileOtp(),
            default      => $this->authenticatePassword(),
        };
    }

    // ──────────────────────────────────────────────────────────────────
    // METHOD 1 — PASSWORD
    // ──────────────────────────────────────────────────────────────────

    private function authenticatePassword(): ?\Filament\Auth\Http\Responses\Contracts\LoginResponse
    {
        $data  = $this->form->getState();
        $email = (string) ($data['email'] ?? '');

        // Per-account lockout check
        $this->checkAccountLockout($email, 'data.email');

        $credentials = [
            'email'    => $email,
            'password' => $data['password'] ?? null,
        ];

        if (! Filament::auth()->attempt($credentials, false)) {
            $this->recordFailedAttempt($email);
            $this->logAttempt(method: 'password', identifier: $email, success: false, reason: 'invalid_credentials');

            $remaining = $this->remainingAttempts($email);
            $message   = $remaining > 0
                ? __('filament-panels::auth/pages/login.messages.failed') . " ({$remaining} attempts remaining)"
                : 'Account locked for ' . self::LOCKOUT_MINUTES . ' minutes due to too many failed attempts.';

            throw ValidationException::withMessages(['data.email' => $message]);
        }

        /** @var User $user */
        $user = Filament::auth()->user();

        if (! $user->is_active) {
            Filament::auth()->logout();
            $this->logAttempt(method: 'password', user: $user, identifier: $email, success: false, reason: 'inactive');
            throw ValidationException::withMessages(['data.email' => 'Your account has been disabled. Contact your administrator.']);
        }

        $this->clearFailedAttempts($email);
        $this->afterSuccessfulLogin($user, method: 'password', identifier: $email);
        session()->regenerate();

        return app(\Filament\Auth\Http\Responses\Contracts\LoginResponse::class);
    }

    // ──────────────────────────────────────────────────────────────────
    // METHOD 2 — OTP EMAIL
    // ──────────────────────────────────────────────────────────────────

    private function authenticateOtpEmail(): ?\Filament\Auth\Http\Responses\Contracts\LoginResponse
    {
        $data  = $this->form->getState();
        $email = (string) ($data['otp_email'] ?? '');
        $otp   = (string) ($data['otp'] ?? '');

        // ── Step 1: No OTP entered → send OTP ──
        if ($otp === '') {
            $this->checkAccountLockout($email, 'data.otp_email');
            $this->sendEmailOtp($email);

            Notification::make()
                ->title('OTP sent! Check your email.')
                ->body('Enter the 6-digit code within ' . self::OTP_EXPIRY_MINUTES . ' minutes.')
                ->success()
                ->send();

            $this->logAttempt(method: 'otp_email', identifier: $email, success: false, reason: 'otp_sent');
            return null;
        }

        // ── Step 2: OTP entered → verify ──
        $this->checkAccountLockout($email, 'data.otp_email');

        $cacheKey  = $this->emailOtpCacheKey($email);
        $hashedOtp = Cache::get($cacheKey);

        if (! is_string($hashedOtp)) {
            $this->logAttempt(method: 'otp_email', identifier: $email, success: false, reason: 'otp_expired');
            throw ValidationException::withMessages(['data.otp_email' => 'OTP has expired. Please request a new one.']);
        }

        // Brute-force protection: max 3 wrong OTP attempts
        $attemptsKey = 'auth:otp_tries:' . $cacheKey;
        $otpAttempts = (int) Cache::get($attemptsKey, 0);

        if ($otpAttempts >= self::MAX_OTP_ATTEMPTS) {
            Cache::forget($cacheKey);
            Cache::forget($attemptsKey);
            $this->logAttempt(method: 'otp_email', identifier: $email, success: false, reason: 'otp_max_attempts');
            throw ValidationException::withMessages(['data.otp' => 'Too many incorrect attempts. Request a new OTP.']);
        }

        if (! Hash::check($otp, $hashedOtp)) {
            Cache::put($attemptsKey, $otpAttempts + 1, now()->addMinutes(self::OTP_EXPIRY_MINUTES));
            $this->recordFailedAttempt($email);
            $this->logAttempt(method: 'otp_email', identifier: $email, success: false, reason: 'invalid_otp');

            $left = self::MAX_OTP_ATTEMPTS - ($otpAttempts + 1);
            throw ValidationException::withMessages([
                'data.otp' => "Invalid OTP. {$left} attempt(s) remaining before OTP is invalidated.",
            ]);
        }

        // OTP verified — clean up
        Cache::forget($cacheKey);
        Cache::forget($attemptsKey);

        /** @var User|null $user */
        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            $this->logAttempt(method: 'otp_email', identifier: $email, success: false, reason: 'user_not_found');
            throw ValidationException::withMessages(['data.otp_email' => 'No account found for this email.']);
        }

        if (! $user->is_active) {
            $this->logAttempt(method: 'otp_email', user: $user, identifier: $email, success: false, reason: 'inactive');
            throw ValidationException::withMessages(['data.otp_email' => 'Your account has been disabled. Contact your administrator.']);
        }

        $this->clearFailedAttempts($email);
        Filament::auth()->login($user, remember: false);
        $this->afterSuccessfulLogin($user, method: 'otp_email', identifier: $email);
        session()->regenerate();

        return app(\Filament\Auth\Http\Responses\Contracts\LoginResponse::class);
    }

    // ──────────────────────────────────────────────────────────────────
    // METHOD 3 — LOGIN CODE (ABC123 format)
    // ──────────────────────────────────────────────────────────────────

    private function authenticateLoginCode(): ?\Filament\Auth\Http\Responses\Contracts\LoginResponse
    {
        $data = $this->form->getState();
        $code = strtoupper(trim((string) ($data['login_code_input'] ?? '')));

        if ($code === '') {
            throw ValidationException::withMessages(['data.login_code_input' => 'Please enter your login code.']);
        }

        // Per-code lockout (prevents brute forcing all codes)
        $this->checkAccountLockout($code, 'data.login_code_input');

        /** @var User|null $user */
        $user = User::query()->where('login_code', $code)->first();

        if (! $user) {
            $this->recordFailedAttempt($code);
            $this->logAttempt(method: 'code', identifier: $code, success: false, reason: 'invalid_code');

            $remaining = $this->remainingAttempts($code);
            throw ValidationException::withMessages([
                'data.login_code_input' => $remaining > 0
                    ? "Invalid login code. {$remaining} attempt(s) remaining."
                    : 'Login code locked for ' . self::LOCKOUT_MINUTES . ' minutes.',
            ]);
        }

        if (! $user->is_active) {
            $this->logAttempt(method: 'code', user: $user, identifier: $code, success: false, reason: 'inactive');
            throw ValidationException::withMessages(['data.login_code_input' => 'Your account has been disabled. Contact your administrator.']);
        }

        $this->clearFailedAttempts($code);
        Filament::auth()->login($user, remember: false);
        $this->afterSuccessfulLogin($user, method: 'code', identifier: $code);
        session()->regenerate();

        return app(\Filament\Auth\Http\Responses\Contracts\LoginResponse::class);
    }

    // ──────────────────────────────────────────────────────────────────
    // METHOD 4 — MOBILE OTP (SMS — plug-in ready)
    // ──────────────────────────────────────────────────────────────────

    private function authenticateMobileOtp(): ?\Filament\Auth\Http\Responses\Contracts\LoginResponse
    {
        $data  = $this->form->getState();
        $phone = trim((string) ($data['otp_phone'] ?? ''));
        $otp   = (string) ($data['otp_mobile_input'] ?? '');

        /** @var SmsService $sms */
        $sms = app(SmsService::class);

        if (! $sms->isConfigured()) {
            $this->logAttempt(method: 'otp_mobile', identifier: $phone, success: false, reason: 'sms_not_configured');
            throw ValidationException::withMessages([
                'data.otp_phone' => 'Mobile OTP is not configured yet. Please contact your administrator or use another login method.',
            ]);
        }

        // ── Step 1: No OTP entered → send SMS ──
        if ($otp === '') {
            if (empty($phone)) {
                throw ValidationException::withMessages(['data.otp_phone' => 'Please enter your mobile number.']);
            }

            $this->checkAccountLockout($phone, 'data.otp_phone');

            // Check user exists by phone
            $user = User::query()->where('phone', $phone)->first();
            if (! $user) {
                // Generic message — don't reveal if number is registered
                $this->logAttempt(method: 'otp_mobile', identifier: $phone, success: false, reason: 'user_not_found');
            } else {
                $this->sendSmsOtp($phone, $sms);
            }

            Notification::make()
                ->title('OTP sent if this number is registered.')
                ->body('Check your SMS. Code expires in ' . self::OTP_EXPIRY_MINUTES . ' minutes.')
                ->success()
                ->send();

            return null;
        }

        // ── Step 2: OTP entered → verify ──
        $this->checkAccountLockout($phone, 'data.otp_phone');

        $cacheKey  = $this->phoneOtpCacheKey($phone);
        $hashedOtp = Cache::get($cacheKey);

        if (! is_string($hashedOtp)) {
            throw ValidationException::withMessages(['data.otp_phone' => 'OTP has expired. Please request a new one.']);
        }

        // Brute-force protection
        $attemptsKey = 'auth:otp_tries:' . $cacheKey;
        $otpAttempts = (int) Cache::get($attemptsKey, 0);

        if ($otpAttempts >= self::MAX_OTP_ATTEMPTS) {
            Cache::forget($cacheKey);
            Cache::forget($attemptsKey);
            throw ValidationException::withMessages(['data.otp_mobile_input' => 'Too many incorrect attempts. Request a new OTP.']);
        }

        if (! Hash::check($otp, $hashedOtp)) {
            Cache::put($attemptsKey, $otpAttempts + 1, now()->addMinutes(self::OTP_EXPIRY_MINUTES));
            $this->recordFailedAttempt($phone);
            $this->logAttempt(method: 'otp_mobile', identifier: $phone, success: false, reason: 'invalid_otp');

            $left = self::MAX_OTP_ATTEMPTS - ($otpAttempts + 1);
            throw ValidationException::withMessages([
                'data.otp_mobile_input' => "Invalid OTP. {$left} attempt(s) remaining.",
            ]);
        }

        Cache::forget($cacheKey);
        Cache::forget($attemptsKey);

        /** @var User|null $user */
        $user = User::query()->where('phone', $phone)->first();

        if (! $user) {
            throw ValidationException::withMessages(['data.otp_phone' => 'No account found for this number.']);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages(['data.otp_phone' => 'Your account has been disabled.']);
        }

        $this->clearFailedAttempts($phone);
        Filament::auth()->login($user, remember: false);
        $this->afterSuccessfulLogin($user, method: 'otp_mobile', identifier: $phone);
        session()->regenerate();

        return app(\Filament\Auth\Http\Responses\Contracts\LoginResponse::class);
    }

    // ──────────────────────────────────────────────────────────────────
    // PUBLIC LIVEWIRE ACTIONS — called by in-form "Send OTP" buttons
    // ──────────────────────────────────────────────────────────────────

    public function doSendEmailOtp(): void
    {
        $state = $this->form->getState();
        $email = trim((string) ($state['otp_email'] ?? ''));

        if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Notification::make()
                ->title('Enter a valid email address first.')
                ->danger()
                ->send();
            return;
        }

        if (Cache::has($this->lockoutKey($email))) {
            Notification::make()
                ->title('Account locked for ' . self::LOCKOUT_MINUTES . ' minutes.')
                ->body('Too many failed attempts. Please wait before trying again.')
                ->danger()
                ->send();
            return;
        }

        $otp        = (string) random_int(100000, 999999);
        $cacheKey   = $this->emailOtpCacheKey($email);
        $schoolName = tenant('name') ?? 'Simption';

        Cache::put($cacheKey, Hash::make($otp), now()->addMinutes(self::OTP_EXPIRY_MINUTES));
        Cache::forget('auth:otp_tries:' . $cacheKey);

        try {
            Mail::to($email)->send(new LoginOtpMail(
                otp:           $otp,
                schoolName:    $schoolName,
                expiryMinutes: self::OTP_EXPIRY_MINUTES,
            ));

            $this->logAttempt(method: 'otp_email', identifier: $email, success: false, reason: 'otp_sent');

            Notification::make()
                ->title('OTP sent!')
                ->body("Check {$email} for the 6-digit code. Valid for " . self::OTP_EXPIRY_MINUTES . ' minutes.')
                ->success()
                ->send();
        } catch (\Throwable) {
            Notification::make()
                ->title('Email delivery failed.')
                ->body('Could not send OTP. Please try again or use a different login method.')
                ->danger()
                ->send();
        }
    }

    public function doSendPhoneOtp(): void
    {
        $state = $this->form->getState();
        $phone = trim((string) ($state['otp_phone'] ?? ''));
        $sms   = app(SmsService::class);

        if (! $phone) {
            Notification::make()
                ->title('Enter your mobile number first.')
                ->danger()
                ->send();
            return;
        }

        if (! $sms->isConfigured()) {
            Notification::make()
                ->title('Mobile OTP is not configured.')
                ->body('Contact your administrator to enable SMS login.')
                ->warning()
                ->send();
            return;
        }

        if (Cache::has($this->lockoutKey($phone))) {
            Notification::make()
                ->title('Number locked for ' . self::LOCKOUT_MINUTES . ' minutes.')
                ->danger()
                ->send();
            return;
        }

        $otp        = (string) random_int(100000, 999999);
        $cacheKey   = $this->phoneOtpCacheKey($phone);
        $schoolName = tenant('name') ?? 'Simption';

        Cache::put($cacheKey, Hash::make($otp), now()->addMinutes(self::OTP_EXPIRY_MINUTES));
        Cache::forget('auth:otp_tries:' . $cacheKey);

        // Only send SMS if user exists (security: don't reveal registration status in notification)
        $user = User::query()->where('phone', $phone)->first();
        if ($user) {
            $sms->sendOtp($phone, $otp, $schoolName);
        }

        $this->logAttempt(method: 'otp_mobile', identifier: $phone, success: false, reason: 'otp_sent');

        Notification::make()
            ->title('OTP sent if this number is registered.')
            ->body('Check your SMS. Code expires in ' . self::OTP_EXPIRY_MINUTES . ' minutes.')
            ->success()
            ->send();
    }

    // ──────────────────────────────────────────────────────────────────
    // OTP SENDERS (private — used by both button and form submit flow)
    // ──────────────────────────────────────────────────────────────────

    private function sendEmailOtp(string $email): void
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw ValidationException::withMessages(['data.otp_email' => 'Enter a valid email address.']);
        }

        $otp        = (string) random_int(100000, 999999);
        $cacheKey   = $this->emailOtpCacheKey($email);
        $schoolName = tenant('name') ?? 'Simption';

        Cache::put($cacheKey, Hash::make($otp), now()->addMinutes(self::OTP_EXPIRY_MINUTES));
        // Reset OTP attempt counter on fresh send
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
    // PER-ACCOUNT LOCKOUT SYSTEM
    // ──────────────────────────────────────────────────────────────────

    /**
     * Throws a ValidationException if the identifier is currently locked out.
     */
    private function checkAccountLockout(string $identifier, string $fieldKey): void
    {
        if (Cache::has($this->lockoutKey($identifier))) {
            $this->logAttempt(method: 'lockout', identifier: $identifier, success: false, reason: 'account_locked');
            throw ValidationException::withMessages([
                $fieldKey => 'Account temporarily locked for ' . self::LOCKOUT_MINUTES . ' minutes due to too many failed attempts.',
            ]);
        }
    }

    /**
     * Increments failed attempt counter. Applies lockout when threshold is reached.
     */
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

    /**
     * How many attempts remain before lockout.
     */
    private function remainingAttempts(string $identifier): int
    {
        $attempts = (int) Cache::get($this->failedAttemptsKey($identifier), 0);
        return max(0, self::MAX_FAILED_ATTEMPTS - $attempts);
    }

    /**
     * Clears failed attempt counter and any lockout on successful login.
     */
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
        // Update last login metadata
        $user->forceFill([
            'last_login_at' => Carbon::now(),
            'last_login_ip' => request()->ip(),
        ])->save();

        // Log successful attempt
        $this->logAttempt(method: $method, user: $user, identifier: $identifier, success: true);

        // Create session record
        UserSession::create([
            'user_id'          => $user->id,
            'session_id'       => session()->getId(),
            'method'           => $method,
            'ip_address'       => request()->ip(),
            'user_agent'       => request()->userAgent(),
            'logged_in_at'     => Carbon::now(),
            'last_activity_at' => Carbon::now(),
        ]);

        // Spatie audit log
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
