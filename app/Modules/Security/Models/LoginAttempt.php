<?php

declare(strict_types=1);

namespace App\Modules\Security\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoginAttempt extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'is_success',
        'failure_reason',
        'system_info',
        'attempted_at',
    ];

    protected function casts(): array
    {
        return [
            'is_success'   => 'bool',
            'system_info'  => 'json',
            'attempted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getFailedAttempts(string $email, int $minutes = 30): int
    {
        return self::where('email', $email)
            ->where('is_success', false)
            ->where('attempted_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    public static function getFailedAttemptsByIp(string $ip, int $minutes = 30): int
    {
        return self::where('ip_address', $ip)
            ->where('is_success', false)
            ->where('attempted_at', '>=', now()->subMinutes($minutes))
            ->count();
    }
}
