<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transformation_phases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('scenario_id')->constrained('scenarios')->cascadeOnDelete();
            $table->string('phase_name');
            $table->unsignedSmallInteger('phase_number');
            $table->unsignedSmallInteger('start_month')->default(0);
            $table->unsignedSmallInteger('duration_months')->default(6);
            $table->json('objectives')->nullable();
            $table->json('headcount_targets')->nullable();
            $table->json('key_milestones')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['scenario_id', 'phase_number']);
            $table->index('organization_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transformation_phases');
    }
};
