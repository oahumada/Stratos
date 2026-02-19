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
        // Almacenamiento para el Selector de Talento
        Schema::table('applications', function (Blueprint $table) {
            $table->json('ai_analysis')->nullable()->after('message');
            $table->integer('match_score')->nullable()->after('ai_analysis');
        });

        // Almacenamiento para el Navegador de Cultura
        Schema::table('pulse_surveys', function (Blueprint $table) {
            $table->json('ai_report')->nullable()->after('department_id');
        });

        // Almacenamiento para el Diseñador de Roles
        Schema::table('roles', function (Blueprint $table) {
            $table->json('ai_archetype_config')->nullable()->after('description');
        });

        Schema::table('scenario_roles', function (Blueprint $table) {
            $table->json('ai_suggestions')->nullable()->after('archetype');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['ai_analysis', 'match_score']);
        });

        Schema::table('pulse_surveys', function (Blueprint $table) {
            $table->dropColumn('ai_report');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('ai_archetype_config');
        });

        Schema::table('scenario_roles', function (Blueprint $table) {
            $table->dropColumn('ai_suggestions');
        });
    }
};
