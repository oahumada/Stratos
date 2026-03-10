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
        Schema::table('roles', function (Blueprint $table) {
            $table->decimal('base_salary', 12, 2)->nullable()->after('level');
        });

        Schema::table('people', function (Blueprint $table) {
            $table->decimal('salary', 12, 2)->nullable()->after('is_high_potential');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('base_salary');
        });

        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn('salary');
        });
    }
};
