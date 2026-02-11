<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PromptInstruction;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PromptInstructionController extends Controller
{
    public function index(Request $request)
    {
        $language = $request->query('language', 'es');
        // If migrations haven't been run the table may not exist; avoid throwing SQL exceptions.
        if (! Schema::hasTable('prompt_instructions')) {
            $items = collect();
        } else {
            // Prefer to return at most two entries per language:
            // - latest editable (if exists)
            // - latest immutable default (editable = false)
            $editable = PromptInstruction::where('language', $language)->where('editable', true)->orderBy('created_at', 'desc')->first();
            $immutable = PromptInstruction::where('language', $language)->where('editable', false)->orderBy('created_at', 'desc')->first();

            $items = collect();
            if ($editable) $items->push($editable);
            if ($immutable) $items->push($immutable);
        }

        // If no items in DB, provide a code-backed default from resources/prompt_instructions
        if ($items->isEmpty()) {
            try {
                // Primary expected location
                $path = resource_path('prompt_instructions/default_' . $language . '.md');

                // Legacy/alternate templates used by some wizards live under prompt_templates
                $alt1 = resource_path('prompt_templates/abacus_modal_prompt_' . $language . '.md');
                $alt2 = resource_path('prompt_templates/abacus_modal_prompt_' . substr($language, 0, 2) . '.md');
                $alt3 = resource_path('prompt_templates/abacus_modal_prompt_' . $language . '.MD');

                // Prefer prompt_templates alternatives when present
                if (file_exists($alt1)) {
                    $path = $alt1;
                } elseif (file_exists($alt2)) {
                    $path = $alt2;
                } elseif (file_exists($alt3)) {
                    $path = $alt3;
                } elseif (! file_exists($path)) {
                    // fallback to english then spanish under prompt_instructions
                    $pathEn = resource_path('prompt_instructions/default_en.md');
                    $pathEs = resource_path('prompt_instructions/default_es.md');
                    if (file_exists($pathEn)) $path = $pathEn;
                    elseif (file_exists($pathEs)) $path = $pathEs;
                }

                if (file_exists($path)) {
                    $content = file_get_contents($path);
                    $item = [
                        'id' => null,
                        'language' => $language,
                        'content' => $content,
                        'editable' => false,
                        'created_by' => null,
                        'author_name' => 'system:file',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Audit/log usage of the file fallback with minimal context
                    try {
                        $user = $request->user();
                        Log::info('PromptInstructionController: using file fallback', [
                            'language' => $language,
                            'user_id' => $user ? $user->id : null,
                            'user_email' => $user ? ($user->email ?? null) : null,
                            'ip' => $request->ip(),
                            'path' => $path,
                        ]);
                    } catch (\Throwable $e) {
                        // non-fatal; don't block response
                        Log::warning('Failed to log fallback usage: '.$e->getMessage());
                    }

                    return response()->json(['success' => true, 'data' => [$item]]);
                }
            } catch (\Throwable $e) {
                Log::error('Error loading default prompt instruction file: '.$e->getMessage());
            }
        }

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', PromptInstruction::class);
        if (! Schema::hasTable('prompt_instructions')) {
            return response()->json(['success' => false, 'message' => 'Database table prompt_instructions does not exist. Run migrations.'], 500);
        }
        $this->validate($request, [
            'language' => 'required|string|max:10',
            'content' => 'required|string',
            'editable' => 'sometimes|boolean',
        ]);

        $user = $request->user();

        $item = PromptInstruction::create([
            'language' => $request->input('language', 'es'),
            'content' => $request->input('content'),
            'editable' => (bool) $request->input('editable', true),
            'created_by' => $user ? $user->id : null,
            'author_name' => $user ? ($user->name ?? $user->email ?? null) : null,
        ]);

        return response()->json(['success' => true, 'data' => $item], 201);
    }

    public function show($id)
    {
        // viewing is allowed for authenticated users; controller-level check optional
        if (! Schema::hasTable('prompt_instructions')) {
            return response()->json(['success' => false, 'message' => 'Database table prompt_instructions does not exist. Run migrations.'], 500);
        }
        $item = PromptInstruction::find($id);
        if (! $item) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $item]);
    }

    public function update(Request $request, $id)
    {
        if (! Schema::hasTable('prompt_instructions')) {
            return response()->json(['success' => false, 'message' => 'Database table prompt_instructions does not exist. Run migrations.'], 500);
        }
        $item = PromptInstruction::find($id);
        if (! $item) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        $this->authorize('update', $item);

        $this->validate($request, [
            'content' => 'sometimes|string',
            'editable' => 'sometimes|boolean',
        ]);

        if ($request->has('content')) $item->content = $request->input('content');
        if ($request->has('editable')) $item->editable = (bool) $request->input('editable');
        $item->save();

        return response()->json(['success' => true, 'data' => $item]);
    }

    /**
     * Restore the default (immutable) instruction by cloning it into a new editable entry.
     */
    public function restoreDefault(Request $request)
    {
        $this->authorize('restore', PromptInstruction::class);
        if (! Schema::hasTable('prompt_instructions')) {
            return response()->json(['success' => false, 'message' => 'Database table prompt_instructions does not exist. Run migrations.'], 500);
        }
        $language = $request->input('language', 'es');

        $default = PromptInstruction::where('language', $language)
            ->where('editable', false)
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $default) {
            return response()->json(['success' => false, 'message' => 'Default instruction not found'], 404);
        }

        $user = $request->user();

        $clone = PromptInstruction::create([
            'language' => $default->language,
            'content' => $default->content,
            'editable' => true,
            'created_by' => $user ? $user->id : null,
            'author_name' => $user ? ($user->name ?? $user->email ?? null) : 'system',
        ]);

        return response()->json(['success' => true, 'data' => $clone], 201);
    }
}
