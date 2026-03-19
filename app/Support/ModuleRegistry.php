<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Central registry of all modules known to the codebase.
 *
 * To add a new module:
 *  1. Build your module class implementing App\Contracts\ModuleInterface
 *  2. Add it to $catalog below — key = module ID, value = FQCN
 *  3. Enable it for a tenant by adding the ID to tenant.data['modules'] JSON
 *
 * No code deploy needed to enable/disable modules per school — it's DB-driven.
 */
class ModuleRegistry
{
    /**
     * ALL modules known to the codebase.
     * Modules listed here but NOT in tenant.data['modules'] are invisible.
     */
    protected static array $catalog = [
        'users'      => \App\Modules\Users\UsersModule::class,
        'security'   => \App\Modules\Security\SecurityModule::class,
        // 'attendance' => \App\Modules\Attendance\AttendanceModule::class,
        // 'fees'       => \App\Modules\Fees\FeesModule::class,
        // 'exam'       => \App\Modules\Exam\ExamModule::class,
        // 'library'    => \App\Modules\Library\LibraryModule::class,
        // 'transport'  => \App\Modules\Transport\TransportModule::class,
        // 'timetable'  => \App\Modules\Timetable\TimetableModule::class,
    ];

    /** Only the modules this tenant has ENABLED (purchased + activated) */
    public static function enabled(): Collection
    {
        if (! tenancy()->initialized) {
            return collect();
        }

        $enabled = (array) (tenant('modules') ?? []);

        return collect(static::$catalog)
            ->filter(fn ($class, $id) => in_array($id, $enabled));
    }

    /** All modules in catalog — for the Module Marketplace page */
    public static function catalog(): Collection
    {
        return collect(static::$catalog);
    }

    /** Check if a specific module is enabled for the current tenant */
    public static function isEnabled(string $id): bool
    {
        if (! tenancy()->initialized) {
            return false;
        }

        return in_array($id, (array) (tenant('modules') ?? []));
    }
}
