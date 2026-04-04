<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_course_id')->nullable()->constrained('lms_courses')->nullOnDelete();
            $table->foreignId('lms_lesson_id')->nullable()->constrained('lms_lessons')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('body');
            $table->foreignId('parent_id')->nullable()->constrained('lms_discussions')->cascadeOnDelete();
            $table->integer('likes_count')->default(0);
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();

            $table->index(['organization_id', 'lms_course_id']);
            $table->index(['parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_discussions');
    }
};
