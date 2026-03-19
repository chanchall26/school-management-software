<?php

declare(strict_types=1);

namespace App\Modules\Security\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAccessRule extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'allowed_ip_addresses',
        'time_restrictions',
        'allowed_categories',
        'allow_multiple_sessions',
        'max_concurrent_sessions',
        'require_mfa',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'allowed_ip_addresses'   => 'json',
            'time_restrictions'      => 'json',
            'allowed_categories'     => 'json',
            'allow_multiple_sessions' => 'bool',
            'require_mfa'            => 'bool',
            'is_active'              => 'bool',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isIpAllowed(string $ip): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if (empty($this->allowed_ip_addresses)) {
            return true;
        }

        return in_array($ip, (array) $this->allowed_ip_addresses);
    }

    public function isAccessAllowedAtTime(\DateTime $time = null): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if (empty($this->time_restrictions)) {
            return true;
        }

        $time        = $time ?? now();
        $dayName     = strtolower($time->format('l'));
        $restrictions = (array) $this->time_restrictions;

        if (! isset($restrictions[$dayName])) {
            return false;
        }

        $dayRestriction = $restrictions[$dayName];
        $startTime      = \DateTime::createFromFormat('H:i', $dayRestriction['start']);
        $endTime        = \DateTime::createFromFormat('H:i', $dayRestriction['end']);

        return $time >= $startTime && $time <= $endTime;
    }

    public function canAccessCategory(string $category): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if (empty($this->allowed_categories)) {
            return true;
        }

        return in_array($category, (array) $this->allowed_categories);
    }
}
