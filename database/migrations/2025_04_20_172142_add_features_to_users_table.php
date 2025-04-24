<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable(); // Profile picture
            $table->boolean('is_verified')->default(false); // Email verification status
            $table->string('verification_token')->nullable(); // Email verification token
            $table->string('two_factor_code')->nullable(); // OTP code for 2FA
            $table->timestamp('two_factor_expires_at')->nullable(); // OTP expiration timestamp
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'is_verified', 'verification_token', 'two_factor_code', 'two_factor_expires_at']);
        });
    }
};
