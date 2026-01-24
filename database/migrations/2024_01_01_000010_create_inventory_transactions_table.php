<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['purchase', 'sale', 'adjustment', 'waste', 'transfer']);
            $table->integer('quantity');
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->engine('InnoDB');
            $table->charset('utf8mb4');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
