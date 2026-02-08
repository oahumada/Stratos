<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scenarios', function (Blueprint $table) {
            if (! Schema::hasColumn('scenarios', 'source_generation_id')) {
                $table->foreignId('source_generation_id')->nullable()->constrained('scenario_generations')->nullOnDelete()->after('parent_id');
            }
            if (! Schema::hasColumn('scenarios', 'accepted_prompt')) {
                $table->text('accepted_prompt')->nullable()->after('scope_notes');
            }
            if (! Schema::hasColumn('scenarios', 'accepted_prompt_redacted')) {
                $table->boolean('accepted_prompt_redacted')->default(true)->after('accepted_prompt');
            }
            if (! Schema::hasColumn('scenarios', 'accepted_prompt_metadata')) {
                $table->json('accepted_prompt_metadata')->nullable()->after('accepted_prompt_redacted');
            }

            $table->index(['source_generation_id']);
        });
    }

    public function down(): void
    {
        Schema::table('scenarios', function (Blueprint $table) {
            if (Schema::hasColumn('scenarios', 'source_generation_id')) {
                $table->dropForeign(['source_generation_id']);
                $table->dropColumn('source_generation_id');
            }
            if (Schema::hasColumn('scenarios', 'accepted_prompt')) {
                $table->dropColumn('accepted_prompt');
            }
            if (Schema::hasColumn('scenarios', 'accepted_prompt_redacted')) {
                $table->dropColumn('accepted_prompt_redacted');
            }
            if (Schema::hasColumn('scenarios', 'accepted_prompt_metadata')) {
                $table->dropColumn('accepted_prompt_metadata');
            }
        });
    }
};
