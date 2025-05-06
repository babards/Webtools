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
        Schema::create('pads', function (Blueprint $table) {
            $table->increments('padID');
            $table->unsignedBigInteger('userID'); // Landlord
            $table->string('padName');
            $table->text('padDescription')->nullable();
            $table->string('padLocation');
            $table->decimal('padRent', 10, 2);
            $table->string('padImage')->nullable();
            $table->enum('padStatus', ['available', 'occupied', 'maintenance'])->default('available');
            $table->timestamp('padCreatedAt')->useCurrent();
            $table->dateTime('padUpdatedAt')->nullable();
            $table->foreign('userID')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pads');
    }
};
