<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $connection = Schema::getConnection()->getDriverName();

        if ($connection === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');

            // create new table with capability_id nullable
            DB::statement(
                'CREATE TABLE skills_new (id INTEGER PRIMARY KEY AUTOINCREMENT, organization_id INTEGER NOT NULL, name TEXT NOT NULL, description TEXT, complexity_level TEXT NOT NULL, capability_id INTEGER, lifecycle_status TEXT NOT NULL, parent_skill_id INTEGER, category TEXT NOT NULL, is_critical INTEGER NOT NULL, scope_type TEXT NOT NULL, domain_tag TEXT, created_at DATETIME, updated_at DATETIME, FOREIGN KEY(organization_id) REFERENCES organizations(id), FOREIGN KEY(capability_id) REFERENCES capabilities(id) ON DELETE CASCADE, FOREIGN KEY(parent_skill_id) REFERENCES skills(id) ON DELETE SET NULL)'
            );

            DB::statement('INSERT INTO skills_new (id, organization_id, name, description, complexity_level, capability_id, lifecycle_status, parent_skill_id, category, is_critical, scope_type, domain_tag, created_at, updated_at) SELECT id, organization_id, name, description, complexity_level, capability_id, lifecycle_status, parent_skill_id, category, is_critical, scope_type, domain_tag, created_at, updated_at FROM skills');

            DB::statement('DROP TABLE skills');
            DB::statement('ALTER TABLE skills_new RENAME TO skills');

            DB::statement('PRAGMA foreign_keys = ON');

            // recreate indexes/uniques
            Schema::table('skills', function (Blueprint $table) {
                $table->unique(['organization_id', 'name']);
                $table->index('category');
                $table->index('scope_type');
                $table->index('domain_tag');
            });

            return;
        }

        // For other engines support alter
        Schema::table('skills', function (Blueprint $table) {
            $table->foreignId('capability_id')->nullable()->constrained()->onDelete('cascade')->change();
        });
    }

    public function down(): void
    {
        $connection = Schema::getConnection()->getDriverName();

        if ($connection === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');

            DB::statement(
                'CREATE TABLE skills_old (id INTEGER PRIMARY KEY AUTOINCREMENT, organization_id INTEGER NOT NULL, name TEXT NOT NULL, description TEXT, complexity_level TEXT NOT NULL, capability_id INTEGER NOT NULL, lifecycle_status TEXT NOT NULL, parent_skill_id INTEGER, category TEXT NOT NULL, is_critical INTEGER NOT NULL, scope_type TEXT NOT NULL, domain_tag TEXT, created_at DATETIME, updated_at DATETIME, FOREIGN KEY(organization_id) REFERENCES organizations(id), FOREIGN KEY(capability_id) REFERENCES capabilities(id) ON DELETE CASCADE, FOREIGN KEY(parent_skill_id) REFERENCES skills(id) ON DELETE SET NULL)'
            );

            DB::statement('INSERT INTO skills_old (id, organization_id, name, description, complexity_level, capability_id, lifecycle_status, parent_skill_id, category, is_critical, scope_type, domain_tag, created_at, updated_at) SELECT id, organization_id, name, description, complexity_level, COALESCE(capability_id, 0), lifecycle_status, parent_skill_id, category, is_critical, scope_type, domain_tag, created_at, updated_at FROM skills');

            DB::statement('DROP TABLE skills');
            DB::statement('ALTER TABLE skills_old RENAME TO skills');

            DB::statement('PRAGMA foreign_keys = ON');

            Schema::table('skills', function (Blueprint $table) {
                $table->unique(['organization_id', 'name']);
                $table->index('category');
                $table->index('scope_type');
                $table->index('domain_tag');
            });

            return;
        }

        Schema::table('skills', function (Blueprint $table) {
            $table->foreignId('capability_id')->nullable(false)->constrained()->onDelete('cascade')->change();
        });
    }
};
