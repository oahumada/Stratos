<?php

namespace App\Services\LLMProviders;

use Illuminate\Support\Arr;

class MockProvider implements LLMProviderInterface
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function generate(string $prompt): array
    {
        $now = now()->toIso8601String();

        // Allow tests to simulate rate-limits
        if (! empty($this->config['simulate_429'])) {
            $retry = isset($this->config['simulate_429_retry_after']) ? (int) $this->config['simulate_429_retry_after'] : null;
            throw new \App\Services\LLMProviders\Exceptions\LLMRateLimitException('Simulated rate limit', $retry);
        }

        // Smart Mock for "Crecimiento Agresivo" / "Aggressive Growth"
        if (stripos($prompt, 'Crecimiento Agresivo') !== false || stripos($prompt, 'Aggressive Growth') !== false || stripos($prompt, 'expansión') !== false) {
            $response = [
                'scenario_metadata' => [
                    'name' => 'Expansión Estratégica LATAM 2026',
                    'generated_at' => $now,
                    'confidence_score' => 0.88,
                    'assumptions' => [
                        'Disponibilidad de talento senior en arquitectura de nube',
                        'Estabilidad de marcos regulatorios de banca abierta',
                        'Adopción cultural de metodologías ágiles escaladas'
                    ],
                ],
                'capabilities' => [
                    [
                        'name' => 'Arquitectura de Banca Abierta', 
                        'description' => 'Capacidad de orquestar APIs financieras seguras y escalables.',
                        'competencies' => [
                            [
                                'name' => 'Diseño de Microservicios', 
                                'description' => 'Diseño de servicios desacoplados en la nube.',
                                'skills' => ['AWS Lambda', 'Kafka Streaming', 'Kubernetes Security']
                            ]
                        ]
                    ],
                    [
                        'name' => 'Inteligencia de Datos en Tiempo Real', 
                        'description' => 'Procesamiento de eventos para decisiones de crédito instantáneas.',
                        'competencies' => [
                            [
                                'name' => 'Machine Learning Engineering', 
                                'description' => 'Entrenamiento y despliegue de modelos predictivos.',
                                'skills' => ['Python for DS', 'TensorFlow', 'Feature Engineering']
                            ]
                        ]
                    ],
                    [
                        'name' => 'Agilidad Organizacional Escalada', 
                        'description' => 'Estructura de squads y tribus para despliegue continuo.',
                        'competencies' => [
                            [
                                'name' => 'DevSecOps', 
                                'description' => 'Integración de seguridad en el ciclo de vida de desarrollo.',
                                'skills' => ['CI/CD Pipelines', 'SAST/DAST Tooling', 'Infrastructure as Code']
                            ],
                            [
                                'name' => 'Liderazgo de Equipos Distribuidos', 
                                'description' => 'Gestión efectiva de equipos remotos en diferentes zonas horarias.',
                                'skills' => ['Kanban / Scrum', 'Remote Communication', 'Conflict Resolution']
                            ]
                        ]
                    ],
                ],
                'suggested_roles' => [
                    [
                        'name' => 'Arquitecto Cloud Senior', 
                        'role_type' => 'strategic', 
                        'complexity_level' => 'high',
                        'human_percentage' => 100,
                        'synthetic_percentage' => 0,
                        'strategic_justification' => 'Liderazgo técnico para la migración core.',
                        'key_competencies' => ['Diseño de Microservicios', 'Liderazgo de Equipos Distribuidos']
                    ],
                    [
                        'name' => 'Ingeniero de Plataforma AI', 
                        'role_type' => 'tactical', 
                        'complexity_level' => 'high',
                        'human_percentage' => 70,
                        'synthetic_percentage' => 30,
                        'strategic_justification' => 'Desarrollo de modelos de scoring predictivo.',
                        'key_competencies' => ['Machine Learning Engineering', 'Diseño de Microservicios']
                    ],
                    [
                        'name' => 'SRE Specialist', 
                        'role_type' => 'tactical', 
                        'complexity_level' => 'medium',
                        'human_percentage' => 50,
                        'synthetic_percentage' => 50,
                        'strategic_justification' => 'Automatización de observabilidad y resiliencia.',
                        'key_competencies' => ['DevSecOps', 'Diseño de Microservicios']
                    ],
                ],
                'impact_analysis' => [
                    'transformation_index' => 0.92,
                    'main_risks' => ['Escasez de talento senior', 'Resistencia cultural al cambio'],
                ],
            ];
        } else {
            // If 'response' is explicitly null in config, fall back to the default mock payload.
            $response = Arr::get($this->config, 'response') ?? [
                'scenario_metadata' => [
                    'generated_at' => $now,
                    'confidence_score' => 0.75,
                    'assumptions' => ['mock response for testing'],
                ],
                'capabilities' => [],
                'competencies' => [],
                'skills' => [],
                'suggested_roles' => [],
                'impact_analysis' => [],
            ];
        }

        return [
            'response' => $response,
            'confidence' => Arr::get($response, 'scenario_metadata.confidence_score', 0.75),
            'model_version' => Arr::get($this->config, 'model_version', 'smart-mock-2.0'),
        ];
    }

    /**
     * Simulate streamed generation by invoking $onDelta with small chunks.
     * Returns the same shape as generate().
     * This helps local/demo jobs persist chunks.
     *
     * @param string $prompt
     * @param callable $onDelta
     * @return array
     */
    public function generateStream(string $prompt, callable $onDelta): array
    {
        $res = $this->generate($prompt);
        $raw = $res['response'] ?? $res;
        $text = is_array($raw) || is_object($raw) ? json_encode($raw, JSON_UNESCAPED_UNICODE) : (string) $raw;

        // split into ~120 char chunks
        $len = strlen($text);
        $pos = 0;
        $chunkSize = 120;
        while ($pos < $len) {
            $part = substr($text, $pos, $chunkSize);
            try {
                $onDelta($part);
            } catch (\Throwable $e) {
                // ignore
            }
            $pos += $chunkSize;
            // small pause to mimic streaming
            usleep(50000); // 50ms
        }

        return $res;
    }
}
