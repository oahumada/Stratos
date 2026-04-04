<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('lms_surveys')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrollment_id')->nullable()->constrained('lms_enrollments')->nullOnDelete();
            $table->jsonb('answers'); // [{question_index, answer}]
            $table->tinyInteger('nps_score')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['survey_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_survey_responses');
    }
};
