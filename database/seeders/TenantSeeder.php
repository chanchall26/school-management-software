<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = [
            [
                'id'          => 'dps',
                'name'        => 'DPS Indore',
                'domain'      => 'dps.localhost',
                'admin_email' => 'admin@dps.com',
                'modules'     => ['attendance', 'fees', 'exam', 'library'],
                // Drop logo file at: public/storage/logos/dps/school_1.png
                'logo'        => 'logos/dps/school_1.png',
            ],
            [
                'id'          => 'ryan',
                'name'        => 'Ryan International',
                'domain'      => 'ryan.localhost',
                'admin_email' => 'admin@ryan.com',
                'modules'     => ['attendance', 'fees'],
                // Drop logo file at: public/storage/logos/ryan/school_1.png
                'logo'        => 'logos/ryan/school_1.png',
            ],
            [
                'id'          => 'delhi',
                'name'        => 'Delhi Public School',
                'domain'      => 'delhi.localhost',
                'admin_email' => 'admin@delhi.com',
                'modules'     => ['attendance', 'fees', 'exam'],
                // Drop logo file at: public/storage/logos/delhi/school_1.png
                'logo'        => 'logos/delhi/school_1.png',
            ],
        ];

        foreach ($tenants as $t) {
            /** @var Tenant $tenant */
            // Stancl/Tenancy v3: pass non-custom-column attrs directly.
            // They are stored in the data JSON column automatically.
            $tenant = Tenant::updateOrCreate(
                ['id' => $t['id']],
                [
                    'name'    => $t['name'],
                    'logo'    => $t['logo'] ?? null,
                    'modules' => $t['modules'],
                ],
            );

            $tenant->domains()->delete();
            $tenant->domains()->create(['domain' => $t['domain']]);

            // Set ADMIN login methods (Company managed)
            DB::connection('central')->table('tenant_login_methods')->updateOrInsert(
                ['tenant_id' => $tenant->id],
                [
                    'method_password' => true,
                    'method_otp_email' => true,
                    'method_login_code' => true,
                    'method_otp_mobile' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $tenant->run(function () use ($t) {
                User::updateOrCreate(
                    ['email' => $t['admin_email']],
                    [
                        'name' => 'Admin',
                        'password' => Hash::make('Admin@1234'),
                        'email_verified_at' => Carbon::now(),
                        'is_active' => true,
                    ],
                );

                // Set STAFF login methods (School Admin managed)
                DB::table('staff_login_methods')->updateOrInsert(
                    ['id' => 1],
                    [
                        'method_password' => true,
                        'method_otp_email' => true,
                        'method_login_code' => true,
                        'method_otp_mobile' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            });
        }
    }
}
