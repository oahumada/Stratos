<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verification_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('audit_type')->nullable();            // 'verification', 'phase_transition', etc.
            $table->string('current_phase')->nullable();          // 'silent', 'flagging', 'blocking'
            $table->string('status')->nullable();                 // 'completed', 'error', 'warning', 'passed', 'failed'
            $table->float('confidence_score')->nullable();
            $table->float('error_rate')->nullable();
            $table->float('retry_rate')->nullable();
            $table->integer('sample_size')->nullable();
            $table->float('throughput')->nullable();
            $table->float('latency')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'created_at']);
            $table->index(['organization_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_audits');
    }
};
