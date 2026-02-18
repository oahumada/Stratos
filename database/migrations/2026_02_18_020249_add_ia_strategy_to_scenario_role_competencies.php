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
        Schema::table('scenario_role_competencies', function (Blueprint $table) {
            $table->string('suggested_strategy')->nullable();
            $table->text('strategy_rationale')->nullable();
            $table->float('ia_confidence_score')->nullable();
            $table->json('ia_action_plan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scenario_role_competencies', function (Blueprint $table) {
            $table->dropColumn(['suggested_strategy', 'strategy_rationale', 'ia_confidence_score', 'ia_action_plan']);
        });
    }
};
