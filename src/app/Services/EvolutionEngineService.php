<?php

// app/Services/EvolutionEngine.php

namespace App\Services;

use App\Models\DevelopmentPath;
use App\Models\Evaluation;
use App\Repository\EvaluationRepository;

class EvolutionEngineService
{
    public function __construct(
        private EvaluationRepository $evaluationRepo
    ) {}

    /**
     * Calcula el nivel actual (N) basado en las respuestas 360°
     * Usa medianas ponderadas según el rol del evaluador
     */
    public function calculateCurrentLevel(Evaluation $evaluation): float
    {
        $responses = $evaluation->responses;

        if ($responses->isEmpty()) {
            return 0;
        }

        // Ponderaciones por rol
        $weights = [
            'manager' => 0.40,
            'peer' => 0.30,
            'subordinate' => 0.20,
            'self' => 0.10,
        ];

        $weightedSum = 0;
        $totalWeight = 0;

        foreach ($responses->groupBy('evaluator_role') as $role => $roleResponses) {
            $median = $this->calculateMedian(
                $roleResponses->pluck('barsLevel.level')->toArray()
            );

            $weight = $weights[$role] ?? 0;
            $weightedSum += $median * $weight;
            $totalWeight += $weight;
        }

        return $totalWeight > 0 ? round($weightedSum / $totalWeight, 2) : 0;
    }

    /**
     * Calcula la confianza basada en la consistencia de las respuestas
     */
    public function calculateConfidence(Evaluation $evaluation): int
    {
        $responses = $evaluation->responses;

        if ($responses->count() < 3) {
            return 30; // Baja confianza si hay pocas respuestas
        }

        $levels = $responses->pluck('barsLevel.level')->toArray();
        $stdDev = $this->standardDeviation($levels);

        // A menor desviación, mayor confianza
        $confidence = max(0, 100 - ($stdDev * 25));

        return (int) $confidence;
    }

    /**
     * Sugiere acciones según el gap
     */
    public function suggestActions(float $gap, string $skillType): array
    {
        $actions = [];

        if ($gap <= 0) {
            return [['strategy' => 'maintain', 'title' => 'Mantener nivel actual']];
        }

        if ($gap == 1) {
            $actions[] = [
                'strategy' => 'build',
                'title' => 'Micro-learning o curso corto',
                'estimated_weeks' => 4,
                'estimated_impact' => 10,
            ];
        } elseif ($gap == 2) {
            $actions[] = [
                'strategy' => 'build',
                'title' => 'Curso técnico + Certificación',
                'estimated_weeks' => 8,
                'estimated_impact' => 18,
            ];
        } elseif ($gap == 3) {
            $actions[] = [
                'strategy' => 'build',
                'title' => 'Proyecto con Mentoría',
                'estimated_weeks' => 12,
                'estimated_impact' => 22,
            ];
            $actions[] = [
                'strategy' => 'buy',
                'title' => 'Contratar especialista',
                'estimated_weeks' => 8,
                'estimated_impact' => 15,
            ];
        } else {
            $actions[] = [
                'strategy' => 'buy',
                'title' => 'Contratar expertos externos',
                'estimated_weeks' => 4,
                'estimated_impact' => 25,
            ];
            $actions[] = [
                'strategy' => 'borrow',
                'title' => 'Consultoría externa',
                'estimated_weeks' => 2,
                'estimated_impact' => 12,
            ];
        }

        return $actions;
    }

    /**
     * Crea un DevelopmentPath y sus DevelopmentActions según el gap detectado
     */
    public function createPathForGap(Evaluation $evaluation)
    {
        $gap = (int) ceil($evaluation->gap ?? 0);

        $path = DevelopmentPath::create([
            'evaluation_id' => $evaluation->id,
            'name' => 'Ruta de Evolución: '.($evaluation->skill->name ?? 'Skill'),
            'target_level' => $evaluation->required_level ?? null,
        ]);

        if ($gap >= 1) {
            $path->actions()->create([
                'title' => 'Formación Teórica (Build)',
                'type' => 'training',
                'order' => 1,
                'impact_weight' => 0.3,
            ]);
        }

        if ($gap >= 2) {
            $path->actions()->create([
                'title' => 'Laboratorio de Aplicación Práctica',
                'type' => 'practice',
                'order' => 2,
                'impact_weight' => 0.3,
            ]);
        }

        if ($gap >= 3) {
            $path->actions()->create([
                'title' => 'Proyecto de Implementación Estratégica',
                'type' => 'project',
                'order' => 3,
                'impact_weight' => 0.4,
            ]);
        }

        return $path->load('actions');
    }

    // Helpers matemáticos
    private function calculateMedian(array $numbers): float
    {
        sort($numbers);
        $count = count($numbers);
        $middle = floor($count / 2);

        if ($count % 2 == 0) {
            return ($numbers[$middle - 1] + $numbers[$middle]) / 2;
        }

        return $numbers[$middle];
    }

    private function standardDeviation(array $numbers): float
    {
        $mean = array_sum($numbers) / count($numbers);
        $variance = array_sum(array_map(fn ($x) => pow($x - $mean, 2), $numbers)) / count($numbers);

        return sqrt($variance);
    }
}
