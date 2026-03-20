<?php

declare(strict_types=1);

namespace App\Modules\Security\Livewire;

use App\Modules\Security\Models\SecuritySetting;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('panel.layouts.app')]
#[Title('Security Settings')]
class SecuritySettings extends Component
{
    // ── Login / lockout settings ────────────────────────────────────────────
    public bool $captcha_enabled           = true;
    public int  $failed_attempts_threshold = 5;
    public int  $captcha_trigger_attempts  = 3;
    public int  $lockout_duration_minutes  = 30;

    // ── Session settings ────────────────────────────────────────────────────
    public bool $session_timeout_enabled    = true;
    public int  $session_timeout_minutes    = 60;
    public bool $device_fingerprint_enabled = false;

    // ── Access control settings ─────────────────────────────────────────────
    public bool $time_restriction_enabled = false;
    public bool $ip_whitelist_enabled     = false;

    // ── 2FA settings ────────────────────────────────────────────────────────
    public bool   $twofa_enabled = false;
    public string $twofa_method  = 'email_otp';
    public string $mobile_target = 'user_registered';
    public string $fixed_mobile  = '';
    public string $email_target  = 'user_registered';
    public string $fixed_email   = '';
    public string $static_code   = '';

    public bool $saved = false;

    public function mount(): void
    {
        // Load login/session/access settings
        $settings = SecuritySetting::getCurrent();

        $this->captcha_enabled            = $settings->captcha_enabled;
        $this->failed_attempts_threshold  = $settings->failed_attempts_threshold;
        $this->captcha_trigger_attempts   = $settings->captcha_trigger_attempts;
        $this->lockout_duration_minutes   = $settings->lockout_duration_minutes;
        $this->session_timeout_enabled    = $settings->session_timeout_enabled;
        $this->session_timeout_minutes    = $settings->session_timeout_minutes;
        $this->time_restriction_enabled   = $settings->time_restriction_enabled;
        $this->ip_whitelist_enabled       = $settings->ip_whitelist_enabled;
        $this->device_fingerprint_enabled = $settings->device_fingerprint_enabled;

        // Load 2FA config from central DB
        $twoFa = DB::connection('central')
            ->table('tenant_2fa_configs')
            ->where('tenant_id', tenant('id'))
            ->first();

        if ($twoFa) {
            $this->twofa_enabled = (bool) $twoFa->enabled;
            $this->twofa_method  = $twoFa->method ?? 'email_otp';
            $this->mobile_target = $twoFa->mobile_target ?? 'user_registered';
            $this->fixed_mobile  = $twoFa->fixed_mobile ?? '';
            $this->email_target  = $twoFa->email_target ?? 'user_registered';
            $this->fixed_email   = $twoFa->fixed_email ?? '';
            $this->static_code   = $twoFa->static_code ?? '';
        }
    }

    public function save(): void
    {
        // Validate 2FA inputs
        if ($this->twofa_enabled) {
            if (empty($this->twofa_method)) {
                $this->addError('twofa_method', 'Please select a 2FA method.');
                return;
            }
            if ($this->twofa_method === 'static_code' && empty(trim($this->static_code))) {
                $this->addError('static_code', 'Please enter a static security code.');
                return;
            }
            if ($this->twofa_method === 'mobile_otp' && $this->mobile_target === 'fixed' && empty(trim($this->fixed_mobile))) {
                $this->addError('fixed_mobile', 'Please enter the fixed mobile number.');
                return;
            }
            if ($this->twofa_method === 'email_otp' && $this->email_target === 'fixed' && empty(trim($this->fixed_email))) {
                $this->addError('fixed_email', 'Please enter the fixed email address.');
                return;
            }
        }

        $this->validate([
            'failed_attempts_threshold' => 'required|integer|min:1|max:50',
            'captcha_trigger_attempts'  => 'required|integer|min:1|max:20',
            'lockout_duration_minutes'  => 'required|integer|min:1|max:1440',
            'session_timeout_minutes'   => 'required|integer|min:5|max:1440',
        ]);

        // Save login/session/access settings
        $settings = SecuritySetting::getCurrent();
        $settings->update([
            'captcha_enabled'            => $this->captcha_enabled,
            'failed_attempts_threshold'  => $this->failed_attempts_threshold,
            'captcha_trigger_attempts'   => $this->captcha_trigger_attempts,
            'lockout_duration_minutes'   => $this->lockout_duration_minutes,
            'session_timeout_enabled'    => $this->session_timeout_enabled,
            'session_timeout_minutes'    => $this->session_timeout_minutes,
            'time_restriction_enabled'   => $this->time_restriction_enabled,
            'ip_whitelist_enabled'       => $this->ip_whitelist_enabled,
            'device_fingerprint_enabled' => $this->device_fingerprint_enabled,
        ]);

        // Save 2FA config to central DB
        DB::connection('central')
            ->table('tenant_2fa_configs')
            ->updateOrInsert(
                ['tenant_id' => tenant('id')],
                [
                    'enabled'       => $this->twofa_enabled,
                    'method'        => $this->twofa_enabled ? $this->twofa_method : null,
                    'mobile_target' => $this->mobile_target,
                    'fixed_mobile'  => ($this->twofa_method === 'mobile_otp' && $this->mobile_target === 'fixed')
                        ? trim($this->fixed_mobile) : null,
                    'email_target'  => $this->email_target,
                    'fixed_email'   => ($this->twofa_method === 'email_otp' && $this->email_target === 'fixed')
                        ? trim($this->fixed_email) : null,
                    'static_code'   => $this->twofa_method === 'static_code'
                        ? trim($this->static_code) : null,
                    'updated_at'    => now()->toDateTimeString(),
                    'created_at'    => now()->toDateTimeString(),
                ]
            );

        $this->saved = true;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.modules.security.security-settings');
    }
}
