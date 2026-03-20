<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class SecuritySettings extends Page
{
    protected static \BackedEnum|string|null $navigationIcon  = 'heroicon-o-shield-check';
    protected static \UnitEnum|string|null  $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Security';
    protected static ?string $title           = 'Security Settings';
    protected string         $view            = 'filament.admin.pages.security-settings';
    protected static ?int    $navigationSort  = 99;

    public static function canAccess(): bool
    {
        return auth()->check();
    }

    // ── 2FA form state ─────────────────────────────────────────────────
    public bool   $enabled      = false;
    public string $method       = 'email_otp';

    // Mobile OTP
    public string $mobileTarget = 'user_registered';
    public string $fixedMobile  = '';

    // Email OTP
    public string $emailTarget  = 'user_registered';
    public string $fixedEmail   = '';

    // Static code
    public string $staticCode   = '';

    public function mount(): void
    {
        $config = DB::connection('central')
            ->table('tenant_2fa_configs')
            ->where('tenant_id', tenant('id'))
            ->first();

        if ($config) {
            $this->enabled      = (bool) $config->enabled;
            $this->method       = $config->method ?? 'email_otp';
            $this->mobileTarget = $config->mobile_target ?? 'user_registered';
            $this->fixedMobile  = $config->fixed_mobile ?? '';
            $this->emailTarget  = $config->email_target ?? 'user_registered';
            $this->fixedEmail   = $config->fixed_email ?? '';
            $this->staticCode   = $config->static_code ?? '';
        }
    }

    public function save(): void
    {
        if ($this->enabled) {
            if (empty($this->method)) {
                Notification::make()->title('Please select a 2FA method.')->danger()->send();
                return;
            }
            if ($this->method === 'static_code' && empty(trim($this->staticCode))) {
                Notification::make()->title('Please enter a static security code.')->danger()->send();
                return;
            }
            if ($this->method === 'mobile_otp' && $this->mobileTarget === 'fixed' && empty(trim($this->fixedMobile))) {
                Notification::make()->title('Please enter the fixed mobile number.')->danger()->send();
                return;
            }
            if ($this->method === 'email_otp' && $this->emailTarget === 'fixed' && empty(trim($this->fixedEmail))) {
                Notification::make()->title('Please enter the fixed email address.')->danger()->send();
                return;
            }
        }

        DB::connection('central')
            ->table('tenant_2fa_configs')
            ->updateOrInsert(
                ['tenant_id' => tenant('id')],
                [
                    'enabled'       => $this->enabled,
                    'method'        => $this->enabled ? $this->method : null,
                    'mobile_target' => $this->mobileTarget,
                    'fixed_mobile'  => ($this->method === 'mobile_otp' && $this->mobileTarget === 'fixed')
                        ? trim($this->fixedMobile) : null,
                    'email_target'  => $this->emailTarget,
                    'fixed_email'   => ($this->method === 'email_otp' && $this->emailTarget === 'fixed')
                        ? trim($this->fixedEmail) : null,
                    'static_code'   => $this->method === 'static_code'
                        ? trim($this->staticCode) : null,
                    'updated_at'    => now()->toDateTimeString(),
                    'created_at'    => now()->toDateTimeString(),
                ]
            );

        Notification::make()
            ->title('Security settings saved')
            ->body($this->enabled
                ? '2FA is now enabled for all users.'
                : '2FA has been disabled.'
            )
            ->success()
            ->send();
    }

    public function disable(): void
    {
        $this->enabled = false;
        $this->save();
    }
}
