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
        Schema::create('assessment_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('name');
            $table->enum('target_type', ['department', 'role', 'person', 'global'])->default('global');
            $table->unsignedBigInteger('target_id')->nullable();
            $table->integer('frequency_months')->nullable()->comment('Periodicidad en meses');
            $table->string('trigger_event')->nullable()->comment('Evento disparador: promotion, onboarding_end, etc');
            $table->json('evaluators_config')->nullable()->comment('Configuración de fuentes: jefe, pares, etc');
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null')->comment('Responsable de la métrica');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_policies');
    }
};
