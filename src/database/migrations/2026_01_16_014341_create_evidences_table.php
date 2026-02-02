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
        Schema::create('evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['kpi', 'artifact', 'certification', 'project'])->default('artifact');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable(); // Para PDFs, imágenes, etc.
            $table->string('external_url')->nullable(); // Para links externos
            $table->json('metadata')->nullable(); // Para KPIs: {value: 95, unit: "%", date: "2026-01"}
            $table->foreignId('validated_by')->nullable()->constrained('users'); // Validador social
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidences');
    }
};
