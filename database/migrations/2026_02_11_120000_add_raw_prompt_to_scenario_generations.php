<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scenario_generations', function (Blueprint $table) {
            if (! Schema::hasColumn('scenario_generations', 'raw_prompt')) {
                $table->text('raw_prompt')->nullable()->after('prompt');
            }
        });
    }

    public function down(): void
    {
        Schema::table('scenario_generations', function (Blueprint $table) {
            if (Schema::hasColumn('scenario_generations', 'raw_prompt')) {
                $table->dropColumn('raw_prompt');
            }
        });
    }
};
