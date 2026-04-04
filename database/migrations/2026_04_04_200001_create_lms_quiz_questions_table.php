<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_quiz_id')->constrained('lms_quizzes')->onDelete('cascade');
            $table->text('question_text');
            $table->string('question_type'); // multiple_choice, true_false, fill_blank, matching, short_answer
            $table->json('options')->nullable();
            $table->json('correct_answer');
            $table->integer('points')->default(1);
            $table->text('explanation')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_quiz_questions');
    }
};
