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
        Schema::table('assessment_feedback', function (Blueprint $table) {
            $table->foreignId('skill_id')->nullable()->constrained('skills')->onDelete('cascade');
            $table->integer('score')->nullable()->after('answer')->comment('Puntaje BARS 1-5');
            $table->string('evidence_url')->nullable()->after('score');
            $table->integer('confidence_level')->nullable()->after('evidence_url')->comment('Nivel de certeza del evaluador 1-100');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_feedback', function (Blueprint $table) {
            //
        });
    }
};
