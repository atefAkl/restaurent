<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pos_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['POS', 'Payment Terminal', 'Cash Drawer', 'Barcode Scanner']);
            $table->string('ip_address');
            $table->integer('port')->default(9100);
            $table->string('location');
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_online')->default(false);
            $table->integer('response_time')->nullable();
            $table->timestamp('last_connected')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pos_devices');
    }
};
