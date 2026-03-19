<?php

declare(strict_types=1);

namespace App\Modules\Users\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UpdateUser
{
    public function handle(User $user, array $data): User
    {
        $updates = [
            'name'               => $data['name'],
            'email'              => $data['email'],
            'phone'              => $data['phone'] ?? null,
            'role_type'          => $data['role_type'] ?? 'staff',
            'role_label'         => ($data['role_type'] ?? 'staff') === 'other' ? ($data['role_label'] ?? null) : null,
            'restrict_access'    => $data['restrict_access'] ?? false,
            'can_login_app'      => $data['can_login_app'] ?? true,
            'show_login_status'  => $data['show_login_status'] ?? true,
            'allowed_access_times' => (! empty($data['time_restriction'])) ? ($user->allowed_access_times ?? []) : null,
        ];

        // Only update password if provided
        if (! empty($data['password'])) {
            $updates['password'] = $data['password'];
        }

        // Handle avatar upload
        if (! empty($data['avatar'])) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $updates['avatar'] = $data['avatar']->store('avatars', 'public');
        }

        $user->update($updates);

        return $user->fresh();
    }
}
