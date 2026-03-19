<?php

declare(strict_types=1);

namespace App\Actions\Panel;

use App\Support\ModuleRegistry;

class DisableModule
{
    /**
     * Disable a module for the current tenant.
     *
     * Removes module ID from tenant.data['modules'].
     * Does NOT drop tables (data preserved — re-enabling restores it).
     * Does NOT remove permissions (they become inactive, not deleted).
     */
    public function handle(string $moduleId): void
    {
        if (! ModuleRegistry::isEnabled($moduleId)) {
            return;
        }

        // Stancl Tenancy v3: non-custom columns are direct model attributes
        $tenant  = tenant();
        $modules = array_values(array_filter(
            (array) ($tenant->modules ?? []),
            fn ($id) => $id !== $moduleId,
        ));

        $tenant->modules = $modules;
        $tenant->save();
    }
}
