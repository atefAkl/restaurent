<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('print_templates', function (Blueprint $table) {
            $table->foreignId('theme_id')->nullable()->after('type')->constrained('report_themes')->onDelete('set null');
            $table->text('description')->nullable()->after('name');
            $table->json('settings')->nullable()->after('content');
            $table->dropColumn('default');
            $table->boolean('is_default')->default(false)->after('is_active');
        });
    }

    public function down()
    {
        Schema::table('print_templates', function (Blueprint $table) {
            $table->dropForeign(['theme_id']);
            $table->dropColumn(['theme_id', 'description', 'settings', 'is_default']);
            $table->boolean('default')->default(false)->after('is_active');
        });
    }
};
