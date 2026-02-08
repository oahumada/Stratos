<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrunePromptInstructionsSeeder extends Seeder
{
    /**
     * Run the database prune to keep at most one editable and one immutable per language.
     */
    public function run(): void
    {
        $languages = DB::table('prompt_instructions')->select('language')->distinct()->pluck('language');
        foreach ($languages as $lang) {
            // Keep latest editable
            $editable = DB::table('prompt_instructions')->where('language', $lang)->where('editable', true)->orderBy('created_at', 'desc')->pluck('id')->toArray();
            if (count($editable) > 1) {
                $keep = array_shift($editable);
                DB::table('prompt_instructions')->whereIn('id', $editable)->delete();
            }

            // Keep latest immutable
            $immutable = DB::table('prompt_instructions')->where('language', $lang)->where('editable', false)->orderBy('created_at', 'desc')->pluck('id')->toArray();
            if (count($immutable) > 1) {
                $keep = array_shift($immutable);
                DB::table('prompt_instructions')->whereIn('id', $immutable)->delete();
            }
        }
    }
}
