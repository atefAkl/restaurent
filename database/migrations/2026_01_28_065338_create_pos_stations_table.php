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
        Schema::create('pos_stations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // POS 1, الكاشير الرئيسي
            $table->string('code')->unique(); // POS001, POS002
            $table->string('location'); // المطعم, الطابق الأول
            $table->unsignedBigInteger('computer_id')->nullable(); // جهاز الكمبيوتر
            $table->unsignedBigInteger('printer_id')->nullable(); // الطابعة
            $table->unsignedBigInteger('pos_device_id')->nullable(); // جهاز POS
            $table->unsignedBigInteger('cash_drawer_id')->nullable(); // الخزينة
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign keys (سنضيفها لما نعمل الجداول التانية)
            // $table->foreign('computer_id')->references('id')->on('computers');
            // $table->foreign('printer_id')->references('id')->on('printers');
            // $table->foreign('pos_device_id')->references('id')->on('pos_devices');
            // $table->foreign('cash_drawer_id')->references('id')->on('cash_drawers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_stations');
    }
};
