<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('activitylog.table_name', 'activity_log');

        Schema::table($tableName, function (Blueprint $blueprint) use ($tableName) {
            if (! Schema::hasColumn($tableName, 'event')) {
                $blueprint->string('event')->nullable()->after('description');
            }
            if (! Schema::hasColumn($tableName, 'batch_uuid')) {
                $blueprint->uuid('batch_uuid')->nullable()->after('properties');
            }
        });
    }

    public function down(): void
    {
        $tableName = config('activitylog.table_name', 'activity_log');

        Schema::table($tableName, function (Blueprint $blueprint) use ($tableName) {
            $cols = [];
            if (Schema::hasColumn($tableName, 'event')) {
                $cols[] = 'event';
            }
            if (Schema::hasColumn($tableName, 'batch_uuid')) {
                $cols[] = 'batch_uuid';
            }
            if ($cols) {
                $blueprint->dropColumn($cols);
            }
        });
    }
};
