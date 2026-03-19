<?php

declare(strict_types=1);

namespace App\Modules\Security\Models;

use Illuminate\Database\Eloquent\Model;

class SecuritySetting extends Model
{
    protected $fillable = [
        'captcha_enabled',
        'failed_attempts_threshold',
        'captcha_trigger_attempts',
        'lockout_duration_minutes',
        'session_timeout_enabled',
        'session_timeout_minutes',
        'time_restriction_enabled',
        'ip_whitelist_enabled',
        'device_fingerprint_enabled',
        'allowed_login_hours',
    ];

    protected function casts(): array
    {
        return [
            'captcha_enabled'          => 'bool',
            'session_timeout_enabled'  => 'bool',
            'time_restriction_enabled' => 'bool',
            'ip_whitelist_enabled'     => 'bool',
            'device_fingerprint_enabled' => 'bool',
            'allowed_login_hours'      => 'json',
        ];
    }

    /**
     * Get or create the settings record for the current tenant.
     * Since each tenant has their own DB, there is only ever one row.
     */
    public static function getCurrent(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'captcha_enabled'          => true,
                'failed_attempts_threshold' => 5,
                'captcha_trigger_attempts'  => 3,
                'lockout_duration_minutes'  => 30,
                'session_timeout_enabled'   => true,
                'session_timeout_minutes'   => 60,
                'time_restriction_enabled'  => false,
                'ip_whitelist_enabled'      => false,
                'device_fingerprint_enabled' => false,
            ]
        );
    }
}
