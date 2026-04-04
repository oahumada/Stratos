<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_video_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('enrollment_id')->constrained('lms_enrollments')->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained('lms_lessons')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('provider'); // youtube, vimeo, loom
            $table->string('video_id');
            $table->integer('duration_seconds')->default(0);
            $table->integer('watched_seconds')->default(0);
            $table->integer('last_position')->default(0);
            $table->boolean('completed')->default(false);
            $table->decimal('completion_threshold', 3, 2)->default(0.90);
            $table->timestamps();

            $table->unique(['enrollment_id', 'lesson_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_video_trackings');
    }
};
