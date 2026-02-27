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
        Schema::table('assessment_requests', function (Blueprint $table) {
            $table->foreignId('assessment_cycle_id')->nullable()->after('organization_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_requests', function (Blueprint $table) {
            $table->dropForeign(['assessment_cycle_id']);
            $table->dropColumn('assessment_cycle_id');
        });
    }
};
