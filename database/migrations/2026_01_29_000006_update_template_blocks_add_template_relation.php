<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('template_blocks', function (Blueprint $table) {
            $table->foreignId('print_template_id')->nullable()->constrained('print_templates')->onDelete('cascade');
            $table->string('type')->default('content'); // header, footer, content, sidebar
            $table->integer('position_x')->default(0);
            $table->integer('position_y')->default(0);
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->integer('order')->default(0);
        });
    }

    public function down()
    {
        Schema::table('template_blocks', function (Blueprint $table) {
            $table->dropForeign(['print_template_id']);
            $table->dropColumn(['print_template_id', 'type', 'position_x', 'position_y', 'width', 'height', 'is_visible', 'order']);
        });
    }
};
