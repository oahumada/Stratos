<?php

// app/Services/OrchestrationService.php
namespace App\Services;

use App\Models\TalentBlueprint;
use Illuminate\Support\Facades\Http;

class OrchestrationService
{
    public function executeStrategy(TalentBlueprint $blueprint)
    {
        $webhookUrl = config('services.n8n.webhook_url');
        
        $payload = [
            'action' => $this->mapStrategyToAction($blueprint->strategy_suggestion),
            'role_name' => $blueprint->role_name,
            'estimated_fte' => $blueprint->estimated_fte,
            'agent_type' => $blueprint->suggested_agent_type,
            'competencies' => $blueprint->key_competencies,
        ];

        return Http::post($webhookUrl, $payload);
    }

    private function mapStrategyToAction(string $strategy): string
    {
        return match($strategy) {
            'Buy' => 'post_job_opening',
            'Build' => 'assign_training',
            'Borrow' => 'contact_staffing_agency',
            'Synthetic' => 'deploy_ai_agent',
            'Hybrid' => 'setup_hybrid_team',
            default => 'log_decision',
        };
    }
}