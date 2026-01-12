<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scenario_capabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained()->onDelete('cascade');
            $table->foreignId('capability_id')->constrained()->onDelete('cascade');
            $table->string('strategic_role')->default('target'); // target, watch, sunset
            $table->unsignedSmallInteger('strategic_weight')->default(10); // 1-100 para IQ ponderado
            $table->unsignedSmallInteger('priority')->default(1); // 1-5
            $table->text('rationale')->nullable();
            $table->timestamps();

            $table->unique(['scenario_id', 'capability_id']);
        });

        // Añadir constraints solo en drivers que soporten ALTER TABLE ADD CONSTRAINT (no SQLite)
        if (DB::getDriverName() !== 'sqlite') {
            // Constraint para strategic_role
            DB::statement("ALTER TABLE scenario_capabilities ADD CONSTRAINT scenario_capabilities_strategic_role_check CHECK (strategic_role IN ('target', 'watch', 'sunset'))");

            // Constraint para strategic_weight
            DB::statement("ALTER TABLE scenario_capabilities ADD CONSTRAINT scenario_capabilities_strategic_weight_check CHECK (strategic_weight >= 1 AND strategic_weight <= 100)");

            // Constraint para priority
            DB::statement("ALTER TABLE scenario_capabilities ADD CONSTRAINT scenario_capabilities_priority_check CHECK (priority >= 1 AND priority <= 5)");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('scenario_capabilities');
    }
};