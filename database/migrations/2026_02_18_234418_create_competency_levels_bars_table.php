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
        Schema::create('competency_levels_bars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_id')->constrained('skills')->onDelete('cascade');
            $table->integer('level')->unsigned()->comment('1-5');
            $table->string('name')->nullable()->comment('Ej: Novato, Experto');
            $table->text('description')->comment('Descripción conductual del nivel');
            $table->json('key_behaviors')->nullable()->comment('Ejemplos específicos de comportamientos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competency_levels_bars');
    }
};
