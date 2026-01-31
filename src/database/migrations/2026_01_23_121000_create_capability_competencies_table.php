<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('capability_competencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained('scenarios')->onDelete('cascade');
            $table->foreignId('capability_id')->constrained('capabilities')->onDelete('cascade');
            $table->foreignId('competency_id')->constrained('competencies')->onDelete('cascade');
            $table->unsignedSmallInteger('required_level')->default(3);
            $table->unsignedSmallInteger('priority')->nullable();
            $table->unsignedSmallInteger('weight')->nullable();
            $table->text('rationale')->nullable();
            $table->boolean('is_required')->default(false);
            $table->timestamps();

            $table->unique(['scenario_id', 'capability_id', 'competency_id'], 'cap_comp_scenario_cap_comp_idx');
            $table->index(['capability_id', 'competency_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capability_competencies');
    }
};
