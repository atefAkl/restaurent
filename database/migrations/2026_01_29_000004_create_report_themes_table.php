<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('report_themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('styles'); // CSS styles as JSON
            $table->timestamps();
            
            $table->index(['is_active', 'is_default']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_themes');
    }
};
