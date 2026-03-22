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
        Schema::create('agent_interactions', function (Blueprint $table) {
            $table->id();
            $table->string('agent_name'); // e.g., "Stratos Impact Cortex"
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('prompt_hash')->index(); // SHA256 hash
            $table->unsignedInteger('latency_ms'); // Milliseconds
            $table->unsignedInteger('token_count')->nullable();
            $table->enum('status', ['success', 'error'])->default('success');
            $table->text('error_message')->nullable();
            $table->unsignedInteger('input_length')->nullable();
            $table->unsignedInteger('output_length')->nullable();
            $table->string('provider')->nullable(); // deepseek, openai, etc
            $table->string('model')->nullable(); // model name/version
            $table->string('context')->nullable(); // API endpoint, job name, etc
            $table->timestamps();

            // Indexes for efficient queries
            $table->index(['organization_id', 'created_at']);
            $table->index(['agent_name', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_interactions');
    }
};
