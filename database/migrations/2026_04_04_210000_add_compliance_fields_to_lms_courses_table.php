<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lms_courses', function (Blueprint $table) {
            $table->boolean('is_compliance')->default(false)->after('is_active');
            $table->integer('compliance_deadline_days')->nullable()->after('is_compliance');
            $table->integer('recertification_interval_months')->nullable()->after('compliance_deadline_days');
            $table->string('compliance_category')->nullable()->after('recertification_interval_months');
        });
    }

    public function down(): void
    {
        Schema::table('lms_courses', function (Blueprint $table) {
            $table->dropColumn([
                'is_compliance',
                'compliance_deadline_days',
                'recertification_interval_months',
                'compliance_category',
            ]);
        });
    }
};
