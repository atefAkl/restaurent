<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('address_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['dine_in', 'takeaway', 'delivery', 'catering', 'subscription'])->default('dine_in');
            $table->enum('status', ['in_progress', 'pending', 'preparing', 'ready', 'completed', 'cancelled'])->default('pending');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2)->default(0);
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'on_account', 'subscription', 'mixed'])->default('cash');
            $table->string('payment_reference')->nullable(); // For machine name or bank account id
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->engine('InnoDB');
            $table->charset('utf8mb4');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
