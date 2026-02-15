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
        Schema::table('scenario_roles', function (Blueprint $table) {
            $table->integer('human_leverage')->nullable();
            $table->string('archetype', 1)->nullable(); // E, T, O
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scenario_roles', function (Blueprint $table) {
            $table->dropColumn(['human_leverage', 'archetype']);
        });
    }
};
