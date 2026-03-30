<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scenarios', function (Blueprint $table) {
            if (! Schema::hasColumn('scenarios', 'budget')) {
                $table->bigInteger('budget')->nullable()->after('strategic_context');
            }
            if (! Schema::hasColumn('scenarios', 'timeline_weeks')) {
                $table->integer('timeline_weeks')->nullable()->after('budget');
            }
        });
    }

    public function down(): void
    {
        Schema::table('scenarios', function (Blueprint $table) {
            if (Schema::hasColumn('scenarios', 'budget')) {
                $table->dropColumn('budget');
            }
            if (Schema::hasColumn('scenarios', 'timeline_weeks')) {
                $table->dropColumn('timeline_weeks');
            }
        });
    }
};
