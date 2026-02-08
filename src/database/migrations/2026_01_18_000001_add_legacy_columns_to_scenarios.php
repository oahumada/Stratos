<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scenarios', function (Blueprint $table) {
            if (! Schema::hasColumn('scenarios', 'scenario_type')) {
                $table->string('scenario_type')->nullable();
            }
            if (! Schema::hasColumn('scenarios', 'time_horizon_weeks')) {
                $table->integer('time_horizon_weeks')->nullable();
            }
            if (! Schema::hasColumn('scenarios', 'assumptions')) {
                $table->json('assumptions')->nullable();
            }
            if (! Schema::hasColumn('scenarios', 'custom_config')) {
                $table->text('custom_config')->nullable();
            }
            if (! Schema::hasColumn('scenarios', 'estimated_budget')) {
                $table->bigInteger('estimated_budget')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('scenarios', function (Blueprint $table) {
            $table->dropColumn(['scenario_type', 'time_horizon_weeks', 'assumptions', 'custom_config', 'estimated_budget']);
        });
    }
};
