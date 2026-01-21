<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('expense_number')->unique();
            $table->string('description_ar');
            $table->string('description_en')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('category', ['rent', 'utilities', 'salaries', 'supplies', 'maintenance', 'other'])->default('other');
            $table->date('expense_date');
            $table->string('receipt_image')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
