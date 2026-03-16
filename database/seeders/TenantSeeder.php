<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = [
            [
                'id' => 'dps',
                'name' => 'DPS Indore',
                'domain' => 'dps.localhost',
                'admin_email' => 'admin@dps.com',
                'modules' => ['attendance', 'fees', 'exam', 'library'],
            ],
            [
                'id' => 'ryan',
                'name' => 'Ryan International',
                'domain' => 'ryan.localhost',
                'admin_email' => 'admin@ryan.com',
                'modules' => ['attendance', 'fees'],
            ],
            [
                'id' => 'delhi',
                'name' => 'Delhi Public School',
                'domain' => 'delhi.localhost',
                'admin_email' => 'admin@delhi.com',
                'modules' => ['attendance', 'fees', 'exam'],
            ],
        ];

        foreach ($tenants as $t) {
            /** @var Tenant $tenant */
            $tenant = Tenant::updateOrCreate(
                ['id' => $t['id']],
                [
                    'name' => $t['name'],
                    'data' => [
                        'modules' => $t['modules'],
                        'login_methods' => ['otp', 'password', 'code', 'mobile'],
                    ],
                ],
            );

            $tenant->domains()->delete();
            $tenant->domains()->create(['domain' => $t['domain']]);

            $tenant->run(function () use ($t) {
                User::updateOrCreate(
                    ['email' => $t['admin_email']],
                    [
                        'name' => 'Admin',
                        'password' => Hash::make('Admin@1234'),
                        'email_verified_at' => Carbon::now(),
                    ],
                );
            });
        }
    }
}

