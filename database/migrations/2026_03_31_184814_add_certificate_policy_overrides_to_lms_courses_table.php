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
        Schema::table('lms_courses', function (Blueprint $table) {
            $table->decimal('cert_min_resource_completion_ratio', 5, 4)->nullable()->after('xp_points');
            $table->boolean('cert_require_assessment_score')->nullable()->after('cert_min_resource_completion_ratio');
            $table->decimal('cert_min_assessment_score', 5, 2)->nullable()->after('cert_require_assessment_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_courses', function (Blueprint $table) {
            $table->dropColumn([
                'cert_min_resource_completion_ratio',
                'cert_require_assessment_score',
                'cert_min_assessment_score',
            ]);
        });
    }
};
