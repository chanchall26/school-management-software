<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_2fa_configs', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->unique();
            $table->boolean('enabled')->default(false);
            $table->enum('method', ['mobile_otp', 'email_otp', 'static_code'])->nullable();

            // Mobile OTP: send to user's registered phone, or a fixed admin-set number
            $table->enum('mobile_target', ['user_registered', 'fixed'])->default('user_registered');
            $table->string('fixed_mobile', 20)->nullable();

            // Email OTP: send to user's email, or a fixed admin-set email
            $table->enum('email_target', ['user_registered', 'fixed'])->default('user_registered');
            $table->string('fixed_email')->nullable();

            // Static code: admin sets once, shared with users
            $table->string('static_code', 100)->nullable();

            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_2fa_configs');
    }
};
