<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Create a lightweight compatibility table and triggers to mirror 'scenarios'
        DB::statement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS workforce_planning_scenarios (
                id INTEGER PRIMARY KEY,
                name TEXT,
                description TEXT,
                scenario_type TEXT,
                horizon_months INTEGER,
                fiscal_year INTEGER,
                organization_id INTEGER,
                template_id INTEGER,
                created_by INTEGER,
                created_at DATETIME,
                updated_at DATETIME
            );

            CREATE TRIGGER IF NOT EXISTS trg_wp_scenarios_insert
            AFTER INSERT ON scenarios
            BEGIN
                INSERT INTO workforce_planning_scenarios (id, name, organization_id, created_by, created_at, updated_at)
                VALUES (NEW.id, NEW.name, NEW.organization_id, NEW.created_by, NEW.created_at, NEW.updated_at);
            END;

            CREATE TRIGGER IF NOT EXISTS trg_wp_scenarios_update
            AFTER UPDATE ON scenarios
            BEGIN
                UPDATE workforce_planning_scenarios
                SET name = NEW.name,
                    organization_id = NEW.organization_id,
                    created_by = NEW.created_by,
                    created_at = NEW.created_at,
                    updated_at = NEW.updated_at
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
