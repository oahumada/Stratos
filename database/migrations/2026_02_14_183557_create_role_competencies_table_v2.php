<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Indica si la migración debe ejecutarse dentro de una transacción.
     * Se deshabilita para permitir que la falla de CREATE EXTENSION no aborte el resto.
     */
    public $withinTransaction = false;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Intentar creación de la extensión pgvector (requiere que esté instalada en el sistema OS)
        try {
            DB::statement('CREATE EXTENSION IF NOT EXISTS pgvector');
        } catch (\Exception $e) {
            // Si falla, se ignora para permitir la creación del resto de la tabla
            // El usuario deberá instalar la extensión en el servidor PostgreSQL manualmente
        }

        // Recreación de la tabla role_competencies con la estructura solicitada
        // Se borra si existe para garantizar que los campos coincidan exactamente con la nueva definición
        Schema::dropIfExists('role_competencies');

        Schema::create('role_competencies', function (Blueprint $table) {
            // id: primary key, bigserial
            $table->id();

            // role_id: foreign key a tabla roles, no nulo
            $table->foreignId('role_id')
                  ->constrained('roles')
                  ->onDelete('cascade');

            // competency_id: foreign key a tabla competencies, no nulo
            $table->foreignId('competency_id')
                  ->constrained('competencies')
                  ->onDelete('cascade');

            // required_level: integer, nivel requerido de la competencia (1-5), no nulo
            $table->integer('required_level');

            // criticity: integer, nivel de criticidad de la competencia para el rol (1-5), no nulo, default 3
            $table->integer('criticity')->default(3);

            // change_type: enum con valores ('new', 'increased', 'stable'), no nulo, default 'stable'
            // Indica si el requerimiento es nuevo, ha aumentado o se mantiene.
            $table->enum('change_type', ['new', 'increased', 'stable'])->default('stable');

            // strategy: enum con valores ('buy', 'build', 'borrow', 'bot'), nullable inicialmente
            // Estrategia recomendada por agentes IA o definida por expertos para cerrar brechas.
            $table->enum('strategy', ['buy', 'build', 'borrow', 'bot'])->nullable();

            // notes: texto, para observaciones adicionales, nullable
            $table->text('notes')->nullable();

            // created_at y updated_at: timestamps con default a la hora actual
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            // Índices para optimizar consultas por role_id y competency_id
            $table->index('role_id');
            $table->index('competency_id');
            
            // Evitar duplicados de la misma competencia para el mismo rol
            $table->unique(['role_id', 'competency_id'], 'role_comp_unique');
        });

        // Documentación mediante comentarios SQL para cada campo
        DB::statement("COMMENT ON TABLE role_competencies IS 'Asociación de competencias a roles con niveles requeridos y criterios de impacto.'");
        
        DB::statement("COMMENT ON COLUMN role_competencies.required_level IS 'Nivel mínimo esperado (1-5). Base fundamental para el cálculo de brechas (talent gaps).' ");
        
        DB::statement("COMMENT ON COLUMN role_competencies.criticity IS 'Prioridad de cierre de brecha (1-5). Las competencias críticas se priorizan en planes de desarrollo y sugerencias de IA.'");
        
        DB::statement("COMMENT ON COLUMN role_competencies.change_type IS 'Indica la evolución del requerimiento (new, increased, stable). Crucial para proyecciones en Scenario IQ.'");
        
        DB::statement("COMMENT ON COLUMN role_competencies.strategy IS 'Estrategia sugerida para cerrar brechas: Buy (externo), Build (interno), Borrow (movilidad) o Bot (automatización).'");
        
        DB::statement("COMMENT ON COLUMN role_competencies.notes IS 'Contexto adicional para decisiones manuales o retroalimentación a agentes IA.'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_competencies');
    }
};
