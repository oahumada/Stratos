<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_scorm_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('lms_course_id')->nullable()->constrained('lms_courses')->onDelete('set null');
            $table->foreignId('lms_lesson_id')->nullable()->constrained('lms_lessons')->onDelete('set null');
            $table->string('title');
            $table->string('filename');
            $table->string('version')->default('1.2');
            $table->json('manifest_data')->nullable();
            $table->string('entry_point')->nullable();
            $table->string('identifier')->nullable();
            $table->string('storage_path');
            $table->string('status')->default('processing');
            $table->bigInteger('file_size_bytes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_scorm_packages');
    }
};
