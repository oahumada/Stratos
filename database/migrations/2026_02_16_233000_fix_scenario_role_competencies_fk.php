<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('scenario_role_competencies', function (Blueprint $table) {
            try {
                $table->dropForeign(['role_id']);
            } catch (\Exception $e) {
                // Ignore if it doesn't exist
            }
        });

        // Data Fix: Update role_id from base roles to scenario_roles IDs
        // This is necessary to avoid FK violation when adding the new constraint
        if (DB::getDriverName() === 'pgsql' || DB::getDriverName() === 'mysql') {
            DB::statement("
                UPDATE scenario_role_competencies
                SET role_id = sub.new_role_id
                FROM (
                    SELECT sr.id as new_role_id, src.id as mapping_id
                    FROM scenario_role_competencies src
                    JOIN scenario_roles sr ON src.role_id = sr.role_id AND src.scenario_id = sr.scenario_id
                ) AS sub
                WHERE scenario_role_competencies.id = sub.mapping_id
            ");
        }

        Schema::table('scenario_role_competencies', function (Blueprint $table) {
            $table->foreign('role_id')->references('id')->on('scenario_roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scenario_role_competencies', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }
};
