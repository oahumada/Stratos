<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create the table using Schema builder (database agnostic)
        if (! Schema::hasTable('workforce_plans')) {
            Schema::create('workforce_plans', function (Blueprint $table) {
                $table->id(); // integer primary key
                $table->integer('organization_id')->nullable();
                $table->text('name')->nullable();
                $table->text('code')->nullable();
                $table->text('description')->nullable();
                $table->dateTime('start_date')->nullable();
                $table->dateTime('end_date')->nullable();
                $table->integer('planning_horizon_months')->nullable();
                $table->text('scope_type')->nullable();
                $table->text('scope_notes')->nullable();
                $table->text('strategic_context')->nullable();
                $table->text('budget_constraints')->nullable();
                $table->text('legal_constraints')->nullable();
                $table->text('labor_relations_constraints')->nullable();
                $table->text('status')->nullable();
                $table->integer('owner_user_id')->nullable();
                $table->integer('sponsor_user_id')->nullable();
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->timestamps();
            });
        }

        // 2. Add Triggers based on driver
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement(<<<'SQL'
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
            SQL);
        } elseif ($driver === 'pgsql') {
            // PostgreSQL Functions and Triggers
            DB::unprepared(<<<'SQL'
                -- Function for wp -> scenarios (Insert)
                CREATE OR REPLACE FUNCTION trg_func_wp_plans_insert() RETURNS TRIGGER AS $$
                BEGIN
                    IF pg_trigger_depth() > 1 THEN RETURN NEW; END IF;
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
                    RETURN NEW;
                END;
                $$ LANGUAGE plpgsql;

                CREATE TRIGGER trg_wp_plans_insert
                AFTER INSERT ON workforce_plans
                FOR EACH ROW EXECUTE FUNCTION trg_func_wp_plans_insert();

                -- Function for wp -> scenarios (Update)
                CREATE OR REPLACE FUNCTION trg_func_wp_plans_update() RETURNS TRIGGER AS $$
                BEGIN
                    IF pg_trigger_depth() > 1 THEN RETURN NEW; END IF;
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
                    RETURN NEW;
                END;
                $$ LANGUAGE plpgsql;

                CREATE TRIGGER trg_wp_plans_update
                AFTER UPDATE ON workforce_plans
                FOR EACH ROW EXECUTE FUNCTION trg_func_wp_plans_update();

                -- Function for wp -> scenarios (Delete)
                CREATE OR REPLACE FUNCTION trg_func_wp_plans_delete() RETURNS TRIGGER AS $$
                BEGIN
                    IF pg_trigger_depth() > 1 THEN RETURN OLD; END IF;
                    DELETE FROM scenarios WHERE id = OLD.id;
                    RETURN OLD;
                END;
                $$ LANGUAGE plpgsql;

                CREATE TRIGGER trg_wp_plans_delete
                AFTER DELETE ON workforce_plans
                FOR EACH ROW EXECUTE FUNCTION trg_func_wp_plans_delete();

                -- Function for scenarios -> wp (Insert)
                CREATE OR REPLACE FUNCTION trg_func_scenarios_insert_to_wp() RETURNS TRIGGER AS $$
                BEGIN
                    IF pg_trigger_depth() > 1 THEN RETURN NEW; END IF;
                    INSERT INTO workforce_plans (
                        id, organization_id, name, code, description, start_date, end_date,
                        planning_horizon_months, scope_type, scope_notes, strategic_context, budget_constraints,
                        legal_constraints, labor_relations_constraints, status, owner_user_id, sponsor_user_id,
                        created_by, updated_by, created_at, updated_at
                    ) VALUES (
                        NEW.id, NEW.organization_id, NEW.name, NEW.code, NEW.description, NEW.start_date, NEW.end_date,
                        NEW.horizon_months, NEW.scope_type, NEW.scope_notes, NEW.strategic_context, NEW.budget_constraints,
                        NEW.legal_constraints, NEW.labor_relations_constraints, NEW.status, NEW.owner_user_id, NEW.sponsor_user_id,
                        NEW.created_by, NEW.updated_by, NEW.created_at, NEW.updated_at
                    )
                    ON CONFLICT (id) DO UPDATE SET
                        organization_id = EXCLUDED.organization_id,
                        name = EXCLUDED.name,
                        code = EXCLUDED.code,
                        description = EXCLUDED.description,
                        start_date = EXCLUDED.start_date,
                        end_date = EXCLUDED.end_date,
                        planning_horizon_months = EXCLUDED.planning_horizon_months,
                        scope_type = EXCLUDED.scope_type,
                        scope_notes = EXCLUDED.scope_notes,
                        strategic_context = EXCLUDED.strategic_context,
                        budget_constraints = EXCLUDED.budget_constraints,
                        legal_constraints = EXCLUDED.legal_constraints,
                        labor_relations_constraints = EXCLUDED.labor_relations_constraints,
                        status = EXCLUDED.status,
                        owner_user_id = EXCLUDED.owner_user_id,
                        sponsor_user_id = EXCLUDED.sponsor_user_id,
                        created_by = EXCLUDED.created_by,
                        updated_by = EXCLUDED.updated_by,
                        updated_at = EXCLUDED.updated_at;
                    RETURN NEW;
                END;
                $$ LANGUAGE plpgsql;

                CREATE TRIGGER trg_scenarios_insert_to_wp
                AFTER INSERT ON scenarios
                FOR EACH ROW EXECUTE FUNCTION trg_func_scenarios_insert_to_wp();

                -- Function for scenarios -> wp (Update)
                CREATE OR REPLACE FUNCTION trg_func_scenarios_update_to_wp() RETURNS TRIGGER AS $$
                BEGIN
                    IF pg_trigger_depth() > 1 THEN RETURN NEW; END IF;
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
                    RETURN NEW;
                END;
                $$ LANGUAGE plpgsql;

                CREATE TRIGGER trg_scenarios_update_to_wp
                AFTER UPDATE ON scenarios
                FOR EACH ROW EXECUTE FUNCTION trg_func_scenarios_update_to_wp();

                -- Function for scenarios -> wp (Delete)
                CREATE OR REPLACE FUNCTION trg_func_scenarios_delete_to_wp() RETURNS TRIGGER AS $$
                BEGIN
                    IF pg_trigger_depth() > 1 THEN RETURN OLD; END IF;
                    DELETE FROM workforce_plans WHERE id = OLD.id;
                    RETURN OLD;
                END;
                $$ LANGUAGE plpgsql;

                CREATE TRIGGER trg_scenarios_delete_to_wp
                AFTER DELETE ON scenarios
                FOR EACH ROW EXECUTE FUNCTION trg_func_scenarios_delete_to_wp();
            SQL);
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('DROP TRIGGER IF EXISTS trg_wp_plans_insert');
            DB::statement('DROP TRIGGER IF EXISTS trg_wp_plans_update');
            DB::statement('DROP TRIGGER IF EXISTS trg_wp_plans_delete');
            DB::statement('DROP TRIGGER IF EXISTS trg_scenarios_insert_to_wp');
            DB::statement('DROP TRIGGER IF EXISTS trg_scenarios_update_to_wp');
            DB::statement('DROP TRIGGER IF EXISTS trg_scenarios_delete_to_wp');
        } elseif ($driver === 'pgsql') {
            DB::statement('DROP TRIGGER IF EXISTS trg_wp_plans_insert ON workforce_plans');
            DB::statement('DROP TRIGGER IF EXISTS trg_wp_plans_update ON workforce_plans');
            DB::statement('DROP TRIGGER IF EXISTS trg_wp_plans_delete ON workforce_plans');
            DB::statement('DROP TRIGGER IF EXISTS trg_scenarios_insert_to_wp ON scenarios');
            DB::statement('DROP TRIGGER IF EXISTS trg_scenarios_update_to_wp ON scenarios');
            DB::statement('DROP TRIGGER IF EXISTS trg_scenarios_delete_to_wp ON scenarios');

            DB::statement('DROP FUNCTION IF EXISTS trg_func_wp_plans_insert');
            DB::statement('DROP FUNCTION IF EXISTS trg_func_wp_plans_update');
            DB::statement('DROP FUNCTION IF EXISTS trg_func_wp_plans_delete');
            DB::statement('DROP FUNCTION IF EXISTS trg_func_scenarios_insert_to_wp');
            DB::statement('DROP FUNCTION IF EXISTS trg_func_scenarios_update_to_wp');
            DB::statement('DROP FUNCTION IF EXISTS trg_func_scenarios_delete_to_wp');
        }
        
        Schema::dropIfExists('workforce_plans');
    }
};
