<?php

declare(strict_types=1);

namespace App\Modules\Security\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginAttempt extends Model
{
    // Points to the central login_activities table (no separate login_attempts table).
    protected $table = 'login_activities';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'identifier',   // email / phone / code
        'method',
        'ip_address',
        'user_agent',
        'is_success',
        'failure_reason',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'is_success' => 'bool',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getFailedAttempts(string $identifier, int $minutes = 30): int
    {
        return self::where('identifier', $identifier)
            ->where('is_success', false)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    public static function getFailedAttemptsByIp(string $ip, int $minutes = 30): int
    {
        return self::where('ip_address', $ip)
            ->where('is_success', false)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->count();
    }
}
