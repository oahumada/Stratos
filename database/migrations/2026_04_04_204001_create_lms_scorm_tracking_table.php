<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_scorm_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_scorm_package_id')->constrained('lms_scorm_packages')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('lms_enrollment_id')->nullable()->constrained('lms_enrollments')->onDelete('set null');
            $table->json('cmi_data')->nullable();
            $table->string('lesson_status')->default('not attempted');
            $table->float('score_raw')->nullable();
            $table->float('score_min')->nullable();
            $table->float('score_max')->nullable();
            $table->string('total_time')->default('0000:00:00');
            $table->integer('session_count')->default(0);
            $table->text('suspend_data')->nullable();
            $table->string('lesson_location')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['lms_scorm_package_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_scorm_tracking');
    }
};
