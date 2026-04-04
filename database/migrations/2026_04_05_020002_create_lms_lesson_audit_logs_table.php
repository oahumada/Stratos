<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_lesson_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('enrollment_id')->constrained('lms_enrollments')->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained('lms_lessons')->onDelete('cascade');
            $table->foreignId('module_id')->nullable()->constrained('lms_modules')->onDelete('set null');
            $table->string('action');
            $table->json('metadata')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['enrollment_id', 'lesson_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_lesson_audit_logs');
    }
};
