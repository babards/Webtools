<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Step 1: Add 'fullyoccupied' to the ENUM before using it
        DB::statement("ALTER TABLE pads MODIFY COLUMN padStatus ENUM('available', 'occupied', 'fullyoccupied', 'maintenance') NOT NULL");

        // Step 2: Update 'occupied' values to 'fullyoccupied'
        DB::table('pads')->where('padStatus', 'occupied')->update(['padStatus' => 'fullyoccupied']);

        // Step 3: Remove 'occupied' from ENUM (optional but clean)
        DB::statement("ALTER TABLE pads MODIFY COLUMN padStatus ENUM('available', 'fullyoccupied', 'maintenance') NOT NULL");
    }

    public function down(): void
    {
        // Step 1: Add back 'occupied' temporarily to revert data
        DB::statement("ALTER TABLE pads MODIFY COLUMN padStatus ENUM('available', 'occupied', 'fullyoccupied', 'maintenance') NOT NULL");

        // Step 2: Change back to 'occupied'
        DB::table('pads')->where('padStatus', 'fullyoccupied')->update(['padStatus' => 'occupied']);

        // Step 3: Remove 'fullyoccupied' from ENUM
        DB::statement("ALTER TABLE pads MODIFY COLUMN padStatus ENUM('available', 'occupied', 'maintenance') NOT NULL");
    }
};