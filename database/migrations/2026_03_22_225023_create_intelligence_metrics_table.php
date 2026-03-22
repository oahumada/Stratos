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
        Schema::create('intelligence_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->index();
            $table->enum('metric_type', ['rag', 'llm_call', 'agent', 'evaluation'])->default('rag');
            $table->string('source_type')->nullable()->index();
            $table->integer('context_count')->default(0);
            $table->decimal('confidence', 5, 4)->nullable();
            $table->integer('duration_ms')->default(0);
            $table->boolean('success')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'metric_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intelligence_metrics');
    }
};
