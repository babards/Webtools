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
        // First, update any existing records with 'occupied' status to 'fullyoccupied'
        DB::table('pads')
            ->where('padStatus', 'occupied')
            ->update(['padStatus' => 'fullyoccupied']);

        // Then modify the ENUM to replace 'occupied' with 'fullyoccupied'
        DB::statement("ALTER TABLE pads MODIFY COLUMN padStatus ENUM('available', 'fullyoccupied', 'maintenance') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, update any existing records with 'fullyoccupied' status back to 'occupied'
        DB::table('pads')
            ->where('padStatus', 'fullyoccupied')
            ->update(['padStatus' => 'occupied']);

        // Then modify the ENUM back to the original values
        DB::statement("ALTER TABLE pads MODIFY COLUMN padStatus ENUM('available', 'occupied', 'maintenance') NOT NULL");
    }
};
