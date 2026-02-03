<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('scenario_role_skills', function (Blueprint $table) {
            $table->string('source')->default('competency')->after('is_critical'); // 'competency' | 'manual'
            $table->foreignId('competency_id')->nullable()->after('source')->constrained()->onDelete('cascade');
        });

        // Constraint para source (SQLite no soporta ALTER TABLE ADD CONSTRAINT)
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE scenario_role_skills ADD CONSTRAINT scenario_role_skills_source_check CHECK (source IN ('competency', 'manual'))");
        }
    }

    public function down(): void
    {
        Schema::table('scenario_role_skills', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropConstraint('scenario_role_skills_source_check');
            }
            $table->dropForeign(['competency_id']);
            $table->dropColumn(['source', 'competency_id']);
        });
    }
};