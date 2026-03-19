<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Security module — adds security-related columns to the users table.
 * Uses hasColumn() guards so it is safe to run multiple times.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_locked')) {
                $table->boolean('is_locked')->default(false)->after('is_active');
            }
            if (! Schema::hasColumn('users', 'locked_until')) {
                $table->timestamp('locked_until')->nullable()->after('is_locked');
            }
            if (! Schema::hasColumn('users', 'failed_login_attempts')) {
                $table->integer('failed_login_attempts')->default(0)->after('locked_until');
            }
            if (! Schema::hasColumn('users', 'last_failed_attempt')) {
                $table->timestamp('last_failed_attempt')->nullable()->after('failed_login_attempts');
            }
            if (! Schema::hasColumn('users', 'category')) {
                $table->string('category')->default('standard')->after('last_failed_attempt');
            }
            if (! Schema::hasColumn('users', 'allowed_access_times')) {
                $table->json('allowed_access_times')->nullable()->after('category');
            }
            if (! Schema::hasColumn('users', 'require_mfa')) {
                $table->boolean('require_mfa')->default(false)->after('allowed_access_times');
            }
            if (! Schema::hasColumn('users', 'mfa_method')) {
                $table->string('mfa_method')->nullable()->after('require_mfa');
            }
            if (! Schema::hasColumn('users', 'mfa_secret')) {
                $table->text('mfa_secret')->nullable()->after('mfa_method');
            }
        });
    }

    public function down(): void
    {
        $cols = [
            'is_locked', 'locked_until', 'failed_login_attempts',
            'last_failed_attempt', 'category', 'allowed_access_times',
            'require_mfa', 'mfa_method', 'mfa_secret',
        ];

        Schema::table('users', function (Blueprint $table) use ($cols) {
            $existing = array_filter($cols, fn ($c) => Schema::hasColumn('users', $c));
            if ($existing) {
                $table->dropColumn(array_values($existing));
            }
        });
    }
};
