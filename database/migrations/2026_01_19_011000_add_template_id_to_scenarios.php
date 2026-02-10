<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('scenarios', 'template_id')) {
            Schema::table('scenarios', function (Blueprint $table) {
                $table->foreignId('template_id')->nullable()->constrained('scenario_templates')->nullOnDelete()->after('sponsor_user_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('scenarios', 'template_id')) {
            Schema::table('scenarios', function (Blueprint $table) {
                $table->dropConstrainedForeignId('template_id');
            });
        }
    }
};
