<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_competencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('competency_id')->constrained()->onDelete('restrict');
            $table->unsignedSmallInteger('required_level')->default(1); // 1-5
            $table->boolean('is_core')->default(true); // Competencia crítica para el rol
            $table->text('rationale')->nullable();
            $table->timestamps();

            $table->unique(['role_id', 'competency_id']);
            $table->index('role_id');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('role_competencies');
    }
};
