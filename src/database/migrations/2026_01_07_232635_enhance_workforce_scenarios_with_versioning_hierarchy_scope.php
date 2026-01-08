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
            // Versionamiento
            if (!Schema::hasColumn('workforce_planning_scenarios', 'version_group_id')) {
                $table->uuid('version_group_id')->after('id')->nullable();
                $table->index('version_group_id');
            }
            if (!Schema::hasColumn('workforce_planning_scenarios', 'version_number')) {
                $table->integer('version_number')->default(1)->after('version_group_id');
            }
            if (!Schema::hasColumn('workforce_planning_scenarios', 'is_current_version')) {
                $table->boolean('is_current_version')->default(true)->after('version_number');
                $table->index(['version_group_id', 'is_current_version']);
            }

            // Jerarquía (padre-hijo)
            if (!Schema::hasColumn('workforce_planning_scenarios', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('organization_id')
                    ->constrained('workforce_planning_scenarios')->nullOnDelete();
                $table->index('parent_id');
            }

            // Scope/Alcance
            if (!Schema::hasColumn('workforce_planning_scenarios', 'scope_type')) {
                $table->enum('scope_type', ['organization', 'department', 'role_family'])
                    ->default('organization')->after('scenario_type');
            }
            if (!Schema::hasColumn('workforce_planning_scenarios', 'scope_id')) {
                $table->bigInteger('scope_id')->nullable()->after('scope_type');
                $table->index(['scope_type', 'scope_id']);
            }

            // Metodología 7 pasos
            if (!Schema::hasColumn('workforce_planning_scenarios', 'current_step')) {
                $table->integer('current_step')->default(1)->after('custom_config');
                $table->index('current_step');
            }

            // Estados duales (decision + execution)
            if (!Schema::hasColumn('workforce_planning_scenarios', 'decision_status')) {
                $table->enum('decision_status', ['draft', 'simulated', 'proposed', 'approved', 'archived', 'rejected'])
                    ->default('draft')->after('status');
                $table->index(['organization_id', 'decision_status']);
            }
            if (!Schema::hasColumn('workforce_planning_scenarios', 'execution_status')) {
                $table->enum('execution_status', ['not_started', 'in_progress', 'paused', 'completed'])
                    ->default('not_started')->after('decision_status');
                $table->index('execution_status');
            }

            // Auditoría mejorada
            if (!Schema::hasColumn('workforce_planning_scenarios', 'owner_id')) {
                $table->foreignId('owner_id')->nullable()->after('owner')
                    ->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('workforce_planning_scenarios', 'last_simulated_at')) {
                $table->timestamp('last_simulated_at')->nullable()->after('approved_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workforce_planning_scenarios', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['version_group_id', 'is_current_version']);
            $table->dropIndex(['organization_id', 'decision_status']);
            $table->dropIndex(['scope_type', 'scope_id']);
            
            // Drop foreign keys
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['owner_id']);
            
            // Drop columns
            $table->dropColumn([
                'version_group_id',
                'version_number',
                'is_current_version',
                'parent_id',
                'scope_type',
                'scope_id',
                'current_step',
                'decision_status',
                'execution_status',
                'owner_id',
                'last_simulated_at',
            ]);
        });
    }
};
