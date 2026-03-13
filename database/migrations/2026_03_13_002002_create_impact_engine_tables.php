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
        // 1. Métricas de Negocio Empíricas (Measures)
        Schema::create('business_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained();
            $table->string('metric_name'); // e.g., productivity, revenue, tickets_resolved
            $table->decimal('metric_value', 15, 2);
            $table->string('unit')->nullable(); // e.g., USD, Units, Percentage
            $table->date('period_date'); // Fecha referencial del dato
            $table->string('source')->default('manual'); // ERP, CRM, Manual
            $table->foreignId('department_id')->nullable()->constrained();
            $table->foreignId('people_id')->nullable()->constrained(); // Si es métrica individual
            $table->json('metadata')->nullable(); // Contexto adicional
            $table->timestamps();
        });

        // 2. Análisis de Impacto y Ciencia de Decisión (Logic & Analytics)
        Schema::create('impact_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained();
            $table->string('type'); // correlation, roi_projection, cultural_friction_cost
            $table->string('target_engine')->nullable(); // vanguard, cerbero, etc.
            $table->json('correlations')->nullable(); // r-square, p-values detectados por IA
            $table->text('logic_narrative')->nullable(); // El "Por qué" (Logic de LAMP)
            $table->text('insight_summary');
            $table->json('recommendations')->nullable();
            
            // Stratos Sentinel Signature (SSS)
            $table->string('digital_signature')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->string('signature_version')->nullable();
            
            $table->timestamps();
        });

        // 3. Indicadores Financieros de Capital Humano (HCVA, ROI, Costos)
        Schema::create('financial_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained();
            $table->foreignId('people_id')->nullable()->constrained();
            $table->string('indicator_type'); // HCVA, replacement_cost, training_roi
            $table->decimal('value', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->date('reference_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_indicators');
        Schema::dropIfExists('impact_analyses');
        Schema::dropIfExists('business_metrics');
    }
};
