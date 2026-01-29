<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('report_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_block_id')->constrained('template_blocks')->onDelete('cascade');
            $table->string('type'); // text, logo, image, table, barcode, line, etc.
            $table->string('name');
            $table->text('content')->nullable(); // text content, image path, etc.
            $table->integer('position_x')->default(0);
            $table->integer('position_y')->default(0);
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->json('properties')->nullable(); // additional properties like font-size, color, etc.
            $table->boolean('is_visible')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index(['template_block_id', 'type', 'is_visible']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_elements');
    }
};
