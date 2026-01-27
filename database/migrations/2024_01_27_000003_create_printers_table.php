<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('printers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['Thermal', 'Inkjet', 'Laser', 'Dot Matrix', 'POS']);
            $table->enum('connection_type', ['USB', 'Network', 'Bluetooth', 'Serial']);
            $table->string('ip_address')->nullable();
            $table->integer('port')->nullable();
            $table->enum('paper_type', ['thermal', 'regular', 'cashier']);
            $table->integer('paper_width')->default(80);
            $table->string('location');
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->enum('print_density', ['low', 'medium', 'high'])->default('medium');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_online')->default(false);
            $table->integer('total_prints')->default(0);
            $table->timestamp('last_used')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('printers');
    }
};
