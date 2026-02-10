<?php
// Manual compacted endpoint tester
// Usage: php src/scripts/manual_compacted_test.php

$vendor = __DIR__ . '/../vendor/autoload.php';
if (! file_exists($vendor)) {
    echo "Cannot find vendor/autoload.php at $vendor\n";
    exit(1);
}
require $vendor;
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Organization;
use App\Models\User;
use App\Models\ScenarioGeneration;

// ensure organization
$org = Organization::firstOrCreate(['id' => 1], ['name' => 'local', 'subdomain' => 'local']);

// create user
$user = User::factory()->create(['organization_id' => $org->id]);
$token = $user->createToken('manual-test')->plainTextToken;

// create generation with compacted metadata
$payload = ['hello' => 'world', 'time' => now()->toDateTimeString()];
$compact = base64_encode(json_encode($payload));
$gen = ScenarioGeneration::create([
    'organization_id' => $org->id,
    'prompt' => 'manual compacted test',
    'status' => 'complete',
    'metadata' => ['compacted' => $compact],
]);

echo "TOKEN={$token}\n";
echo "GEN_ID={$gen->id}\n";

// optional: output curl command suggestion
$host = getenv('APP_URL') ?: 'http://localhost:8000';
$cmd = "curl -s -H 'Authorization: Bearer {$token}' '{$host}/api/strategic-planning/scenarios/generate/{$gen->id}/compacted'";
echo "\nRun:\n$cmd\n";
