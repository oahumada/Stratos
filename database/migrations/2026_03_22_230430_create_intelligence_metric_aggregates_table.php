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
        Schema::create('intelligence_metric_aggregates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->index();
            $table->enum('metric_type', ['rag', 'llm_call', 'agent', 'evaluation'])->default('rag');
            $table->string('source_type')->nullable();
            $table->date('date_key')->index();
            $table->integer('total_count')->default(0);
            $table->integer('success_count')->default(0);
            $table->decimal('success_rate', 5, 4)->default(0);
            $table->integer('avg_duration_ms')->default(0);
            $table->integer('p50_duration_ms')->default(0);
            $table->integer('p95_duration_ms')->default(0);
            $table->integer('p99_duration_ms')->default(0);
            $table->decimal('avg_confidence', 5, 4)->default(0);
            $table->integer('avg_context_count')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'metric_type', 'date_key']);
            $table->unique(['organization_id', 'metric_type', 'source_type', 'date_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intelligence_metric_aggregates');
    }
};
