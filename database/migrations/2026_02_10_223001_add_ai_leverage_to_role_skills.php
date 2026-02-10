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
        if (! Schema::hasTable('role_skills')) {
            return;
        }

        Schema::table('role_skills', function (Blueprint $table) {
            // Qué tanto de esta skill específica puede ser resuelta por IA (0-100)
            if (! Schema::hasColumn('role_skills', 'ai_leverage_score')) {
                $table->integer('ai_leverage_score')->default(0)->after('required_level');
            }

            // Cómo la IA ayuda en esta skill
            if (! Schema::hasColumn('role_skills', 'ai_integration_notes')) {
                $table->text('ai_integration_notes')->nullable()->after('ai_leverage_score');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('role_skills')) {
            return;
        }

        Schema::table('role_skills', function (Blueprint $table) {
            if (Schema::hasColumn('role_skills', 'ai_integration_notes')) {
                $table->dropColumn('ai_integration_notes');
            }

            if (Schema::hasColumn('role_skills', 'ai_leverage_score')) {
                $table->dropColumn('ai_leverage_score');
            }
        });
    }
};
