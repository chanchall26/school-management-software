<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('captcha_enabled')->default(true);
            $table->integer('failed_attempts_threshold')->default(5);
            $table->integer('captcha_trigger_attempts')->default(3);
            $table->integer('lockout_duration_minutes')->default(30);
            $table->boolean('session_timeout_enabled')->default(true);
            $table->integer('session_timeout_minutes')->default(60);
            $table->boolean('time_restriction_enabled')->default(false);
            $table->json('allowed_login_hours')->nullable();
            $table->boolean('ip_whitelist_enabled')->default(false);
            $table->boolean('device_fingerprint_enabled')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_settings');
    }
};
