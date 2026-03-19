<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Every optional module (Attendance, Fees, Exam, Library …) must implement this.
 * Modules are enabled per-tenant via the `modules` key in the tenant data JSON.
 */
interface ModuleInterface
{
    // ── Identity ──────────────────────────────────────────────────────────────

    /** Unique slug used in tenant.data['modules'] JSON — e.g. "attendance" */
    public static function id(): string;

    /** Human-readable name shown in sidebar and marketplace — e.g. "Attendance" */
    public static function name(): string;

    /** Short description shown in the marketplace */
    public static function description(): string;

    /** Heroicon name (outline) for sidebar icon — e.g. "shield-check" */
    public static function icon(): string;

    /** Brand/accent color for this module — e.g. "#14B8A6" */
    public static function color(): string;

    /** Semantic version — e.g. "1.0.0" */
    public static function version(): string;

    // ── Navigation ────────────────────────────────────────────────────────────

    /** Sidebar section group label — e.g. "MODULES" */
    public static function navGroup(): string;

    /** Position within the group (lower = higher) */
    public static function navOrder(): int;

    // ── Integration ───────────────────────────────────────────────────────────

    /** Register module-specific routes (called when module is enabled for the tenant) */
    public static function routes(): void;

    /**
     * Livewire component class names to inject into the dashboard widget grid.
     * Return empty array if no dashboard widgets needed.
     *
     * @return class-string[]
     */
    public static function dashboardWidgets(): array;

    /**
     * Spatie permission slugs to seed when the module is enabled.
     *
     * @return string[]
     */
    public static function permissions(): array;

    /** Absolute path to the module's tenant DB migrations directory */
    public static function migrationsPath(): string;
}
