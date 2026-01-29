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
        Schema::table('pos_devices', function (Blueprint $table) {
            $table->string('ip_address')->nullable()->change();
            $table->integer('port')->nullable()->change();
            $table->string('location')->nullable()->change();
            $table->string('manufacturer')->nullable()->change();
            $table->string('model')->nullable()->change();
            $table->text('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_devices', function (Blueprint $table) {
            $table->string('ip_address')->nullable(false)->change();
            $table->integer('port')->nullable(false)->change();
            $table->string('location')->nullable(false)->change();
            $table->string('manufacturer')->nullable(false)->change();
            $table->string('model')->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
        });
    }
};
