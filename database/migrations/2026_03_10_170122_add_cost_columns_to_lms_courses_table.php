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
            $table->decimal('cost_per_seat', 10, 2)->default(0)->after('estimated_duration_minutes');
            $table->string('currency', 3)->default('USD')->after('cost_per_seat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_courses', function (Blueprint $table) {
            $table->dropColumn(['cost_per_seat', 'currency']);
        });
    }
};
