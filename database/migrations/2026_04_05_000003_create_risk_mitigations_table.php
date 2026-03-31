<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_mitigations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('risk_indicator_id')->constrained('talent_risk_indicators')->cascadeOnDelete();
            $table->enum('action_type', ['training', 'mentoring', 'promotion', 'retention_bonus', 'redeployment']);
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['planned', 'in_progress', 'completed', 'failed'])->default('planned');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['risk_indicator_id', 'status']);
            $table->index('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_mitigations');
    }
};
