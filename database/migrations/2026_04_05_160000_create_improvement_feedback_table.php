<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('improvement_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('agent_id')->nullable();
            $table->unsignedBigInteger('evaluation_id')->nullable();
            $table->unsignedBigInteger('intelligence_metric_id')->nullable();
            $table->tinyInteger('rating');
            $table->text('feedback_text')->nullable();
            $table->json('tags')->nullable();
            $table->json('context')->nullable();
            $table->enum('status', ['pending', 'processed', 'applied'])->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('evaluation_id')->references('id')->on('llm_evaluations')->nullOnDelete();
            $table->foreign('intelligence_metric_id')->references('id')->on('intelligence_metrics')->nullOnDelete();

            $table->index('organization_id');
            $table->index('agent_id');
            $table->index('rating');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('improvement_feedback');
    }
};
