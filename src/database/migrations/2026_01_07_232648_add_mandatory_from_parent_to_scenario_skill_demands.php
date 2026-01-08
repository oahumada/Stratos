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
        Schema::table('scenario_skill_demands', function (Blueprint $table) {
            // Indica si esta demanda fue heredada del escenario padre
            if (!Schema::hasColumn('scenario_skill_demands', 'is_mandatory_from_parent')) {
                $table->boolean('is_mandatory_from_parent')->default(false)
                    ->after('rationale')
                    ->comment('Heredado del escenario padre - no se puede eliminar');
                $table->index('is_mandatory_from_parent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scenario_skill_demands', function (Blueprint $table) {
            $table->dropIndex(['is_mandatory_from_parent']);
            $table->dropColumn('is_mandatory_from_parent');
        });
    }
};
