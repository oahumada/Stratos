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
        Schema::table('talent_blueprints', function (Blueprint $table) {
            if (!Schema::hasColumn('talent_blueprints', 'role_description')) {
                $table->text('role_description')->nullable()->after('role_name');
            }
            if (!Schema::hasColumn('talent_blueprints', 'key_competencies')) {
                $table->json('key_competencies')->nullable()->after('role_description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('talent_blueprints', function (Blueprint $table) {
            $table->dropColumn(['role_description', 'key_competencies']);
        });
    }
};
