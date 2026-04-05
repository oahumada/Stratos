<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('embedding_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->unsignedBigInteger('embedding_id')->nullable();
            $table->enum('action', ['created', 'updated', 'deleted', 'flagged', 'unflagged']);
            $table->json('changes')->nullable();
            $table->string('triggered_by'); // 'system', 'reindex_job', 'user', 'feedback_loop'
            $table->timestamps();

            $table->index('organization_id');
            $table->index('embedding_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('embedding_audit_logs');
    }
};
