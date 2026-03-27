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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // 'created', 'updated', 'deleted'
            $table->string('entity_type'); // 'AlertThreshold', 'AlertHistory', etc.
            $table->string('entity_id'); // ID of the entity that changed
            $table->json('changes')->nullable(); // { field => [old_value, new_value] }
            $table->json('metadata')->nullable(); // Additional context (IP, UA, etc)
            $table->string('triggered_by')->default('user'); // 'user', 'system', 'api'
            $table->timestamps();

            // Indexes for performance
            $table->index(['organization_id', 'created_at']);
            $table->index(['entity_type', 'entity_id']);
            $table->index(['action']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
