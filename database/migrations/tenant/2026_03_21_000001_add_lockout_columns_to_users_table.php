<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('is_active')->index();
            $table->timestamp('locked_until')->nullable()->after('is_locked');
            $table->integer('failed_login_attempts')->default(0)->after('locked_until');
            $table->timestamp('last_failed_attempt')->nullable()->after('failed_login_attempts');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_locked', 'locked_until', 'failed_login_attempts', 'last_failed_attempt']);
        });
    }
};
