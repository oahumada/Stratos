<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scenario_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->string('role_change')->default('evolve'); // evolve, new, sunset
            $table->string('impact_level')->default('medium'); // high, medium, low
            $table->string('evolution_type')->default('incremental'); // incremental, transformative, disruptive
            $table->text('rationale')->nullable();
            $table->timestamps();

            $table->unique(['scenario_id', 'role_id']);
        });

        /*         // Añadir constraints solo en drivers que soporten ALTER TABLE ADD CONSTRAINT (no SQLite)
                if (DB::getDriverName() !== 'sqlite') {
                    // Constraint para role_change
                    DB::statement("ALTER TABLE scenario_roles ADD CONSTRAINT scenario_roles_role_change_check CHECK (role_change IN ('evolve', 'new', 'sunset'))");

                    // Constraint para impact_level
                    DB::statement("ALTER TABLE scenario_roles ADD CONSTRAINT scenario_roles_impact_level_check CHECK (impact_level IN ('high', 'medium', 'low'))");

                    // Constraint para evolution_type
                    DB::statement("ALTER TABLE scenario_roles ADD CONSTRAINT scenario_roles_evolution_type_check CHECK (evolution_type IN ('incremental', 'transformative', 'disruptive'))");
                }
                */
    }

    public function down(): void
    {
        Schema::dropIfExists('scenario_roles');
    }
};
