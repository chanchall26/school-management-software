<?php

declare(strict_types=1);

namespace App\Modules\Users\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class CreateUser
{
    public function handle(array $data): User
    {
        $avatarPath = null;

        if (! empty($data['avatar'])) {
            $avatarPath = $data['avatar']->store('avatars', 'public');
        }

        return User::create([
            'name'               => $data['name'],
            'email'              => $data['email'],
            'phone'              => $data['phone'] ?? null,
            'password'           => $data['password'],
            'login_code'         => User::generateLoginCode(),
            'is_active'          => true,
            'avatar'             => $avatarPath,
            'role_type'          => $data['role_type'] ?? 'staff',
            'role_label'         => ($data['role_type'] ?? 'staff') === 'other' ? ($data['role_label'] ?? null) : null,
            'restrict_access'    => $data['restrict_access'] ?? false,
            'can_login_app'      => $data['can_login_app'] ?? true,
            'show_login_status'  => $data['show_login_status'] ?? true,
            'allowed_access_times' => (! empty($data['time_restriction'])) ? [] : null,
        ]);
    }
}
