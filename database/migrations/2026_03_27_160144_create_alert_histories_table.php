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
        Schema::create('alert_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('alert_threshold_id')->constrained('alert_thresholds')->cascadeOnDelete();
            $table->timestamp('triggered_at');
            $table->timestamp('acknowledged_at')->nullable();
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('severity', ['critical', 'high', 'medium', 'low'])->default('high');
            $table->enum('status', ['triggered', 'acknowledged', 'resolved', 'muted'])->default('triggered');
            $table->decimal('metric_value', 10, 2)->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('muted_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'triggered_at']);
            $table->index(['alert_threshold_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alert_histories');
    }
};
