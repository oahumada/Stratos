<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_course_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_course_id')->constrained('lms_courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->integer('rating');
            $table->text('review')->nullable();
            $table->timestamps();

            $table->unique(['lms_course_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_course_ratings');
    }
};
