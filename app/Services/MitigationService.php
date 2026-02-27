<?php

namespace App\Services;

use App\Models\PsychometricProfile;
use App\Models\DevelopmentAction;
use App\Models\People;
use Illuminate\Support\Facades\Log;

class MitigationService
{
    /**
     * Analiza el perfil psicométrico y genera sugerencias de mitigación automáticas.
     */
    public function generateSuggestions(array $analysisData): array
    {
        $suggestions = [];
        $traits = $analysisData['traits'] ?? [];
        $blindSpots = $analysisData['blind_spots'] ?? [];
        $culturalFit = $analysisData['cultural_fit'] ?? 1.0;

        // 1. Mitigación por Rasgos Bajos (< 0.4)
        foreach ($traits as $trait) {
            if (($trait['score'] ?? 1.0) < 0.4) {
                $suggestions[] = [
                    'type' => 'training',
                    'title' => 'Refuerzo en ' . $trait['name'],
                    'description' => "Se detectó un nivel bajo en '{$trait['name']}'. Razón IA: {$trait['rationale']}. Se recomienda capacitación inmediata.",
                    'priority' => 'high'
                ];
            }
        }

        // 2. Mitigación por Blind Spots
        foreach ($blindSpots as $spot) {
            $suggestions[] = [
                'type' => 'mentorship',
                'title' => 'Sesión de Feedback: Puntos Ciegos',
                'description' => "La IA identificó la siguiente discrepancia de percepción: {$spot}. Se requiere una sesión de mentoría para alinear expectativas.",
                'priority' => 'medium'
            ];
        }

        // 3. Mitigación por Desalineación Cultural
        if ($culturalFit < 0.6) {
            $suggestions[] = [
                'type' => 'culture_immersion',
                'title' => 'Inmersión en Valores Stratos',
                'description' => "Alineación cultural baja ({$culturalFit}). Se sugiere taller de valores y repaso del Manifiesto Stratos.",
                'priority' => 'critical'
            ];
        }

        return $suggestions;
    }

    /**
     * Persiste las sugerencias como acciones de desarrollo.
     */
    public function persistMitigations(People $people, array $suggestions)
    {
        foreach ($suggestions as $s) {
            DevelopmentAction::create([
                'people_id' => $people->id,
                'organization_id' => $people->organization_id,
                'title' => $s['title'],
                'description' => $s['description'],
                'type' => $s['type'],
                'priority' => $s['priority'],
                'status' => 'suggested'
            ]);
        }
    }
}
