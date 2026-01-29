<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_components', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المكون
            $table->string('type'); // header, text, image, table, barcode, line, qr_code, etc
            $table->text('description')->nullable(); // وصف المكون
            $table->json('default_properties')->nullable(); // الخصائص الافتراضية
            $table->json('content_template')->nullable(); // قالب المحتوى مع المتغيرات
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false); // هل هو مكون نظام (لا يمكن حذفه)
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_components');
    }
};
