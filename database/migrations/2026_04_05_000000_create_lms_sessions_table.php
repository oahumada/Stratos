<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('lms_courses')->cascadeOnDelete();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('session_type'); // in_person, virtual, hybrid
            $table->string('location')->nullable();
            $table->string('meeting_url')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('timezone')->default('UTC');
            $table->integer('max_attendees')->nullable();
            $table->boolean('is_recording_available')->default(false);
            $table->string('recording_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_sessions');
    }
};
