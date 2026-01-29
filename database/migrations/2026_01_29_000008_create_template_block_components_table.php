<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_block_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_block_id')->constrained('template_blocks')->onDelete('cascade');
            $table->foreignId('report_component_id')->constrained('report_components')->onDelete('cascade');
            $table->string('name'); // اسم المثيل في هذا الجزء
            $table->json('content')->nullable(); // المحتوى الفعلي (متغيرات أو ثابت)
            $table->json('properties')->nullable(); // خصائص الظهور (موقع، حجم، ألوان، خطوط)
            $table->boolean('is_visible')->default(true);
            $table->integer('order')->default(0); // الترتيب داخل الجزء
            $table->timestamps();
            
            $table->index(['template_block_id', 'order']);
            $table->index(['report_component_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_block_components');
    }
};
