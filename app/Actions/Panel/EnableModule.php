<?php

declare(strict_types=1);

namespace App\Actions\Panel;

use App\Contracts\ModuleInterface;
use App\Support\ModuleRegistry;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;

class EnableModule
{
    /**
     * Enable a module for the current tenant.
     *
     * 1. Adds module ID to tenant.data['modules']
     * 2. Runs the module's migrations on the tenant DB
     * 3. Seeds the module's permissions
     */
    public function handle(string $moduleId): void
    {
        $catalog = ModuleRegistry::catalog();

        if (! $catalog->has($moduleId)) {
            throw new \InvalidArgumentException("Module [{$moduleId}] not found in catalog.");
        }

        if (ModuleRegistry::isEnabled($moduleId)) {
            return;
        }

        /** @var class-string<ModuleInterface> $moduleClass */
        $moduleClass = $catalog->get($moduleId);

        // 1. Add to tenant data in central DB
        // Stancl Tenancy v3: non-custom columns are direct model attributes, not data['key']
        $tenant  = tenant();
        $modules = array_values((array) ($tenant->modules ?? []));
        $modules[] = $moduleId;

        $installLog            = (array) ($tenant->modules_installed_at ?? []);
        $installLog[$moduleId] = now()->toISOString();

        $tenant->modules              = array_unique($modules);
        $tenant->modules_installed_at = $installLog;
        $tenant->save();

        // 2. Run module migrations on the tenant's DB
        $migrationsPath = $moduleClass::migrationsPath();
        if (is_dir($migrationsPath)) {
            Artisan::call('migrate', [
                '--path'     => str_replace(base_path() . DIRECTORY_SEPARATOR, '', $migrationsPath),
                '--database' => 'tenant',
                '--force'    => true,
            ]);
        }

        // 3. Seed module permissions
        foreach ($moduleClass::permissions() as $permission) {
            Permission::firstOrCreate([
                'name'       => $permission,
                'guard_name' => 'web',
            ]);
        }
    }
}
