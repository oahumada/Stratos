#!/bin/bash

GEN_ID=$1

if [ -z "$GEN_ID" ]; then
    echo "Usage: $0 <generation_id>"
    exit 1
fi

echo "=========================================================="
echo "🚀 STRATOS: FINALIZING GENERATION TO SCENARIO"
echo "Generation ID: $GEN_ID"
echo "=========================================================="

php artisan tinker --execute="
\$gen = App\Models\ScenarioGeneration::find($GEN_ID);
if (!\$gen) {
    echo \"❌ Generation not found.\n\";
    exit(1);
}

\$service = app(App\Services\ScenarioGenerationService::class);
echo \"Step 1: Importing entities (Capabilities, Competencies, Skills)...\n\";
\$result = \$service->finalizeScenarioImport(\$gen);

if (\$result['success']) {
    echo \"✅ SUCCESS!\n\";
    echo \"Scenario ID created: \" . \$result['scenario_id'] . \"\n\";
    echo \"Stats: \" . json_encode(\$result['stats']) . \"\n\";
} else {
    echo \"❌ FAILED to import.\n\";
}
"
