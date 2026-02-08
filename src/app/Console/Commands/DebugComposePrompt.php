<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ScenarioGenerationService;
use App\Models\User;
use App\Models\Organizations;

class DebugComposePrompt extends Command
{
    protected $signature = 'debug:compose-prompt {--payload=} {--file=} {--lang=es} {--instruction_id=}';
    protected $description = 'Compose prompt using ScenarioGenerationService for a given payload (JSON) and print it to stdout.';

    public function handle()
    {
        $payloadOption = $this->option('payload');
        $fileOption = $this->option('file');
        $lang = $this->option('lang') ?: 'es';
        $instructionId = $this->option('instruction_id') ?: null;

        if (empty($payloadOption) && empty($fileOption)) {
            $this->error('Provide --payload or --file with JSON payload.');
            return 1;
        }

        if (!empty($fileOption)) {
            if (!file_exists($fileOption)) {
                $this->error('File not found: '.$fileOption);
                return 2;
            }
            $payloadJson = @file_get_contents($fileOption);
        } else {
            $payloadJson = $payloadOption;
        }

        $data = @json_decode($payloadJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON payload: '.json_last_error_msg());
            return 3;
        }

        $user = User::first();
        $org = Organizations::first();
        if (! $user || ! $org) {
            $this->warn('No User or Organizations found in DB. Using null placeholders.');
        }

        $svc = new ScenarioGenerationService();
        $result = $svc->composePromptWithInstruction($data, $user, $org, $lang, $instructionId ? (int)$instructionId : null);

        $this->line("---- COMPOSED PROMPT ----\n");
        $this->line($result['prompt']);
        $this->line("\n---- INSTRUCTION METADATA ----\n");
        $this->line(json_encode($result['instruction'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return 0;
    }
}
