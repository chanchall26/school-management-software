<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages\Auth;

use App\Models\LoginActivity;
use App\Models\User;
use App\Models\UserSession;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function getHeading(): string
    {
        $tenantName = (string) (tenant('name') ?? data_get(tenant('data'), 'name') ?? tenant('id') ?? 'Simption');

        return $tenantName . ' — Login';
    }

    /**
     * In your version of Filament, the form method uses the new Schema system.
     */
    public function form(Schema $schema): Schema
    {
        // Check central database for allowed methods
        $methods = DB::connection('central')
            ->table('tenant_login_methods')
            ->where('tenant_id', tenant('id'))
            ->first();

        // Fallback to tenant data if table row is missing
        $isOtpEmail = $methods->method_otp_email ?? in_array('otp', tenant('data')['login_methods'] ?? []);
        $isPassword = $methods->method_password ?? in_array('password', tenant('data')['login_methods'] ?? ['password']);
        $isLoginCode = $methods->method_login_code ?? in_array('code', tenant('data')['login_methods'] ?? []);
        $isOtpMobile = $methods->method_otp_mobile ?? in_array('mobile', tenant('data')['login_methods'] ?? []);

        $tabs = [];

        if ($isOtpEmail) {
            $tabs[] = Tab::make('OTP (Email)')
                ->id('otp_email')
                ->schema([
                    Section::make()
                        ->schema([
                            TextInput::make('otp_email')
                                ->label('Email')
                                ->email(),
                            TextInput::make('otp')
                                ->label('OTP')
                                ->helperText('Enter the 6-digit OTP. Submit without OTP to send one.')
                                ->numeric()
                                ->minLength(6)
                                ->maxLength(6),
                        ]),
                ]);
        }

        if ($isPassword) {
            $tabs[] = Tab::make('ID + Password')
                ->id('password')
                ->schema([
                    Section::make()
                        ->schema([
                            TextInput::make('email')
                                ->label('Email')
                                ->email(),
                            TextInput::make('password')
                                ->label('Password')
                                ->password(),
                        ]),
                ]);
        }

        if ($isLoginCode) {
            $tabs[] = Tab::make('Login Code')
                ->id('login_code')
                ->schema([
                    Section::make()
                        ->schema([
                            TextInput::make('login_code_input')
                                ->label('Login Code')
                                ->autocomplete(false),
                        ]),
                ]);
        }

        if ($isOtpMobile) {
            $tabs[] = Tab::make('Mobile OTP')
                ->id('otp_mobile')
                ->schema([
                    Section::make()
                        ->schema([
                            TextInput::make('otp_phone')
                                ->label('Mobile number')
                                ->tel(),
                            TextInput::make('otp_mobile_input')
                                ->label('OTP')
                                ->numeric()
                                ->minLength(6)
                                ->maxLength(6),
                        ]),
                ]);
        }

        // Determine default tab
        $defaultTab = 'password';
        if ($isOtpEmail) $defaultTab = 'otp_email';
        elseif ($isPassword) $defaultTab = 'password';
        elseif ($isLoginCode) $defaultTab = 'login_code';
        elseif ($isOtpMobile) $defaultTab = 'otp_mobile';

        return $schema
            ->statePath('data')
            ->components([
                // We use the Hidden component from Forms, but wrap it in Schema
                Hidden::make('active_tab')
                    ->id('hidden_active_tab')
                    ->default($defaultTab),

                // Professional silent sync using a Placeholder
                Placeholder::make('tab_sync')
                    ->hiddenLabel()
                    ->content(new HtmlString('
                        <div x-init="$watch(\'tab\', value => { 
                            let el = document.getElementById(\'hidden_active_tab\'); 
                            if(el) { 
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

    public function authenticate(): ?\Filament\Auth\Http\Responses\Contracts\LoginResponse
    {
        $data = $this->form->getState();
        $activeTab = $data['active_tab'] ?? 'password';

        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title("Too many attempts. Try again in {$exception->secondsUntilAvailable}s.")
                ->danger()
                ->send();

            $this->logAttempt(method: $activeTab, success: false, reason: 'rate_limited');

            return null;
        }

        return match ($activeTab) {
            'otp_email' => $this->authenticateOtpEmail(),
            'password' => $this->authenticatePassword(),
            'login_code' => $this->authenticateLoginCode(),
            'otp_mobile' => $this->authenticateMobileOtpPlaceholder(),
            default => $this->authenticatePassword(),
        };
    }

    private function authenticatePassword(): ?\Filament\Auth\Http\Responses\Contracts\LoginResponse
    {
        $data = $this->form->getState();

        $credentials = [
            'email' => $data['email'] ?? null,
            'password' => $data['password'] ?? null,
        ];

        if (! Filament::auth()->attempt($credentials, $data['remember'] ?? false)) {
            $this->logAttempt(method: 'password', identifier: (string) ($credentials['email'] ?? ''), success: false, reason: 'invalid_credentials');
            throw ValidationException::withMessages(['data.email' => __('filament-panels::auth/pages/login.messages.failed')]);
        }

        /** @var User $user */
        $user = Filament::auth()->user();

        if (! $user->is_active) {
            Filament::auth()->logout();
            $this->logAttempt(method: 'password', user: $user, identifier: $user->email, success: false, reason: 'inactive');
            throw ValidationException::withMessages(['data.email' => 'Account is disabled.']);
        }

        $this->afterSuccessfulLogin($user, method: 'password', identifier: $user->email);

        session()->regenerate();

        return app(\Filament\Auth\Http\Responses\Contracts\LoginResponse::class);
    }

    private function authenticateOtpEmail(): ?\Filament\Auth\Http\Responses\Contracts\LoginResponse
    {
        $data = $this->form->getState();
        $email = (string) ($data['otp_email'] ?? '');
        $otp = (string) ($data['otp'] ?? '');

        if ($otp === '') {
            $this->sendEmailOtp($email);
            Notification::make()->title('OTP sent. Check your email.')->success()->send();
            $this->logAttempt(method: 'otp_email', identifier: $email, success: true, reason: null);
            return null;
        }

        $cacheKey = $this->emailOtpCacheKey($email);
        $hashedOtp = Cache::get($cacheKey);

        if (! is_string($hashedOtp) || ! Hash::check($otp, $hashedOtp)) {
            $this->logAttempt(method: 'otp_email', identifier: $email, success: false, reason: 'invalid_otp');
            throw ValidationException::withMessages(['data.otp' => 'Invalid OTP.']);
        }

        Cache::forget($cacheKey);

        /** @var User|null $user */
        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            $this->logAttempt(method: 'otp_email', identifier: $email, success: false, reason: 'user_not_found');
            throw ValidationException::withMessages(['data.otp_email' => 'User not found.']);
        }

        if (! $user->is_active) {
            $this->logAttempt(method: 'otp_email', user: $user, identifier: $email, success: false, reason: 'inactive');
            throw ValidationException::withMessages(['data.otp_email' => 'Account is disabled.']);
        }

        Filament::auth()->login($user, remember: false);
        $this->afterSuccessfulLogin($user, method: 'otp_email', identifier: $email);

        session()->regenerate();

        return app(\Filament\Auth\Http\Responses\Contracts\LoginResponse::class);
    }

    private function authenticateLoginCode(): ?\Filament\Auth\Http\Responses\Contracts\LoginResponse
    {
        $data = $this->form->getState();
        $code = (string) ($data['login_code_input'] ?? '');

        /** @var User|null $user */
        $user = User::query()->where('login_code', $code)->first();

        if (! $user) {
            $this->logAttempt(method: 'code', identifier: $code, success: false, reason: 'invalid_code');
            throw ValidationException::withMessages(['data.login_code_input' => 'Invalid login code.']);
        }

        if (! $user->is_active) {
            $this->logAttempt(method: 'code', user: $user, identifier: $code, success: false, reason: 'inactive');
            throw ValidationException::withMessages(['data.login_code_input' => 'Account is disabled.']);
        }

        Filament::auth()->login($user, remember: false);
        $this->afterSuccessfulLogin($user, method: 'code', identifier: $code);

        session()->regenerate();

        return app(\Filament\Auth\Http\Responses\Contracts\LoginResponse::class);
    }

    private function authenticateMobileOtpPlaceholder(): ?\Filament\Auth\Http\Responses\Contracts\LoginResponse
    {
        $data = $this->form->getState();
        $phone = (string) ($data['otp_phone'] ?? '');

        $this->logAttempt(method: 'otp_mobile', identifier: $phone, success: false, reason: 'sms_provider_not_configured');
        throw ValidationException::withMessages(['data.otp_phone' => 'Mobile OTP is not configured yet (SMS provider required).']);
    }

    private function sendEmailOtp(string $email): void
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw ValidationException::withMessages(['data.otp_email' => 'Enter a valid email address.']);
        }

        $otp = (string) random_int(100000, 999999);
        Cache::put($this->emailOtpCacheKey($email), Hash::make($otp), now()->addMinutes(10));

        // Minimal mailer; works with MAIL_MAILER=log too.
        Mail::raw("Your Simption OTP is: {$otp}\n\nThis OTP expires in 10 minutes.", function ($message) use ($email) {
            $message->to($email)->subject('Simption Login OTP');
        });
    }

    private function emailOtpCacheKey(string $email): string
    {
        return 'auth:otp_email:' . Str::lower($email);
    }

    private function afterSuccessfulLogin(User $user, string $method, string $identifier): void
    {
        $user->forceFill([
            'last_login_at' => Carbon::now(),
            'last_login_ip' => request()->ip(),
        ])->save();

        $this->logAttempt(method: $method, user: $user, identifier: $identifier, success: true, reason: null);

        UserSession::create([
            'user_id' => $user->id,
            'session_id' => session()->getId(),
            'method' => $method,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'logged_in_at' => Carbon::now(),
            'last_activity_at' => Carbon::now(),
        ]);

        activity()
            ->causedBy($user)
            ->withProperties([
                'method' => $method,
                'ip' => request()->ip(),
            ])
            ->log('login');
    }

    private function logAttempt(string $method, ?User $user = null, ?string $identifier = null, bool $success = false, ?string $reason = null): void
    {
        LoginActivity::create([
            'user_id' => $user?->id,
            'identifier' => $identifier,
            'method' => $method,
            'is_success' => $success,
            'failure_reason' => $reason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => Carbon::now(),
        ]);
    }
}
