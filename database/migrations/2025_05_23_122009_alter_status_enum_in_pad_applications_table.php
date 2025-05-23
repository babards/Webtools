<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE pad_applications MODIFY status ENUM('pending', 'approved', 'rejected', 'cancelled', 'kicked') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE pad_applications MODIFY status ENUM('pending', 'approved', 'rejected', 'cancelled') NOT NULL");
    }
};
