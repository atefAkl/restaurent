<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->enum('type', ['dine_in', 'takeaway', 'delivery', 'catering']);
            $table->string('phone', 20);
            $table->string('email', 100)->nullable();
            $table->string('s_number', 14)->nullable();
            $table->boolean('status')->nullable()->default(true);
            
            $table->foreignId('updated_by', 'users', 'id');
            $table->foreignId('created_by', 'users', 'id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
