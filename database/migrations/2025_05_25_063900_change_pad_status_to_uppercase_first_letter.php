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
        // Update existing records to uppercase first letter
        DB::table('pads')->where('padStatus', 'available')->update(['padStatus' => 'Available']);
        DB::table('pads')->where('padStatus', 'fullyoccupied')->update(['padStatus' => 'Fullyoccupied']);
        DB::table('pads')->where('padStatus', 'maintenance')->update(['padStatus' => 'Maintenance']);

        // Update the ENUM to use uppercase first letter
        DB::statement("ALTER TABLE pads MODIFY COLUMN padStatus ENUM('Available', 'Fullyoccupied', 'Maintenance') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert records back to lowercase
        DB::table('pads')->where('padStatus', 'Available')->update(['padStatus' => 'available']);
        DB::table('pads')->where('padStatus', 'Fullyoccupied')->update(['padStatus' => 'fullyoccupied']);
        DB::table('pads')->where('padStatus', 'Maintenance')->update(['padStatus' => 'maintenance']);

        // Revert ENUM back to lowercase
        DB::statement("ALTER TABLE pads MODIFY COLUMN padStatus ENUM('available', 'fullyoccupied', 'maintenance') NOT NULL");
    }
};
