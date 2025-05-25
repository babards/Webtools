<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up(): void
    {
        // Step 1: Update values first — BEFORE changing the ENUM
        DB::table('pads')->where('padStatus', 'available')->update(['padStatus' => 'Available']);
        DB::table('pads')->where('padStatus', 'fullyoccupied')->update(['padStatus' => 'Fullyoccupied']);
        DB::table('pads')->where('padStatus', 'maintenance')->update(['padStatus' => 'Maintenance']);

        // Step 2: Then update the ENUM to only contain capitalized values
        DB::statement("ALTER TABLE pads MODIFY COLUMN padStatus ENUM('Available', 'Fullyoccupied', 'Maintenance') NOT NULL");
    }

    public function down(): void
    {
        // Step 1: Revert capitalized values back to lowercase
        DB::table('pads')->where('padStatus', 'Available')->update(['padStatus' => 'available']);
        DB::table('pads')->where('padStatus', 'Fullyoccupied')->update(['padStatus' => 'fullyoccupied']);
        DB::table('pads')->where('padStatus', 'Maintenance')->update(['padStatus' => 'maintenance']);

        // Step 2: Revert ENUM
        DB::statement("ALTER TABLE pads MODIFY COLUMN padStatus ENUM('available', 'fullyoccupied', 'maintenance') NOT NULL");
    }

};