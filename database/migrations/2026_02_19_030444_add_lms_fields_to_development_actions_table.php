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
        Schema::table('development_actions', function (Blueprint $table) {
            $table->string('lms_course_id')->nullable()->after('type');
            $table->string('lms_enrollment_id')->nullable()->after('lms_course_id');
            $table->string('lms_provider')->nullable()->after('lms_enrollment_id'); // e.g., 'moodle', 'coursera', 'mock'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('development_actions', function (Blueprint $table) {
            $table->dropColumn(['lms_course_id', 'lms_enrollment_id', 'lms_provider']);
        });
    }
};
