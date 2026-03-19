<?php

declare(strict_types=1);

namespace App\Modules\Security\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityAuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'resource_type',
        'resource_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'success',
        'remarks',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'json',
            'new_values' => 'json',
            'success'    => 'bool',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(
        ?User $user,
        string $action,
        ?string $resourceType = null,
        ?string $resourceId = null,
        array $oldValues = [],
        array $newValues = [],
        bool $success = true,
        ?string $remarks = null
    ): self {
        return self::create([
            'user_id'       => $user?->id,
            'action'        => $action,
            'resource_type' => $resourceType,
            'resource_id'   => $resourceId,
            'old_values'    => $oldValues ?: null,
            'new_values'    => $newValues ?: null,
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
            'success'       => $success,
            'remarks'       => $remarks,
            'created_at'    => now(),
        ]);
    }
}
