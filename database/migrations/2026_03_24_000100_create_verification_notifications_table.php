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
        Schema::create('verification_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('type');  // 'phase_transition', 'alert_threshold', 'violation_detected'
            $table->json('data');    // Full notification payload
            $table->enum('severity', ['info', 'warning', 'critical'])->default('info');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Indexes for queries
            $table->index(['organization_id', 'type']);
            $table->index(['created_at']);
            $table->index(['read_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_notifications');
    }
};
