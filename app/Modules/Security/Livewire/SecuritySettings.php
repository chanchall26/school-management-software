<?php

declare(strict_types=1);

namespace App\Modules\Security\Livewire;

use App\Modules\Security\Models\SecuritySetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('panel.layouts.app')]
#[Title('Security Settings')]
class SecuritySettings extends Component
{
    public bool $captcha_enabled           = true;
    public int  $failed_attempts_threshold = 5;
    public int  $captcha_trigger_attempts  = 3;
    public int  $lockout_duration_minutes  = 30;
    public bool $session_timeout_enabled   = true;
    public int  $session_timeout_minutes   = 60;
    public bool $time_restriction_enabled  = false;
    public bool $ip_whitelist_enabled      = false;
    public bool $device_fingerprint_enabled = false;

    public bool $saved = false;

    public function mount(): void
    {
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
    }

    public function save(): void
    {
        $this->validate([
            'failed_attempts_threshold' => 'required|integer|min:1|max:50',
            'captcha_trigger_attempts'  => 'required|integer|min:1|max:20',
            'lockout_duration_minutes'  => 'required|integer|min:1|max:1440',
            'session_timeout_minutes'   => 'required|integer|min:5|max:1440',
        ]);

        $settings = SecuritySetting::getCurrent();
        $settings->update([
            'captcha_enabled'           => $this->captcha_enabled,
            'failed_attempts_threshold' => $this->failed_attempts_threshold,
            'captcha_trigger_attempts'  => $this->captcha_trigger_attempts,
            'lockout_duration_minutes'  => $this->lockout_duration_minutes,
            'session_timeout_enabled'   => $this->session_timeout_enabled,
            'session_timeout_minutes'   => $this->session_timeout_minutes,
            'time_restriction_enabled'  => $this->time_restriction_enabled,
            'ip_whitelist_enabled'      => $this->ip_whitelist_enabled,
            'device_fingerprint_enabled' => $this->device_fingerprint_enabled,
        ]);

        $this->saved = true;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.modules.security.security-settings');
    }
}
