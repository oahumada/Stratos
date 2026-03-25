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
        $tables = [
            'applications',
            'job_openings',
            'evaluations',
            'llm_evaluations',
            'px_campaigns',
            'pulse_surveys',
            'employee_pulses',
            'pulse_responses',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->text('digital_signature')->nullable();
                    $table->timestamp('signed_at')->nullable();
                    $table->string('signature_version')->nullable();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'applications',
            'job_openings',
            'evaluations',
            'llm_evaluations',
            'px_campaigns',
            'pulse_surveys',
            'employee_pulses',
            'pulse_responses',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn(['digital_signature', 'signed_at', 'signature_version']);
                });
            }
        }
    }
};
