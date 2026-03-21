<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('phone');
            }

            if (! Schema::hasColumn('users', 'role_type')) {
                $table->string('role_type', 50)->default('staff')->index()->after('avatar');
            }

            if (! Schema::hasColumn('users', 'role_label')) {
                $table->string('role_label')->nullable()->after('role_type');
            }

            if (! Schema::hasColumn('users', 'restrict_access')) {
                $table->boolean('restrict_access')->default(false)->after('role_label');
            }

            if (! Schema::hasColumn('users', 'can_login_app')) {
                $table->boolean('can_login_app')->default(true)->after('restrict_access');
            }

            if (! Schema::hasColumn('users', 'show_login_status')) {
                $table->boolean('show_login_status')->default(true)->after('can_login_app');
            }

            if (! Schema::hasColumn('users', 'allowed_access_times')) {
                $table->json('allowed_access_times')->nullable()->after('show_login_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = ['allowed_access_times', 'show_login_status', 'can_login_app',
                     'restrict_access', 'role_label', 'role_type', 'avatar'];

            foreach ($cols as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
