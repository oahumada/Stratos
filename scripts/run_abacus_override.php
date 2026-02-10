<?php
// scripts/run_abacus_override.php
// Helper wrapper to run the existing harness `scripts/generate_via_abacus.php` using
// the modal prompt template or a provided prompt file. Supports timeouts and
// optional background execution.

$opts = getopt('', [
    'prompt-file::',
    'prompt::',
    'company-name::',
    'model::',
    'timeout::',
    'stream_idle_timeout::',
    'background::',
    'help::',
]);

if (isset($opts['help'])) {
    echo "Usage: php scripts/run_abacus_override.php [--prompt-file=path] [--prompt='text'] [--company-name='ACME'] [--model=gpt-5] [--timeout=300] [--stream_idle_timeout=240] [--background]\n";
    exit(0);
}

// Determine prompt source
$prompt = null;
if (!empty($opts['prompt'])) {
    $prompt = $opts['prompt'];
} elseif (!empty($opts['prompt-file'])) {
    $pf = $opts['prompt-file'];
    if (!file_exists($pf)) {
        fwrite(STDERR, "Prompt file not found: $pf\n");
        exit(2);
    }
    $prompt = file_get_contents($pf);
} else {
    // default template shipped in repo
    $templatePath = __DIR__ . '/../resources/prompt_templates/abacus_modal_prompt_es.md';
    if (file_exists($templatePath)) {
        $prompt = file_get_contents($templatePath);
    } else {
        $prompt = "Genera un escenario de prueba en JSON con keys scenario_metadata y capabilities";
    }
}

// Replace tokens
$company = $opts['company-name'] ?? 'ACME';
$prompt = str_replace('{{company_name}}', $company, $prompt);

$model = $opts['model'] ?? getenv('ABACUS_MODEL') ?: 'gpt-5';
$timeout = (int) ($opts['timeout'] ?? 300);
$streamIdle = (int) ($opts['stream_idle_timeout'] ?? 240);
$background = isset($opts['background']);

// Build command to invoke existing harness
$script = __DIR__ . '/generate_via_abacus.php';
if (!file_exists($script)) {
    fwrite(STDERR, "Harness script not found: $script\n");
    exit(3);
}

$escapedPrompt = escapeshellarg($prompt);
$cmd = "php " . escapeshellarg($script) . " --prompt=$escapedPrompt --model=" . escapeshellarg($model) . " --timeout=" . escapeshellarg((string)$timeout) . " --stream_idle_timeout=" . escapeshellarg((string)$streamIdle);

if ($background) {
    // send to background and capture PID
    $log = '/tmp/abacus_run.log';
    $bgCmd = $cmd . " > " . escapeshellarg($log) . " 2>&1 & echo $!";
    $pid = trim(shell_exec($bgCmd));
    echo "Started background run. PID={$pid}, log={$log}\n";
    exit(0);
}

// Run in foreground and stream output
passthru($cmd, $exit);
exit($exit);
