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
        Schema::table('lms_enrollments', function (Blueprint $table) {
            $table->unsignedInteger('resources_completed')->default(0)->after('progress_percentage');
            $table->unsignedInteger('resources_total')->default(0)->after('resources_completed');
            $table->decimal('assessment_score', 5, 2)->nullable()->after('resources_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_enrollments', function (Blueprint $table) {
            $table->dropColumn([
                'resources_completed',
                'resources_total',
                'assessment_score',
            ]);
        });
    }
};
