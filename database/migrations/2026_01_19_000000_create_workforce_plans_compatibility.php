<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Lightweight compatibility table: writes to workforce_plans mirror into scenarios
        DB::statement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS workforce_plans (
                id INTEGER PRIMARY KEY,
                organization_id INTEGER,
                name TEXT,
                code TEXT,
                description TEXT,
                start_date DATETIME,
                end_date DATETIME,
                planning_horizon_months INTEGER,
                scope_type TEXT,
                scope_notes TEXT,
                strategic_context TEXT,
                budget_constraints TEXT,
                legal_constraints TEXT,
                labor_relations_constraints TEXT,
                status TEXT,
                owner_user_id INTEGER,
                sponsor_user_id INTEGER,
                created_by INTEGER,
                updated_by INTEGER,
                created_at DATETIME,
                updated_at DATETIME
            );

            CREATE TRIGGER IF NOT EXISTS trg_wp_plans_insert
            AFTER INSERT ON workforce_plans
            BEGIN
                INSERT INTO scenarios (
                    id, organization_id, name, code, description, start_date, end_date,
                    horizon_months, scope_type, scope_notes, strategic_context, budget_constraints,
                    legal_constraints, labor_relations_constraints, status, owner_user_id, sponsor_user_id,
                    created_by, updated_by, created_at, updated_at
                ) VALUES (
                    NEW.id, NEW.organization_id, NEW.name, NEW.code, NEW.description, NEW.start_date, NEW.end_date,
                    NEW.planning_horizon_months, NEW.scope_type, NEW.scope_notes, NEW.strategic_context, NEW.budget_constraints,
                    NEW.legal_constraints, NEW.labor_relations_constraints, NEW.status, NEW.owner_user_id, NEW.sponsor_user_id,
                    NEW.created_by, NEW.updated_by, NEW.created_at, NEW.updated_at
                );
            END;

            CREATE TRIGGER IF NOT EXISTS trg_wp_plans_update
            AFTER UPDATE ON workforce_plans
            BEGIN
                UPDATE scenarios
                SET
                    organization_id = NEW.organization_id,
                    name = NEW.name,
                    code = NEW.code,
                    description = NEW.description,
                    start_date = NEW.start_date,
                    end_date = NEW.end_date,
                    horizon_months = NEW.planning_horizon_months,
                    scope_type = NEW.scope_type,
                    scope_notes = NEW.scope_notes,
                    strategic_context = NEW.strategic_context,
                    budget_constraints = NEW.budget_constraints,
                    legal_constraints = NEW.legal_constraints,
                    labor_relations_constraints = NEW.labor_relations_constraints,
                    status = NEW.status,
                    owner_user_id = NEW.owner_user_id,
                    sponsor_user_id = NEW.sponsor_user_id,
                    created_by = NEW.created_by,
                    updated_by = NEW.updated_by,
                    updated_at = NEW.updated_at
                WHERE id = NEW.id;
            END;

            CREATE TRIGGER IF NOT EXISTS trg_wp_plans_delete
            AFTER DELETE ON workforce_plans
            BEGIN
                DELETE FROM scenarios WHERE id = OLD.id;
            END;

            -- Keep workforce_plans in sync when scenarios are modified directly
            CREATE TRIGGER IF NOT EXISTS trg_scenarios_insert_to_wp
            AFTER INSERT ON scenarios
            BEGIN
                INSERT OR REPLACE INTO workforce_plans (
                    id, organization_id, name, code, description, start_date, end_date,
                    planning_horizon_months, scope_type, scope_notes, strategic_context, budget_constraints,
                    legal_constraints, labor_relations_constraints, status, owner_user_id, sponsor_user_id,
                    created_by, updated_by, created_at, updated_at
                ) VALUES (
                    NEW.id, NEW.organization_id, NEW.name, NEW.code, NEW.description, NEW.start_date, NEW.end_date,
                    NEW.horizon_months, NEW.scope_type, NEW.scope_notes, NEW.strategic_context, NEW.budget_constraints,
                    NEW.legal_constraints, NEW.labor_relations_constraints, NEW.status, NEW.owner_user_id, NEW.sponsor_user_id,
                    NEW.created_by, NEW.updated_by, NEW.created_at, NEW.updated_at
                );
            END;

            CREATE TRIGGER IF NOT EXISTS trg_scenarios_update_to_wp
            AFTER UPDATE ON scenarios
            BEGIN
                UPDATE workforce_plans
                SET
                    organization_id = NEW.organization_id,
                    name = NEW.name,
                    code = NEW.code,
                    description = NEW.description,
                    start_date = NEW.start_date,
                    end_date = NEW.end_date,
                    planning_horizon_months = NEW.horizon_months,
                    scope_type = NEW.scope_type,
                    scope_notes = NEW.scope_notes,
                    strategic_context = NEW.strategic_context,
                    budget_constraints = NEW.budget_constraints,
                    legal_constraints = NEW.legal_constraints,
                    labor_relations_constraints = NEW.labor_relations_constraints,
                    status = NEW.status,
                    owner_user_id = NEW.owner_user_id,
                    sponsor_user_id = NEW.sponsor_user_id,
                    created_by = NEW.created_by,
                    updated_by = NEW.updated_by,
                    updated_at = NEW.updated_at
                WHERE id = NEW.id;
            END;

            CREATE TRIGGER IF NOT EXISTS trg_scenarios_delete_to_wp
            AFTER DELETE ON scenarios
            BEGIN
                DELETE FROM workforce_plans WHERE id = OLD.id;
            END;
        SQL
        );
    }

    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_wp_plans_insert');
        DB::statement('DROP TRIGGER IF EXISTS trg_wp_plans_update');
        DB::statement('DROP TRIGGER IF EXISTS trg_wp_plans_delete');
        DB::statement('DROP TRIGGER IF EXISTS trg_scenarios_insert_to_wp');
        DB::statement('DROP TRIGGER IF EXISTS trg_scenarios_update_to_wp');
        DB::statement('DROP TRIGGER IF EXISTS trg_scenarios_delete_to_wp');
        DB::statement('DROP TABLE IF EXISTS workforce_plans');
    }
};
