<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('scenario_role_skills', 'competency_version_id')) {
            Schema::table('scenario_role_skills', function (Blueprint $table) {
                $table->foreignId('competency_version_id')->nullable()->after('competency_id')->constrained('competency_versions')->nullOnDelete();
                $table->json('metadata')->nullable()->after('competency_version_id');
                $table->foreignId('created_by')->nullable()->after('metadata')->constrained('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('scenario_role_skills', function (Blueprint $table) {
            if (Schema::hasColumn('scenario_role_skills', 'competency_version_id')) {
                $table->dropForeign(['competency_version_id']);
                $table->dropColumn(['competency_version_id']);
            }
            if (Schema::hasColumn('scenario_role_skills', 'metadata')) {
                $table->dropColumn('metadata');
            }
            if (Schema::hasColumn('scenario_role_skills', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
        });
    }
};
