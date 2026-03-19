<?php

declare(strict_types=1);

namespace App\Modules\Users\Actions;

use App\Models\User;

class ToggleUserStatus
{
    public function handle(int $userId): User
    {
        $user = User::findOrFail($userId);
        $user->update(['is_active' => ! $user->is_active]);

        return $user->fresh();
    }
}
