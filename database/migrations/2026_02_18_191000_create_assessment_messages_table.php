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
        Schema::create('assessment_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_session_id')->constrained()->onDelete('cascade');
            $table->string('role'); // user, assistant, system
            $table->text('content');
            $table->jsonb('metadata')->nullable(); // Para guardar sentimiento o keywords extraídos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_messages');
    }
};
