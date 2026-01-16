<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('people_role_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('people_id')->constrained('people')->onDelete('cascade');
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->foreignId('skill_id')->constrained('skills')->onDelete('cascade');

            // Niveles
            $table->integer('current_level')->default(1)->comment('Nivel actual de la persona en esta skill (1-5)');
            $table->integer('required_level')->default(3)->comment('Nivel requerido por el rol en el momento de asignación');

            // Control de vigencia
            $table->boolean('is_active')->default(true)->comment('Si está activa (rol actual) o es histórica (rol pasado)');
            $table->timestamp('evaluated_at')->nullable()->comment('Fecha de última evaluación');
            $table->timestamp('expires_at')->nullable()->comment('Fecha de caducidad - requiere reevaluación');
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('verified')->default(false);
            $table->string('evidence_source')->default('self_assessment'); // self_assessment, manager_review, certification, test, Talent360
            $table->timestamp('evidence_date')->nullable();

            // Auditoría
            $table->text('notes')->nullable()->comment('Notas de evaluación o cambios');

            // Índices para consultas frecuentes
            $table->index(['people_id', 'is_active']);
            $table->index(['role_id', 'skill_id']);
            $table->index('expires_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people_role_skills');
    }
};
