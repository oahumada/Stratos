<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scenario_role_competencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('competency_id')->constrained()->onDelete('restrict');
            $table->unsignedSmallInteger('required_level')->default(1); // 1-5
            $table->boolean('is_core')->default(true);
            $table->string('change_type')->default('maintenance'); // maintenance, transformation, enrichment, extinction
            $table->text('rationale')->nullable();
            $table->timestamps();

            $table->unique(['scenario_id', 'role_id', 'competency_id'], 'scenario_role_comp_unique');
            $table->index(['scenario_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scenario_role_competencies');
    }
};
