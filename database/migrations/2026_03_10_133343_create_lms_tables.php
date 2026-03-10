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
        Schema::create('lms_courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('category')->nullable();
            $table->string('level')->default('beginner'); // beginner, intermediate, advanced
            $table->integer('estimated_duration_minutes')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->timestamps();
        });

        Schema::create('lms_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_course_id')->constrained('lms_courses')->onDelete('cascade');
            $table->string('title');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('lms_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_module_id')->constrained('lms_modules')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('content_type')->default('video'); // video, pdf, quiz, markdown
            $table->string('content_url')->nullable();
            $table->text('content_body')->nullable();
            $table->integer('order')->default(0);
            $table->integer('duration_minutes')->default(0);
            $table->timestamps();
        });

        Schema::create('lms_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_course_id')->constrained('lms_courses')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->float('progress_percentage')->default(0);
            $table->string('status')->default('enrolled'); // enrolled, in_progress, completed
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_enrollments');
        Schema::dropIfExists('lms_lessons');
        Schema::dropIfExists('lms_modules');
        Schema::dropIfExists('lms_courses');
    }
};
