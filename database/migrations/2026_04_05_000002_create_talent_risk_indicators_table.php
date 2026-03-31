<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('talent_risk_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('scenario_id')->constrained('scenarios')->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->enum('risk_type', ['volatility', 'retention', 'skill_gap', 'engagement']);
            $table->float('risk_score')->default(0); // 0-100
            $table->timestamp('last_assessed_at')->nullable();
            $table->json('factors')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['scenario_id', 'person_id', 'risk_type'], 'unique_scenario_person_risk');
            $table->index(['scenario_id', 'risk_score']);
            $table->index(['organization_id', 'risk_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('talent_risk_indicators');
    }
};
