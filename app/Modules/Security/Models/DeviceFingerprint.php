<?php

declare(strict_types=1);

namespace App\Modules\Security\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceFingerprint extends Model
{
    protected $fillable = [
        'user_id',
        'fingerprint_hash',
        'device_name',
        'browser',
        'os',
        'ip_address',
        'is_trusted',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'is_trusted'   => 'bool',
            'last_used_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function findOrCreate(User $user, string $fingerprint, array $deviceInfo): self
    {
        return self::firstOrCreate(
            ['fingerprint_hash' => $fingerprint],
            [
                'user_id'     => $user->id,
                'device_name' => $deviceInfo['device_name'] ?? null,
                'browser'     => $deviceInfo['browser'] ?? null,
                'os'          => $deviceInfo['os'] ?? null,
                'ip_address'  => $deviceInfo['ip_address'] ?? null,
                'last_used_at' => now(),
            ]
        );
    }
}
