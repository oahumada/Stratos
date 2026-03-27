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
        Schema::create('alert_thresholds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('metric'); // 'cpu_usage', 'memory_usage', 'queue_jobs', etc
            $table->decimal('threshold', 8, 2); // threshold value
            $table->enum('severity', ['critical', 'high', 'medium', 'low'])->default('high');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['organization_id', 'metric', 'severity'], 'unique_org_metric_severity');
            $table->index(['organization_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alert_thresholds');
    }
};
