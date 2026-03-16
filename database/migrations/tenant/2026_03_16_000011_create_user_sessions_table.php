<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_sessions')) {
            return;
        }

        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->string('session_id')->index();
            $table->string('method')->index(); // password | otp_email | code | otp_mobile
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('logged_in_at')->useCurrent();
            $table->timestamp('logged_out_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};

