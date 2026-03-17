<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_login_methods', function (Blueprint $table) {
            $table->id();
            $table->boolean('method_password')->default(true);
            $table->boolean('method_otp_email')->default(false);
            $table->boolean('method_login_code')->default(false);
            $table->boolean('method_otp_mobile')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_login_methods');
    }
};
