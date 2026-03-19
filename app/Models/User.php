<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'login_code',
        'is_active',
        'last_login_at',
        'last_login_ip',
        // Security module fields
        'is_locked',
        'locked_until',
        'failed_login_attempts',
        'last_failed_attempt',
        'category',
        'allowed_access_times',
        'require_mfa',
        'mfa_method',
        'mfa_secret',
        // Users module fields
        'avatar',
        'role_type',
        'role_label',
        'restrict_access',
        'can_login_app',
        'show_login_status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'      => 'datetime',
            'password'               => 'hashed',
            'is_active'              => 'boolean',
            'last_login_at'          => 'datetime',
            // Security module casts
            'is_locked'              => 'boolean',
            'locked_until'           => 'datetime',
            'failed_login_attempts'  => 'integer',
            'last_failed_attempt'    => 'datetime',
            'allowed_access_times'   => 'json',
            'require_mfa'            => 'boolean',
            // Users module casts
            'restrict_access'        => 'boolean',
            'can_login_app'          => 'boolean',
            'show_login_status'      => 'boolean',
        ];
    }

    // ── Security module relationships ─────────────────────────────────────────

    public function loginAttempts(): HasMany
    {
        return $this->hasMany(\App\Modules\Security\Models\LoginAttempt::class);
    }

    public function accessRules(): HasMany
    {
        return $this->hasMany(\App\Modules\Security\Models\UserAccessRule::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(\App\Modules\Security\Models\DeviceFingerprint::class);
    }

    public function securityAuditLogs(): HasMany
    {
        return $this->hasMany(\App\Modules\Security\Models\SecurityAuditLog::class);
    }

    /**
     * Generate a unique 6-character alphanumeric login code (e.g. "ABC123").
     * Keeps generating until unique within this tenant's DB.
     */
    public static function generateLoginCode(): string
    {
        do {
            // 3 uppercase letters + 3 digits — easy to read, hard to guess
            $letters = strtoupper(Str::random(3));
            $digits  = (string) random_int(100, 999);
            $code    = $letters . $digits;
        } while (static::where('login_code', $code)->exists());

        return $code;
    }
}
