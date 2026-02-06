<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scenario_generations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->index();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->text('prompt')->nullable();
            $table->json('llm_response')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->decimal('confidence_score', 5, 4)->nullable();
            $table->enum('status', ['queued', 'processing', 'complete', 'failed'])->default('queued');
            $table->json('metadata')->nullable();
            $table->string('model_version')->nullable();
            $table->boolean('redacted')->default(false);
            $table->timestamps();

            $table->index(['organization_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scenario_generations');
    }
};
