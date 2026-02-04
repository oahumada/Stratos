<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('scenarios')) {
            Schema::create('scenarios', function (Blueprint $table) {
                $table->id();
                $table->foreignId('organization_id')->constrained()->onDelete('cascade');

                $table->string('name');
                $table->string('code', 50)->unique();
                $table->text('description')->nullable();
                $table->json('kpis')->nullable(); // KPIs de negocio esperados

                $table->date('start_date');
                $table->date('end_date');
                $table->integer('horizon_months');
                $table->year('fiscal_year');

                $table->enum('scope_type', ['organization_wide', 'business_unit', 'department', 'critical_roles_only']);
                $table->text('scope_notes')->nullable();

                $table->text('strategic_context')->nullable();
                $table->text('budget_constraints')->nullable();
                $table->text('legal_constraints')->nullable();
                $table->text('labor_relations_constraints')->nullable();

                $table->enum('status', ['draft', 'in_review', 'approved', 'active', 'completed', 'archived'])->default('draft');
                $table->timestamp('approved_at')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users');
                $table->jsonb('assumptions')->nullable(); // Supuestos estratégicos

                $table->foreignId('owner_user_id')->constrained('users');
                $table->foreignId('sponsor_user_id')->nullable()->constrained('users');

                $table->foreignId('created_by')->constrained('users');
                $table->foreignId('updated_by')->nullable()->constrained('users');

                $table->timestamps();

                $table->index(['organization_id', 'status'], 'idx_org_status');
                $table->index(['start_date', 'end_date'], 'idx_dates');

                $table->uuid('version_group_id')->after('id')->nullable();
                $table->index('version_group_id');
                $table->integer('version_number')->default(1)->after('version_group_id');
                $table->boolean('is_current_version')->default(true)->after('version_number');
                $table->index(['version_group_id', 'is_current_version']);
                $table->foreignId('parent_id')->nullable()->after('organization_id')
                    ->constrained('scenarios')->nullOnDelete();
                $table->index('parent_id');
                // Scope/Alcance
                $table->enum('f', ['organization', 'department', 'role_family'])
                    ->default('organization')->after('scenario_type');
                $table->bigInteger('scope_id')->nullable()->after('scope_type');
                // Metodología 7 pasos
                $table->integer('current_step')->default(1)->after('custom_config');
                $table->index('current_step');
                $table->enum('decision_status', ['draft', 'simulated', 'proposed', 'approved', 'archived', 'rejected'])
                    ->default('draft')->after('status');
                $table->index(['organization_id', 'decision_status']);
                $table->enum('execution_status', ['not_started', 'in_progress', 'paused', 'completed'])
                    ->default('not_started')->after('decision_status');
                $table->index('execution_status');
                // Auditoría mejorada
                $table->foreignId('owner_id')->nullable()->after('owner')
                    ->constrained('users')->nullOnDelete();
                $table->timestamp('last_simulated_at')->nullable()->after('approved_at');

            });
        }

      /*   if (!Schema::hasTable('workforce_plan_scope_units')) {
            Schema::create('workforce_plan_scope_units', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workforce_plan_id')->constrained('workforce_plans')->onDelete('cascade');

                $table->enum('unit_type', ['department', 'business_unit', 'location', 'cost_center', 'custom']);
                $table->unsignedBigInteger('unit_id')->nullable();
                $table->string('unit_name');

                $table->enum('inclusion_reason', ['critical', 'high_turnover', 'transformation', 'growth', 'downsizing', 'other']);
                $table->text('notes')->nullable();

                $table->timestamp('created_at')->useCurrent();

                $table->index('workforce_plan_id', 'idx_plan_scope_units');
            });
        }

        if (!Schema::hasTable('workforce_plan_scope_roles')) {
            Schema::create('workforce_plan_scope_roles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workforce_plan_id')->constrained('workforce_plans')->onDelete('cascade');
                $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');

                $table->enum('inclusion_reason', ['critical', 'hard_to_fill', 'transformation', 'high_risk', 'other']);
                $table->text('notes')->nullable();

                $table->timestamp('created_at')->useCurrent();

                $table->unique(['workforce_plan_id', 'role_id'], 'unique_plan_role');
                $table->index('workforce_plan_id', 'idx_plan_scope_roles');
            });
        }

        if (!Schema::hasTable('workforce_plan_transformation_projects')) {
            Schema::create('workforce_plan_transformation_projects', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workforce_plan_id')->constrained('workforce_plans')->onDelete('cascade');

                $table->string('project_name');
                $table->enum('project_type', ['digital_transformation', 'process_automation', 'growth', 'downsizing', 'merger_acquisition', 'restructuring', 'other']);

                $table->text('expected_impact')->nullable();
                $table->integer('estimated_fte_impact')->nullable();

                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();

                $table->timestamp('created_at')->useCurrent();

                $table->index('workforce_plan_id', 'idx_plan_transformation_projects');
            });
        }

        if (!Schema::hasTable('workforce_plan_talent_risks')) {
            Schema::create('workforce_plan_talent_risks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workforce_plan_id')->constrained('workforce_plans')->onDelete('cascade');

                $table->enum('risk_type', ['aging_workforce', 'high_turnover', 'scarce_skills', 'succession_gap', 'knowledge_loss', 'external_competition', 'other']);
                $table->text('risk_description');

                $table->unsignedBigInteger('affected_unit_id')->nullable();
                $table->foreignId('affected_role_id')->nullable()->constrained('roles')->onDelete('set null');

                $table->enum('severity', ['low', 'medium', 'high', 'critical']);
                $table->enum('likelihood', ['low', 'medium', 'high']);

                $table->text('mitigation_strategy')->nullable();

                $table->timestamps();

                $table->index(['workforce_plan_id', 'severity'], 'idx_plan_severity');
            });
        }

        if (!Schema::hasTable('workforce_plan_stakeholders')) {
            Schema::create('workforce_plan_stakeholders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workforce_plan_id')->constrained('workforce_plans')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

                $table->enum('role', ['sponsor', 'owner', 'contributor', 'reviewer', 'approver', 'informed']);
                $table->string('represents')->nullable();

                $table->timestamp('created_at')->useCurrent();

                $table->unique(['workforce_plan_id', 'user_id'], 'unique_plan_user');
                $table->index('workforce_plan_id', 'idx_plan_stakeholders');
            });
        }

        if (!Schema::hasTable('workforce_plan_documents')) {
            Schema::create('workforce_plan_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workforce_plan_id')->constrained('workforce_plans')->onDelete('cascade');

                $table->enum('document_type', ['strategic_plan', 'business_plan', 'budget', 'transformation_plan', 'other']);
                $table->string('document_name');
                $table->string('document_url', 500)->nullable();

                $table->foreignId('uploaded_by')->constrained('users');
                $table->timestamp('uploaded_at')->useCurrent();

                $table->index('workforce_plan_id', 'idx_plan_documents');
            });
        }
 */

    }

    public function down(): void
    {
    /*     Schema::dropIfExists('workforce_plan_documents');
        Schema::dropIfExists('workforce_plan_stakeholders');
        Schema::dropIfExists('workforce_plan_talent_risks');
        Schema::dropIfExists('workforce_plan_transformation_projects');
        Schema::dropIfExists('workforce_plan_scope_roles');
        Schema::dropIfExists('workforce_plan_scope_units'); */
        Schema::dropIfExists('scenarios');
    }
};
