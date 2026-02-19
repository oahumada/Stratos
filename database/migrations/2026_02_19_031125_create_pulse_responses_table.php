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
        Schema::create('pulse_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pulse_survey_id')->constrained()->onDelete('cascade');
            $table->foreignId('people_id')->constrained('people')->onDelete('cascade');
            $table->jsonb('answers'); // {question_id: answer}
            $table->decimal('sentiment_score', 5, 2)->nullable(); // 0 to 100
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pulse_responses');
    }
};
