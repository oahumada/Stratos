<?php

namespace App\Services;

use App\Models\PxCampaign;
use App\Models\PulseSurvey;
use App\Models\Organization;
use Illuminate\Support\Facades\Log;

class PxService
{
    /**
     * Templates for different organizational events.
     */
    protected array $templates = [
        'scenario.finalized' => [
            'name' => 'Strategic Alignment Survey',
            'description' => 'Measuring sentiment and understanding of the new organizational strategy.',
            'topics' => ['Alignment', 'Optimism', 'Clarity'],
            'questions' => [
                ['id' => 'q1', 'text' => '¿Qué tan alineado te sientes con la nueva visión estratégica?', 'type' => 'scale_5'],
                ['id' => 'q2', 'text' => '¿Sientes que tus habilidades actuales son suficientes para los nuevos retos?', 'type' => 'scale_5'],
                ['id' => 'q3', 'text' => '¿Qué es lo que más te motiva del plan propuesto?', 'type' => 'text'],
            ],
        ],
        'crisis.attrition' => [
            'name' => 'Organizational Stability Pulse',
            'description' => 'Proactive survey after a high-risk attrition simulation.',
            'topics' => ['Security', 'Belonging', 'Support'],
            'questions' => [
                ['id' => 'q1', 'text' => '¿Sientes que cuentas con el apoyo necesario en momentos de alta rotación?', 'type' => 'scale_5'],
                ['id' => 'q2', 'text' => '¿Qué tan seguro te sientes sobre el futuro del equipo en los próximos 6 meses?', 'type' => 'scale_5'],
                ['id' => 'q3', 'text' => '¿Hay algo que la organización pueda hacer para aumentar tu compromiso hoy?', 'type' => 'text'],
            ],
        ],
        'merger.restructuring' => [
            'name' => 'Cultural Integration Pulse',
            'description' => 'Survey to detect cultural friction during restructuring.',
            'topics' => ['Integration', 'Communication', 'Justice'],
            'questions' => [
                ['id' => 'q1', 'text' => '¿Cómo evalúas la transparencia de la comunicación en este proceso?', 'type' => 'scale_5'],
                ['id' => 'q2', 'text' => '¿Te sientes valorado equitativamente en la nueva estructura?', 'type' => 'scale_5'],
                ['id' => 'q3', 'text' => '¿Cuál es tu mayor preocupación respecto al cambio cultural?', 'type' => 'text'],
            ],
        ],
    ];

    /**
     * Triggers a PX Campaign based on an event.
     */
    public function triggerEventCampaign(int $organizationId, string $eventType): ?PxCampaign
    {
        if (!isset($this->templates[$eventType])) {
            Log::warning("PX Service: No template found for event type: {$eventType}");
            return null;
        }

        $template = $this->templates[$eventType];

        return \DB::transaction(function () use ($organizationId, $eventType, $template) {
            // 1. Create the Campaign
            $campaign = PxCampaign::create([
                'organization_id' => $organizationId,
                'name' => $template['name'] . ' - ' . now()->format('Y-m-d'),
                'description' => $template['description'],
                'mode' => 'automatic',
                'topics' => $template['topics'],
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addDays(7),
                'created_by' => auth()->id() ?? 1, // Fallback to system user
            ]);

            // 2. Create the associated Pulse Survey
            $survey = PulseSurvey::create([
                'title' => $template['name'],
                'type' => 'event_driven',
                'questions' => $template['questions'],
                'is_active' => true,
                'ai_report' => [
                    'event_trigger' => $eventType,
                    'campaign_id' => $campaign->id
                ]
            ]);

            Log::info("PX Service: Triggered campaign '{$campaign->name}' for event '{$eventType}'");

            return $campaign;
        });
    }

    /**
     * Lists active campaigns for an organization.
     */
    public function getActiveCampaigns(int $organizationId)
    {
        return PxCampaign::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->latest()
            ->get();
    }
}
