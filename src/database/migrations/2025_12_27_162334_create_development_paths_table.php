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
        Schema::create('development_paths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained();
            $table->foreignId('evaluation_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('people_id')->constrained()->cascadeOnDelete();
            $table->string('action_title');
            $table->text('description')->nullable();
            $table->integer('estimated_weeks')->nullable();
            $table->decimal('estimated_impact', 5, 2)->nullable(); // % de mejora esperada
            $table->foreignId('target_role_id')->constrained('roles')->cascadeOnDelete();
            $table->enum('status', ['draft', 'suggested', 'in_progress', 'active', 'completed', 'cancelled'])->default('draft');
            $table->unsignedSmallInteger('estimated_duration_months')->default(6);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('steps')->nullable();
            $table->index(['people_id', 'status']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('development_paths');
    }
};
