<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill scenarios.accepted_prompt* from linked scenario_generations when available
        if (! Schema::hasTable('scenarios') || ! Schema::hasTable('scenario_generations')) {
            return;
        }

        $gens = DB::table('scenario_generations')
            ->select('id', 'prompt', 'metadata', 'redacted')
            ->get();

        foreach ($gens as $g) {
            // Update scenario that references this generation
            DB::table('scenarios')
                ->where('source_generation_id', $g->id)
                ->where(function ($q) use ($g) {
                    $q->whereNull('accepted_prompt_metadata')
                      ->orWhereNull('accepted_prompt');
                })
                ->update([
                    'accepted_prompt_metadata' => $g->metadata,
                    'accepted_prompt' => DB::raw("COALESCE(accepted_prompt, '".addslashes($g->prompt)."')"),
                    'accepted_prompt_redacted' => $g->redacted ? 1 : 0,
                ]);
        }
    }

    public function down(): void
    {
        // No-op: do not remove backfilled provenance data automatically
    }
};
