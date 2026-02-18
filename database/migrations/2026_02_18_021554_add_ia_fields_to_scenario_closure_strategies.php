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
        Schema::table('scenario_closure_strategies', function (Blueprint $table) {
            $table->float('ia_confidence_score')->nullable();
            $table->text('ia_strategy_rationale')->nullable();
            $table->boolean('is_ia_generated')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('scenario_closure_strategies', function (Blueprint $table) {
            $table->dropColumn(['ia_confidence_score', 'ia_strategy_rationale', 'is_ia_generated']);
        });
    }
};
