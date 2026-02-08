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
        Schema::create('scenario_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained('scenarios')->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained('skills')->cascadeOnDelete();
            $table->enum('strategic_role', ['target', 'accelerator', 'sunset', 'watch', 'secondary'])->default('secondary');
            $table->integer('priority')->default(0);
            $table->text('rationale')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scenario_skills');
    }
};
