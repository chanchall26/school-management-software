<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('name');
            }
            if (! Schema::hasColumn('users', 'role_type')) {
                $table->enum('role_type', ['staff', 'teacher', 'other'])->default('staff')->after('avatar');
            }
            if (! Schema::hasColumn('users', 'restrict_access')) {
                $table->boolean('restrict_access')->default(false)->after('mfa_secret');
            }
            if (! Schema::hasColumn('users', 'can_login_app')) {
                $table->boolean('can_login_app')->default(true)->after('restrict_access');
            }
            if (! Schema::hasColumn('users', 'show_login_status')) {
                $table->boolean('show_login_status')->default(true)->after('can_login_app');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = ['avatar', 'role_type', 'restrict_access', 'can_login_app', 'show_login_status'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
