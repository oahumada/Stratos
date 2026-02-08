<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('role_skills', 'source')) {
            Schema::table('role_skills', function (Blueprint $table) {
                $table->string('source')->default('competency')->after('is_critical'); // 'competency' | 'manual'
                $table->foreignId('competency_id')->nullable()->after('source')->constrained()->onDelete('cascade');
            });

            // Constraint para source (SQLite no soporta ALTER TABLE ADD CONSTRAINT)
            if (DB::getDriverName() !== 'sqlite') {
                DB::statement("ALTER TABLE role_skills ADD CONSTRAINT role_skills_source_check CHECK (source IN ('competency', 'manual'))");
            }
        }
    }

    public function down(): void
    {
        Schema::table('role_skills', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropConstraint('role_skills_source_check');
            }
            $table->dropForeign(['competency_id']);
            $table->dropColumn(['source', 'competency_id']);
        });
    }
};
