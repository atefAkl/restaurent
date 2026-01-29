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
        Schema::create('pos_station_device', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pos_station_id');
            $table->unsignedBigInteger('pos_device_id');
            $table->boolean('is_primary')->default(false); // الجهاز الأساسي للـ POS
            $table->timestamps();

            // Foreign keys
            $table->foreign('pos_station_id')->references('id')->on('pos_stations')->onDelete('cascade');
            $table->foreign('pos_device_id')->references('id')->on('pos_devices')->onDelete('cascade');

            // Unique constraint to prevent duplicate assignments
            $table->unique(['pos_station_id', 'pos_device_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_station_device');
    }
};
