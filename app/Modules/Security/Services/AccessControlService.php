<?php

declare(strict_types=1);

namespace App\Modules\Security\Services;

use App\Models\User;
use App\Modules\Security\Models\UserAccessRule;

class AccessControlService
{
    public function canUserAccess(User $user, string $ip, ?string $category = null): array
    {
        if (! $user->is_active) {
            return ['allowed' => false, 'reason' => 'User account is inactive'];
        }

        $accessRule = UserAccessRule::where('user_id', $user->id)
            ->where('is_active', true)
            ->first();

        if (! $accessRule) {
            return ['allowed' => true, 'reason' => 'No restrictions applied'];
        }

        if (! $accessRule->isIpAllowed($ip)) {
            return ['allowed' => false, 'reason' => 'IP address not whitelisted'];
        }

        if (! $accessRule->isAccessAllowedAtTime()) {
            return ['allowed' => false, 'reason' => 'Access not allowed at this time'];
        }

        if ($category && ! $accessRule->canAccessCategory($category)) {
            return ['allowed' => false, 'reason' => "Access to category '{$category}' denied"];
        }

        return ['allowed' => true, 'reason' => 'Access granted'];
    }

    public function getCurrentSessionCount(User $user): int
    {
        return \App\Models\UserSession::where('user_id', $user->id)
            ->whereNull('logged_out_at')
            ->count();
    }

    public function canStartNewSession(User $user): bool
    {
        $accessRule = UserAccessRule::where('user_id', $user->id)
            ->where('is_active', true)
            ->first();

        if (! $accessRule) {
            return true;
        }

        if (! $accessRule->allow_multiple_sessions) {
            return $this->getCurrentSessionCount($user) === 0;
        }

        return $this->getCurrentSessionCount($user) < $accessRule->max_concurrent_sessions;
    }

    public function validateAccessByUserCategory(User $user, string $requiredCategory): bool
    {
        return ($user->category ?? 'standard') === $requiredCategory
            || ($user->category ?? 'standard') === 'admin';
    }

    public function getAccessibleCategories(User $user): array
    {
        $accessRule = UserAccessRule::where('user_id', $user->id)
            ->where('is_active', true)
            ->first();

        if (! $accessRule || empty($accessRule->allowed_categories)) {
            return ['standard'];
        }

        return (array) $accessRule->allowed_categories;
    }
}
