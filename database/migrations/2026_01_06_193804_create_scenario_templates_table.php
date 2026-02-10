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
        Schema::create('scenario_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "IA Adoption Accelerator", "Digital Transformation"
            $table->string('slug')->unique(); // "ia-adoption-accelerator"
            $table->text('description')->nullable();
            $table->enum('scenario_type', ['growth', 'transformation', 'optimization', 'crisis', 'custom'])->default('custom');
            $table->string('industry')->default('general'); // "tech", "finance", "retail", "general"
            $table->string('icon')->nullable(); // "mdi-robot", "mdi-chart-line"
            $table->json('config')->nullable(); // Configuración: predefined_skills, suggested_strategies, kpis, etc.
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0); // Tracking de uso
            $table->timestamps();
            $table->softDeletes();

            $table->index('scenario_type');
            $table->index('industry');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scenario_templates');
    }
};
