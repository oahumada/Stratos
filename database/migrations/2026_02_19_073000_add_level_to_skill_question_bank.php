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
        Schema::table('skill_question_bank', function (Blueprint $table) {
            $table->unsignedInteger('level')->nullable()->comment('Nivel de competencia asociado (1-5)')->after('skill_id');
            $table->string('question_type')->default('situational')->after('target_relationship')->comment('behavioral, situational, technical');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skill_question_bank', function (Blueprint $table) {
            $table->dropColumn(['level', 'question_type']);
        });
    }
};
