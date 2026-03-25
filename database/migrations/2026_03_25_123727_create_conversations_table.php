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
        Schema::create('conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('organization_id');
            $table->string('title');
            
            // Contextual linking
            $table->enum('context_type', ['none', 'learning_assignment', 'performance_review', 'incident', 'survey', 'onboarding'])->default('none');
            $table->string('context_id')->nullable();
            
            // Metadata
            $table->boolean('is_active')->default(true);
            $table->integer('participant_count')->default(0);
            $table->timestamp('last_message_at')->nullable();
            
            // Audit
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('people')->onDelete('cascade');
            $table->index(['organization_id', 'is_active']);
            $table->index(['organization_id', 'last_message_at']);
            $table->index(['context_type', 'context_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
