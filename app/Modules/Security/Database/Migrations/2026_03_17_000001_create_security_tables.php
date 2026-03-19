<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Security module — tenant DB migrations.
 * Creates all security-related tables in each tenant's database.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── security_settings ─────────────────────────────────────────────────
        if (! Schema::hasTable('security_settings')) {
            Schema::create('security_settings', function (Blueprint $table) {
                $table->id();
                $table->boolean('captcha_enabled')->default(true);
                $table->integer('failed_attempts_threshold')->default(5);
                $table->integer('captcha_trigger_attempts')->default(3);
                $table->integer('lockout_duration_minutes')->default(30);
                $table->boolean('session_timeout_enabled')->default(true);
                $table->integer('session_timeout_minutes')->default(60);
                $table->boolean('time_restriction_enabled')->default(false);
                $table->boolean('ip_whitelist_enabled')->default(false);
                $table->boolean('device_fingerprint_enabled')->default(false);
                $table->json('allowed_login_hours')->nullable();
                $table->timestamps();
            });
        }

        // ── login_attempts ────────────────────────────────────────────────────
        if (! Schema::hasTable('login_attempts')) {
            Schema::create('login_attempts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->string('email')->nullable();
                $table->string('ip_address');
                $table->string('user_agent')->nullable();
                $table->boolean('is_success')->default(false);
                $table->string('failure_reason')->nullable();
                $table->json('system_info')->nullable();
                $table->timestamp('attempted_at');
                $table->softDeletes();

                $table->index(['user_id', 'attempted_at']);
                $table->index(['ip_address', 'attempted_at']);
                $table->index(['email', 'attempted_at']);
            });
        }

        // ── user_access_rules ─────────────────────────────────────────────────
        if (! Schema::hasTable('user_access_rules')) {
            Schema::create('user_access_rules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('name');
                $table->text('description')->nullable();
                $table->json('allowed_ip_addresses')->nullable();
                $table->json('time_restrictions')->nullable();
                $table->json('allowed_categories')->nullable();
                $table->boolean('allow_multiple_sessions')->default(true);
                $table->integer('max_concurrent_sessions')->default(1);
                $table->boolean('require_mfa')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // ── device_fingerprints ───────────────────────────────────────────────
        if (! Schema::hasTable('device_fingerprints')) {
            Schema::create('device_fingerprints', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('fingerprint_hash')->unique();
                $table->string('device_name')->nullable();
                $table->string('browser')->nullable();
                $table->string('os')->nullable();
                $table->string('ip_address')->nullable();
                $table->boolean('is_trusted')->default(false);
                $table->timestamp('last_used_at')->nullable();
                $table->timestamps();
            });
        }

        // ── security_audit_logs ───────────────────────────────────────────────
        if (! Schema::hasTable('security_audit_logs')) {
            Schema::create('security_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->string('action');
                $table->string('resource_type')->nullable();
                $table->string('resource_id')->nullable();
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->boolean('success')->default(true);
                $table->text('remarks')->nullable();
                $table->timestamp('created_at');

                $table->index(['user_id', 'action', 'created_at']);
                $table->index(['resource_type', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('security_audit_logs');
        Schema::dropIfExists('device_fingerprints');
        Schema::dropIfExists('user_access_rules');
        Schema::dropIfExists('login_attempts');
        Schema::dropIfExists('security_settings');
    }
};
