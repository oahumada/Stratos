<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        if (! Schema::hasTable('scenarios')) {
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

    }

    public function down(): void
    {
        Schema::dropIfExists('scenarios');
    }

};
