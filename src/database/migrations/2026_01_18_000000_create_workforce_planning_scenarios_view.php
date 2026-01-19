<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Create a lightweight compatibility table and triggers to mirror 'scenarios'
        DB::statement(<<<'SQL'
            -- Compatibility table mirroring 'scenarios' schema used by legacy code/tests
            CREATE TABLE IF NOT EXISTS workforce_planning_scenarios (
                id INTEGER PRIMARY KEY,
                organization_id INTEGER,
                name TEXT,
                code TEXT,
                description TEXT,
                kpis TEXT,
                start_date DATETIME,
                end_date DATETIME,
                horizon_months INTEGER,
                   time_horizon_weeks INTEGER,
                fiscal_year INTEGER,
                scenario_type TEXT,
                scope_type TEXT,
                scope_notes TEXT,
                strategic_context TEXT,
                budget_constraints TEXT,
                legal_constraints TEXT,
                labor_relations_constraints TEXT,
                status TEXT,
                approved_at DATETIME,
                approved_by INTEGER,
                assumptions TEXT,
                owner_user_id INTEGER,
                sponsor_user_id INTEGER,
                template_id INTEGER,
                created_by INTEGER,
                updated_by INTEGER,
                created_at DATETIME,
                updated_at DATETIME,
                version_group_id TEXT,
                version_number INTEGER,
                is_current_version INTEGER,
                parent_id INTEGER,
                f TEXT,
                scope_id INTEGER,
                current_step INTEGER,
                decision_status TEXT,
                execution_status TEXT,
                owner_id INTEGER,
                last_simulated_at DATETIME,
                custom_config TEXT,
                estimated_budget INTEGER
            );

            CREATE TRIGGER IF NOT EXISTS trg_wp_scenarios_insert
            AFTER INSERT ON scenarios
            BEGIN
                INSERT INTO workforce_planning_scenarios (
                    id, organization_id, name, code, description, kpis, start_date, end_date,
                    horizon_months, time_horizon_weeks, fiscal_year, scenario_type, scope_type, scope_notes,
                    strategic_context, budget_constraints, legal_constraints, labor_relations_constraints,
                    status, approved_at, approved_by, assumptions, owner_user_id, sponsor_user_id,
                    template_id, created_by, updated_by, created_at, updated_at, version_group_id,
                    version_number, is_current_version, parent_id, f, scope_id, current_step,
                    decision_status, execution_status, owner_id, last_simulated_at, custom_config, estimated_budget
                ) VALUES (
                    NEW.id, NEW.organization_id, NEW.name, NEW.code, NEW.description, NEW.kpis, NEW.start_date, NEW.end_date,
                    NEW.horizon_months, NEW.time_horizon_weeks, NEW.fiscal_year, NEW.scenario_type, NEW.scope_type, NEW.scope_notes,
                    NEW.strategic_context, NEW.budget_constraints, NEW.legal_constraints, NEW.labor_relations_constraints,
                    NEW.status, NEW.approved_at, NEW.approved_by, NEW.assumptions, NEW.owner_user_id, NEW.sponsor_user_id,
                    NEW.template_id, NEW.created_by, NEW.updated_by, NEW.created_at, NEW.updated_at, NEW.version_group_id,
                    NEW.version_number, NEW.is_current_version, NEW.parent_id, NEW.f, NEW.scope_id, NEW.current_step,
                    NEW.decision_status, NEW.execution_status, NEW.owner_id, NEW.last_simulated_at, NEW.custom_config, NEW.estimated_budget
                );
            END;

            CREATE TRIGGER IF NOT EXISTS trg_wp_scenarios_update
            AFTER UPDATE ON scenarios
            BEGIN
                UPDATE workforce_planning_scenarios
                SET
                    organization_id = NEW.organization_id,
                    name = NEW.name,
                    code = NEW.code,
                    description = NEW.description,
                    kpis = NEW.kpis,
                    start_date = NEW.start_date,
                    end_date = NEW.end_date,
                    horizon_months = NEW.horizon_months,
                    time_horizon_weeks = NEW.time_horizon_weeks,
                    fiscal_year = NEW.fiscal_year,
                    scenario_type = NEW.scenario_type,
                    scope_type = NEW.scope_type,
                    scope_notes = NEW.scope_notes,
                    strategic_context = NEW.strategic_context,
                    budget_constraints = NEW.budget_constraints,
                    legal_constraints = NEW.legal_constraints,
                    labor_relations_constraints = NEW.labor_relations_constraints,
                    status = NEW.status,
                    approved_at = NEW.approved_at,
                    approved_by = NEW.approved_by,
                    assumptions = NEW.assumptions,
                    owner_user_id = NEW.owner_user_id,
                    sponsor_user_id = NEW.sponsor_user_id,
                    template_id = NEW.template_id,
                    created_by = NEW.created_by,
                    updated_by = NEW.updated_by,
                    updated_at = NEW.updated_at,
                    version_group_id = NEW.version_group_id,
                    version_number = NEW.version_number,
                    is_current_version = NEW.is_current_version,
                    parent_id = NEW.parent_id,
                    f = NEW.f,
                    scope_id = NEW.scope_id,
                    current_step = NEW.current_step,
                    decision_status = NEW.decision_status,
                    execution_status = NEW.execution_status,
                    owner_id = NEW.owner_id,
                    last_simulated_at = NEW.last_simulated_at,
                    custom_config = NEW.custom_config,
                    estimated_budget = NEW.estimated_budget
                WHERE id = NEW.id;
            END;

            CREATE TRIGGER IF NOT EXISTS trg_wp_scenarios_delete
            AFTER DELETE ON scenarios
            BEGIN
                DELETE FROM workforce_planning_scenarios WHERE id = OLD.id;
            END;
        SQL
        );
    }

    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_wp_scenarios_insert');
        DB::statement('DROP TRIGGER IF EXISTS trg_wp_scenarios_update');
        DB::statement('DROP TRIGGER IF EXISTS trg_wp_scenarios_delete');
        Schema::dropIfExists('workforce_planning_scenarios');
    }
};
