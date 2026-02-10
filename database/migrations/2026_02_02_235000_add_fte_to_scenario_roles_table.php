<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('scenario_roles', 'fte')) {
            Schema::table('scenario_roles', function (Blueprint $table) {
                $table->decimal('fte', 8, 2)->default(0)->after('role_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('scenario_roles', 'fte')) {
            Schema::table('scenario_roles', function (Blueprint $table) {
                $table->dropColumn('fte');
            });
        }
    }
};
