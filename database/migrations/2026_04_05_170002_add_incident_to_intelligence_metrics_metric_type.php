<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'incident' to the metric_type enum on intelligence_metrics
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE intelligence_metrics DROP CONSTRAINT IF EXISTS intelligence_metrics_metric_type_check');
            DB::statement("ALTER TABLE intelligence_metrics ADD CONSTRAINT intelligence_metrics_metric_type_check CHECK (metric_type IN ('rag', 'llm_call', 'agent', 'evaluation', 'incident'))");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE intelligence_metrics DROP CONSTRAINT IF EXISTS intelligence_metrics_metric_type_check');
            DB::statement("ALTER TABLE intelligence_metrics ADD CONSTRAINT intelligence_metrics_metric_type_check CHECK (metric_type IN ('rag', 'llm_call', 'agent', 'evaluation'))");
        }
    }
};
