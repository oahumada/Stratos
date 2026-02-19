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
        Schema::table('bars_levels', function (Blueprint $table) {
            $table->text('learning_content')->nullable()->after('behavioral_description');
            $table->text('performance_indicator')->nullable()->after('learning_content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bars_levels', function (Blueprint $table) {
            $table->dropColumn(['learning_content', 'performance_indicator']);
        });
    }
};
