<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

/**
 * LogoSeeder — safely updates ONLY the logo path for each tenant.
 *
 * Run this anytime you add or change a school logo:
 *   php artisan db:seed --class=LogoSeeder
 *
 * It never touches users, modules, passwords, or sessions.
 *
 * ─── HOW TO ADD A LOGO FOR A SCHOOL ─────────────────────────────
 *
 *  1. Name your logo file:  school_1.png  (PNG recommended, max 512×512px)
 *
 *  2. Drop the file at:
 *       simption/public/storage/logos/{tenant_id}/school_1.png
 *
 *     Examples:
 *       simption/public/storage/logos/dps/school_1.png
 *       simption/public/storage/logos/ryan/school_1.png
 *       simption/public/storage/logos/delhi/school_1.png
 *
 *  3. Run:  php artisan db:seed --class=LogoSeeder
 *
 *  4. Reload the login page — logo appears instantly.
 *
 * ─── TO REMOVE A LOGO ────────────────────────────────────────────
 *  Set the logo value to null below, then re-run the seeder.
 *
 * ─────────────────────────────────────────────────────────────────
 */
class LogoSeeder extends Seeder
{
    /**
     * Map of tenant_id → logo path (relative to public/storage/).
     * Set to null to remove the logo (shows initial avatar instead).
     */
    private array $logos = [
        'dps'   => 'logos/dps/school_1.png',
        'ryan'  => 'logos/ryan/school_1.png',
        'delhi' => 'logos/delhi/school_1.png',
    ];

    public function run(): void
    {
        foreach ($this->logos as $tenantId => $logoPath) {
            $tenant = Tenant::find($tenantId);

            if (! $tenant) {
                $this->command->warn("Tenant [{$tenantId}] not found — skipping.");
                continue;
            }

            // Stancl/Tenancy v3: set logo as a direct attribute.
            // Only logo is touched — modules, users, sessions untouched.
            $tenant->logo = $logoPath;
            $tenant->save();

            $status = $logoPath ? "→ {$logoPath}" : '→ removed (avatar fallback)';
            $this->command->info("  [{$tenantId}] logo updated {$status}");
        }

        $this->command->info('');
        $this->command->info('✓ All logos updated. Reload the login page to see changes.');
    }
}
