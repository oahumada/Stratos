<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scenario_capabilities', function (Blueprint $table) {
            // store per-scenario UI positions for capability nodes
            $table->double('position_x')->nullable()->after('is_critical');
            $table->double('position_y')->nullable()->after('position_x');
            $table->boolean('is_fixed')->default(false)->after('position_y');
        });
    }

    public function down(): void
    {
        Schema::table('scenario_capabilities', function (Blueprint $table) {
            $table->dropColumn(['position_x', 'position_y', 'is_fixed']);
        });
    }
};
