<?php

namespace Database\Seeders;

use App\Models\LLMEvaluation;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;

class RAGASEvaluationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Get or create a target organization and user for these evaluations
        $org = Organization::first() ?? Organization::factory()->create(['name' => 'Stratos Demo Org']);
        $user = User::where('organization_id', $org->id)->first() ?? User::factory()->create([
            'organization_id' => $org->id,
            'name' => 'Demo User',
            'email' => 'demo@stratos.ai',
        ]);

        $this->command->info("Seeding evaluations for Org: {$org->name} (ID: {$org->id})");

        // 2. Generate varied evaluations per provider to populate the provider comparision chart
        $providers = ['deepseek', 'abacus', 'openai', 'intel'];

        foreach ($providers as $provider) {
            $this->command->info("Generating evaluations for provider: {$provider}");

            // Generate a mix of quality levels
            // Excellent (15%)
            LLMEvaluation::factory()->count(3)->excellent()->provider($provider)->create([
                'organization_id' => $org->id,
                'created_by' => $user->id,
            ]);

            // Good (40%)
            LLMEvaluation::factory()->count(8)->good()->provider($provider)->create([
                'organization_id' => $org->id,
                'created_by' => $user->id,
            ]);

            // Acceptable (25%)
            LLMEvaluation::factory()->count(5)->acceptable()->provider($provider)->create([
                'organization_id' => $org->id,
                'created_by' => $user->id,
            ]);

            // Poor (15%)
            LLMEvaluation::factory()->count(3)->poor()->provider($provider)->create([
                'organization_id' => $org->id,
                'created_by' => $user->id,
            ]);

            // Critical (5%)
            LLMEvaluation::factory()->count(1)->critical()->provider($provider)->create([
                'organization_id' => $org->id,
                'created_by' => $user->id,
            ]);
        }

        // 3. Add some failed/pending ones to show status variety
        LLMEvaluation::factory()->count(3)->pending()->create([
            'organization_id' => $org->id,
            'created_by' => $user->id,
        ]);

        LLMEvaluation::factory()->count(2)->failed()->create([
            'organization_id' => $org->id,
            'created_by' => $user->id,
        ]);

        $total = LLMEvaluation::where('organization_id', $org->id)->count();
        $this->command->info("Successfully seeded {$total} LLM evaluations in PostgreSQL.");
    }
}
