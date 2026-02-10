<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('scenario_generations', function (Blueprint $table) {
            $table->unsignedBigInteger('last_validation_issue_id')->nullable()->after('compacted_by')->index();
        });
    }

    public function down(): void
    {
        Schema::table('scenario_generations', function (Blueprint $table) {
            $table->dropColumn('last_validation_issue_id');
        });
    }
};
