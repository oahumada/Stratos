<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lms_scorm_packages', function (Blueprint $table) {
            $table->json('sequencing_rules')->nullable()->after('manifest_data');
        });

        Schema::table('lms_scorm_tracking', function (Blueprint $table) {
            $table->decimal('progress_measure', 5, 4)->nullable()->after('lesson_location');
            $table->decimal('scaled_score', 5, 4)->nullable()->after('progress_measure');
            $table->string('success_status')->nullable()->after('scaled_score');
            $table->decimal('completion_threshold', 5, 4)->nullable()->after('success_status');
        });
    }

    public function down(): void
    {
        Schema::table('lms_scorm_packages', function (Blueprint $table) {
            $table->dropColumn('sequencing_rules');
        });

        Schema::table('lms_scorm_tracking', function (Blueprint $table) {
            $table->dropColumn(['progress_measure', 'scaled_score', 'success_status', 'completion_threshold']);
        });
    }
};
