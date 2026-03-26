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
        Schema::create('executive_aggregates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->index();
            $table->unsignedBigInteger('scenario_id')->nullable();

            $table->decimal('avg_gap', 6, 3)->nullable();
            $table->decimal('avg_readiness', 6, 4)->nullable();
            $table->integer('ready_now')->default(0);

            $table->integer('headcount')->default(0);
            $table->integer('total_scenarios')->default(0);

            $table->integer('level_0_count')->default(0);
            $table->integer('level_1_count')->default(0);
            $table->integer('level_2_count')->default(0);
            $table->integer('level_3_count')->default(0);
            $table->integer('level_4_count')->default(0);
            $table->integer('level_5_count')->default(0);

            $table->integer('upskilled_count')->default(0);
            $table->integer('bot_strategies')->default(0);
            $table->integer('total_pivot_rows')->default(0);

            $table->integer('critical_gaps')->default(0);

            $table->integer('total_roles')->default(0);
            $table->integer('augmented_roles')->default(0);

            $table->decimal('avg_turnover_risk', 5, 2)->nullable();

            $table->timestamps();

            $table->unique(['organization_id', 'scenario_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('executive_aggregates');
    }
};
