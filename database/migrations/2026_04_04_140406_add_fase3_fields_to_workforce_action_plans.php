<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workforce_action_plans', function (Blueprint $table) {
            // Budget tracking (Fase 3 — gobernanza financiera)
            $table->decimal('budget', 15, 2)->nullable()->after('progress_pct')->comment('Presupuesto estimado en USD');
            $table->decimal('actual_cost', 15, 2)->nullable()->after('budget')->comment('Costo real incurrido');

            // Organizational unit context
            $table->string('unit_name', 255)->nullable()->after('actual_cost')->comment('Unidad organizacional responsable');

            // Palanca de cierre de brecha que origina esta acción
            $table->string('lever', 50)->nullable()->after('unit_name')
                ->comment('Palanca: HIRE|RESKILL|ROTATE|TRANSFER|CONTINGENT|AUTOMATE|HYBRID_TALENT');

            // Hybrid talent coverage indicator
            $table->unsignedTinyInteger('hybrid_coverage_pct')->default(0)->after('lever')
                ->comment('% de la capacidad cubierta por talento híbrido (humano+IA)');

            $table->index(['organization_id', 'lever']);
            $table->index(['organization_id', 'unit_name']);
        });
    }

    public function down(): void
    {
        Schema::table('workforce_action_plans', function (Blueprint $table) {
            $table->dropIndex(['organization_id', 'lever']);
            $table->dropIndex(['organization_id', 'unit_name']);
            $table->dropColumn(['budget', 'actual_cost', 'unit_name', 'lever', 'hybrid_coverage_pct']);
        });
    }
};
