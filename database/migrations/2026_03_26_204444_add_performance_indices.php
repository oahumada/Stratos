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
        // Add indices for business_metrics table
        Schema::table('business_metrics', function (Blueprint $table) {
            // Tenant isolation index (most queries filter by organization_id)
            $table->index(['organization_id'], 'idx_business_metrics_org_id');

            // Query by metric_name (used in cache invalidation lookups)
            $table->index(['metric_name'], 'idx_business_metrics_metric_name');

            // Composite index for common query pattern: org_id + metric_name
            $table->index(['organization_id', 'metric_name'], 'idx_business_metrics_org_metric');

            // Temporal index for ordered queries (reporting periods)
            $table->index(['created_at'], 'idx_business_metrics_created_at');

            // Multi-column index for filtered + sorted queries
            $table->index(['organization_id', 'created_at'], 'idx_business_metrics_org_created');
        });

        // Add indices for financial_indicators table
        Schema::table('financial_indicators', function (Blueprint $table) {
            // Tenant isolation index
            $table->index(['organization_id'], 'idx_financial_indicators_org_id');

            // Query by indicator_type (used in cache lookups)
            $table->index(['indicator_type'], 'idx_financial_indicators_indicator_type');

            // Composite index for common query pattern: org_id + indicator_type
            $table->index(['organization_id', 'indicator_type'], 'idx_financial_indicators_org_type');

            // Temporal index for benchmarking
            $table->index(['created_at'], 'idx_financial_indicators_created_at');

            // Multi-column for filtered + sorted queries
            $table->index(['organization_id', 'created_at'], 'idx_financial_indicators_org_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_metrics', function (Blueprint $table) {
            $table->dropIndex('idx_business_metrics_org_id');
            $table->dropIndex('idx_business_metrics_metric_name');
            $table->dropIndex('idx_business_metrics_org_metric');
            $table->dropIndex('idx_business_metrics_created_at');
            $table->dropIndex('idx_business_metrics_org_created');
        });

        Schema::table('financial_indicators', function (Blueprint $table) {
            $table->dropIndex('idx_financial_indicators_org_id');
            $table->dropIndex('idx_financial_indicators_indicator_type');
            $table->dropIndex('idx_financial_indicators_org_type');
            $table->dropIndex('idx_financial_indicators_created_at');
            $table->dropIndex('idx_financial_indicators_org_created');
        });
    }
};
