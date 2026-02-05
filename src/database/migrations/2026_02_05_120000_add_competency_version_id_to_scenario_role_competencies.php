<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompetencyVersionIdToScenarioRoleCompetencies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scenario_role_competencies', function (Blueprint $table) {
            $table->unsignedBigInteger('competency_version_id')->nullable()->after('competency_id');
            $table->foreign('competency_version_id')->references('id')->on('competency_versions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scenario_role_competencies', function (Blueprint $table) {
            $table->dropForeign(['competency_version_id']);
            $table->dropColumn('competency_version_id');
        });
    }
}
