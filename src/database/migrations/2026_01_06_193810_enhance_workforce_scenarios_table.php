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
        Schema::table('workforce_planning_scenarios', function (Blueprint $table) {
            // Verificar y agregar solo si no existen
            if (!Schema::hasColumn('workforce_planning_scenarios', 'template_id')) {
                $table->foreignId('template_id')->nullable()->after('organization_id')->constrained('scenario_templates')->nullOnDelete();
            }
            if (!Schema::hasColumn('workforce_planning_scenarios', 'scenario_type')) {
                $table->string('scenario_type')->default('custom')->after('name');
            }
            if (!Schema::hasColumn('workforce_planning_scenarios', 'target_date')) {
                $table->date('target_date')->nullable()->after('horizon_months');
            }
            if (!Schema::hasColumn('workforce_planning_scenarios', 'time_horizon_weeks')) {
                $table->integer('time_horizon_weeks')->nullable()->after('target_date');
            }
            if (!Schema::hasColumn('workforce_planning_scenarios', 'assumptions')) {
                $table->json('assumptions')->nullable()->after('description');
            }
            if (!Schema::hasColumn('workforce_planning_scenarios', 'custom_config')) {
                $table->json('custom_config')->nullable()->after('assumptions');
            }
            if (!Schema::hasColumn('workforce_planning_scenarios', 'estimated_budget')) {
                $table->decimal('estimated_budget', 15, 2)->nullable()->after('custom_config');
            }
            if (!Schema::hasColumn('workforce_planning_scenarios', 'owner')) {
                $table->string('owner')->nullable()->after('created_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('workforce_planning_scenarios', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropColumn([
                'template_id',
                'scenario_type',
                'target_date',
                'time_horizon_weeks',
                'assumptions',
                'custom_config',
                'estimated_budget',
                'owner',
            ]);
        });
    }
};
