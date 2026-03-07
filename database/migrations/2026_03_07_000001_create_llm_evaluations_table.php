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
        Schema::create('llm_evaluations', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');

            // Source of evaluation
            $table->string('evaluable_type')->default('ScenarioGeneration'); // Polymorphic
            $table->unsignedBigInteger('evaluable_id');
            $table->index(['evaluable_type', 'evaluable_id']);

            // LLM Provider information
            $table->string('llm_provider')->default('mock');  // deepseek, abacus, openai, intel, mock
            $table->string('llm_model')->nullable();          // Specific model version
            $table->string('llm_version')->nullable();        // Provider version

            // Content being evaluated
            $table->longText('input_content');                // Original prompt/input
            $table->longText('output_content');               // LLM output
            $table->longText('context_content')->nullable();  // Reference/context for evaluation

            // RAGAS Individual Metrics (0-1 scale)
            $table->decimal('faithfulness_score', 3, 2)->nullable();       // Consistency with source
            $table->decimal('relevance_score', 3, 2)->nullable();          // Relevance to query
            $table->decimal('context_alignment_score', 3, 2)->nullable();  // Alignment with context
            $table->decimal('coherence_score', 3, 2)->nullable();          // Structural coherence
            $table->decimal('hallucination_rate', 3, 2)->nullable();       // 0-1: rate of hallucinations

            // Composite Score
            $table->decimal('composite_score', 3, 2)->nullable();          // Weighted average (0-1)
            $table->enum('quality_level', ['excellent', 'good', 'acceptable', 'poor', 'critical'])->nullable();

            // Normalized Score (adjusted by provider baseline)
            $table->decimal('normalized_score', 3, 2)->nullable();         // Adjusted by baseline

            // Evaluation metadata
            $table->json('metric_details')->nullable();                    // Detailed per-metric info
            $table->json('issues_detected')->nullable();                   // Identified problems
            $table->json('recommendations')->nullable();                   // Suggestions for improvement

            // Evaluation status
            $table->enum('status', ['pending', 'evaluating', 'completed', 'failed', 'retrying'])->default('pending');
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Audit trail
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('evaluation_config')->nullable();                 // Config used for evaluation

            // Performance metrics
            $table->integer('processing_ms')->nullable();                  // Evaluation time
            $table->integer('tokens_used')->nullable();                    // If applicable

            // Historical tracking
            $table->boolean('is_latest')->default(true);                   // Latest evaluation for this content
            $table->unsignedBigInteger('superseded_by_id')->nullable();    // If re-evaluated

            $table->timestamps();
            $table->softDeletes();

            // Indices for common queries
            $table->index('organization_id');
            $table->index('llm_provider');
            $table->index('status');
            $table->index('created_at');
            $table->index('composite_score');
            $table->index('quality_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('llm_evaluations');
    }
};
