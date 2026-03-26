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
        Schema::create('talent_pass_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('talent_pass_id')->constrained('talent_passes')->cascadeOnDelete();
            $table->string('skill_name');
            $table->enum('proficiency_level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('intermediate');
            $table->integer('years_of_experience')->default(0);
            $table->json('endorsed_by_people_ids')->nullable();
            $table->integer('endorsement_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['talent_pass_id', 'skill_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talent_pass_skills');
    }
};
