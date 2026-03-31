<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('development_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('succession_candidate_id')->constrained('succession_candidates')->cascadeOnDelete();
            $table->text('goal_description');
            $table->date('target_completion_date')->nullable();
            $table->json('activities')->nullable(); // [{activity, duration_hours, status}]
            $table->enum('status', ['active', 'paused', 'completed', 'archived'])->default('active');
            $table->unsignedSmallInteger('progress_pct')->default(0); // 0-100
            $table->timestamps();
            $table->softDeletes();

            $table->index(['succession_candidate_id', 'status']);
            $table->index('organization_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('development_plans');
    }
};
