<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cashier_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('cashier_name');
            $table->string('cash_drawer');
            $table->decimal('opening_balance', 10, 2)->default(0);
            $table->decimal('closing_balance', 10, 2)->nullable();
            $table->decimal('total_sales', 10, 2)->default(0);
            $table->integer('total_transactions')->default(0);
            $table->decimal('cash_payments', 10, 2)->default(0);
            $table->decimal('card_payments', 10, 2)->default(0);
            $table->decimal('transfer_payments', 10, 2)->default(0);
            $table->decimal('account_payments', 10, 2)->default(0);
            $table->string('pos_device')->nullable();
            $table->string('printer')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cashier_sessions');
    }
};
