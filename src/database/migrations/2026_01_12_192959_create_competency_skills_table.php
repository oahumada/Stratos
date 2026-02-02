<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('competency_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competency_id')->constrained()->onDelete('cascade');
            $table->foreignId('skill_id')->constrained()->onDelete('restrict');
            $table->unsignedSmallInteger('weight')->default(10); // 1-100 para ponderación
            $table->timestamps();

            $table->unique(['competency_id', 'skill_id']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('competency_skills');
    }
};