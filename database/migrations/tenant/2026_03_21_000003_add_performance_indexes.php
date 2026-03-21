<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add composite indexes for common query patterns:
 *  - SecurityCenter counts: is_success + created_at
 *  - Login audit search: identifier + is_success + created_at
 *  - Locked user lookup: is_locked + locked_until
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('login_activities', function (Blueprint $table) {
            // SecurityCenter: WHERE is_success=0 AND created_at >= X
            $table->index(['is_success', 'created_at'], 'la_success_time_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            // SecurityCenter: WHERE is_locked=1 AND (locked_until IS NULL OR locked_until > now())
            $table->index(['is_locked', 'locked_until'], 'users_locked_until_idx');
        });
    }

    public function down(): void
    {
        Schema::table('login_activities', function (Blueprint $table) {
            $table->dropIndex('la_success_time_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_locked_until_idx');
        });
    }
};
