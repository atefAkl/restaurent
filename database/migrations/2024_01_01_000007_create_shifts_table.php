<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('shift_number')->unique();
            $table->decimal('opening_balance', 10, 2)->default(0);
            $table->decimal('closing_balance', 10, 2)->nullable();
            $table->decimal('cash_sales', 10, 2)->default(0);
            $table->decimal('visa_sales', 10, 2)->default(0);
            $table->decimal('total_sales', 10, 2)->default(0);
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();

            $table->engine('InnoDB');
            $table->charset('utf8mb4');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
