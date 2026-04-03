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
        Schema::table('lms_courses', function (Blueprint $table): void {
            $table->foreignId('cert_template_id')
                ->nullable()
                ->after('cert_min_assessment_score')
                ->constrained('lms_certificate_templates')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_courses', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('cert_template_id');
        });
    }
};
