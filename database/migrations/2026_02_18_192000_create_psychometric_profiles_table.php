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
        Schema::create('psychometric_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('people_id')->constrained('people')->onDelete('cascade');
            $table->foreignId('assessment_session_id')->nullable()->constrained('assessment_sessions')->onDelete('set null');
            $table->string('trait_name'); // Ej: "Emotional Intelligence", "Technical Leadership", etc.
            $table->decimal('score', 4, 3); // 0.000 to 1.000
            $table->text('rationale')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psychometric_profiles');
    }
};
