<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('succession_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('scenario_id')->constrained('scenarios')->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->foreignId('target_role_id')->constrained('roles')->cascadeOnDelete();
            $table->float('skill_match_score')->default(0);
            $table->enum('readiness_level', ['junior', 'intermediate', 'senior', 'expert'])->default('junior');
            $table->integer('estimated_months_to_ready')->nullable();
            $table->json('gaps')->nullable();
            $table->enum('status', ['potential', 'active', 'ready', 'archived'])->default('potential');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['scenario_id', 'person_id', 'target_role_id'], 'unique_scenario_candidate_role');
            $table->index(['scenario_id', 'readiness_level']);
            $table->index(['scenario_id', 'status']);
            $table->index(['organization_id', 'person_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('succession_candidates');
    }
};
