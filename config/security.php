<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CAPTCHA Configuration
    |--------------------------------------------------------------------------
    */
    'captcha' => [
        'provider' => env('CAPTCHA_PROVIDER', 'math'), // 'google', 'hcaptcha', 'math'
        'enabled'  => env('CAPTCHA_ENABLED', true),
    ],

    'recaptcha' => [
        'key'             => env('RECAPTCHA_KEY'),
        'secret'          => env('RECAPTCHA_SECRET'),
        'score_threshold' => 0.5,
    ],

    'hcaptcha' => [
        'key'    => env('HCAPTCHA_KEY'),
        'secret' => env('HCAPTCHA_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Login Security
    |--------------------------------------------------------------------------
    */
    'login' => [
        'max_failed_attempts'    => env('LOGIN_MAX_FAILED_ATTEMPTS', 5),
        'lockout_minutes'        => env('LOGIN_LOCKOUT_MINUTES', 30),
        'captcha_trigger_attempts' => env('LOGIN_CAPTCHA_TRIGGER_ATTEMPTS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Security
    |--------------------------------------------------------------------------
    */
    'session' => [
        'timeout_minutes' => env('SESSION_TIMEOUT_MINUTES', 60),
        'require_mfa'     => env('SESSION_REQUIRE_MFA', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Device Fingerprinting
    |--------------------------------------------------------------------------
    */
    'device_fingerprint' => [
        'enabled'        => env('DEVICE_FINGERPRINT_ENABLED', false),
        'trust_only_once' => env('DEVICE_FINGERPRINT_TRUST_ONCE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | IP Whitelisting
    |--------------------------------------------------------------------------
    */
    'ip_whitelist' => [
        'enabled'     => env('IP_WHITELIST_ENABLED', false),
        'allowed_ips' => array_filter(explode(',', env('IP_WHITELIST_IPS', ''))),
    ],

    /*
    |--------------------------------------------------------------------------
    | Time-based Access Control
    |--------------------------------------------------------------------------
    */
    'access_control' => [
        'time_restrictions_enabled' => env('ACCESS_TIME_RESTRICTIONS', false),
        'allowed_hours'             => env('ACCESS_ALLOWED_HOURS', '09:00-17:00'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Logging
    |--------------------------------------------------------------------------
    */
    'audit' => [
        'enabled'              => env('AUDIT_LOGGING_ENABLED', true),
        'retention_days'       => env('AUDIT_RETENTION_DAYS', 90),
        'log_successful_logins' => env('AUDIT_LOG_SUCCESSFUL_LOGINS', true),
        'log_failed_logins'    => env('AUDIT_LOG_FAILED_LOGINS', true),
    ],
];
