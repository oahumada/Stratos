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
        Schema::create('skill_question_bank', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_id')->constrained('skills')->onDelete('cascade');
            $table->string('archetype')->nullable()->comment('Ej: Liderazgo, Técnico, Operativo');
            $table->string('target_relationship')->nullable()->comment('self, boss, peer, subordinate, all');
            $table->text('question');
            $table->boolean('is_global')->default(false)->comment('Si es una pregunta de validación general');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skill_question_bank');
    }
};
