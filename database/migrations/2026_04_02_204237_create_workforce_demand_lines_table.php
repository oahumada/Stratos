<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workforce_demand_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained('scenarios')->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('unit')->comment('Unidad/área organizacional');
            $table->string('role_name')->comment('Nombre del rol o familia de roles');
            $table->string('period', 7)->comment('Período en formato YYYY-MM');
            $table->unsignedInteger('volume_expected')->default(0)->comment('Volumen de trabajo esperado');
            $table->unsignedSmallInteger('time_standard_minutes')->default(60)->comment('Tiempo estándar por unidad de trabajo en minutos');
            $table->decimal('productivity_factor', 4, 2)->default(1.00)->comment('Factor de productividad real vs estándar (0.1 – 2.0)');
            $table->decimal('coverage_target_pct', 5, 2)->default(95.00)->comment('Cobertura objetivo en %');
            $table->decimal('attrition_pct', 5, 2)->default(0.00)->comment('Tasa estimada de rotación para el período');
            $table->decimal('ramp_factor', 4, 2)->default(1.00)->comment('Factor de rampa para FTE en incorporación');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['scenario_id', 'period']);
            $table->index(['organization_id', 'scenario_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workforce_demand_lines');
    }
};
