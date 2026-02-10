<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('capabilities') && ! Schema::hasColumn('capabilities', 'llm_id')) {
            Schema::table('capabilities', function (Blueprint $table) {
                $table->string('llm_id', 191)->nullable()->after('id');
                $table->index(['organization_id', 'llm_id'], 'capabilities_org_llm_idx');
            });
        }

        if (Schema::hasTable('competencies') && ! Schema::hasColumn('competencies', 'llm_id')) {
            Schema::table('competencies', function (Blueprint $table) {
                $table->string('llm_id', 191)->nullable()->after('id');
                $table->index(['organization_id', 'llm_id'], 'competencies_org_llm_idx');
            });
        }

        if (Schema::hasTable('skills') && ! Schema::hasColumn('skills', 'llm_id')) {
            Schema::table('skills', function (Blueprint $table) {
                $table->string('llm_id', 191)->nullable()->after('id');
                $table->index(['organization_id', 'llm_id'], 'skills_org_llm_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('capabilities') && Schema::hasColumn('capabilities', 'llm_id')) {
            Schema::table('capabilities', function (Blueprint $table) {
                $table->dropIndex('capabilities_org_llm_idx');
                $table->dropColumn('llm_id');
            });
        }

        if (Schema::hasTable('competencies') && Schema::hasColumn('competencies', 'llm_id')) {
            Schema::table('competencies', function (Blueprint $table) {
                $table->dropIndex('competencies_org_llm_idx');
                $table->dropColumn('llm_id');
            });
        }

        if (Schema::hasTable('skills') && Schema::hasColumn('skills', 'llm_id')) {
            Schema::table('skills', function (Blueprint $table) {
                $table->dropIndex('skills_org_llm_idx');
                $table->dropColumn('llm_id');
            });
        }
    }
};
