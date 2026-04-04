<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_compliance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_enrollment_id')->constrained('lms_enrollments')->onDelete('cascade');
            $table->foreignId('lms_course_id')->constrained('lms_courses')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->date('due_date');
            $table->date('completed_date')->nullable();
            $table->string('status')->default('pending');
            $table->date('recertification_due_date')->nullable();
            $table->integer('escalation_level')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'status']);
            $table->index(['user_id', 'lms_course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_compliance_records');
    }
};
