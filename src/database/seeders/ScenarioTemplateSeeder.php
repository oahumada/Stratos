<?php

namespace Database\Seeders;

use App\Models\ScenarioTemplate;
use Illuminate\Database\Seeder;

class ScenarioTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'IA Adoption Accelerator',
                'slug' => 'ia-adoption-accelerator',
                'description' => 'Plan para acelerar la adopción de inteligencia artificial en la organización',
                'scenario_type' => 'transformation',
                'industry' => 'general',
                'icon' => 'mdi-robot',
                'config' => [
                    'predefined_skills' => [
                        [
                            'skill_id' => 1,
                            'required_headcount' => 5,
                            'required_level' => 4,
                            'priority' => 'critical',
                            'rationale' => 'AI/ML engineers necesarios para proyectos iniciales'
                        ],
                        [
                            'skill_id' => 2,
                            'required_headcount' => 15,
                            'required_level' => 3,
                            'priority' => 'high',
                            'rationale' => 'Data analysis para soporte de decisiones IA'
                        ],
                    ],
                    'suggested_strategies' => ['build', 'buy', 'bind'],
                    'kpis' => ['AI talent coverage', 'Time to AI first project', 'Training hours per employee'],
                    'assumptions' => [
                        'Budget disponible: $500k-$1M',
                        'Timeline: 12-18 meses',
                        'Retención esperada: 90%'
                    ]
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Digital Transformation',
                'slug' => 'digital-transformation',
                'description' => 'Estrategia integral de transformación digital',
                'scenario_type' => 'transformation',
                'industry' => 'general',
                'icon' => 'mdi-cloud-sync',
                'config' => [
                    'predefined_skills' => [
                        [
                            'skill_id' => 3,
                            'required_headcount' => 10,
                            'required_level' => 4,
                            'priority' => 'critical',
                            'rationale' => 'Cloud architects para infraestructura'
                        ],
                        [
                            'skill_id' => 4,
                            'required_headcount' => 20,
                            'required_level' => 3,
                            'priority' => 'high',
                            'rationale' => 'Full-stack developers para modernización'
                        ],
                    ],
                    'suggested_strategies' => ['build', 'buy', 'bridge'],
                    'kpis' => ['Cloud migration %', 'Legacy system decommission rate', 'Time to market for new features'],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Rapid Growth',
                'slug' => 'rapid-growth',
                'description' => 'Plan para crecimiento acelerado de 50%+ en headcount',
                'scenario_type' => 'growth',
                'industry' => 'tech',
                'icon' => 'mdi-chart-line',
                'config' => [
                    'predefined_skills' => [
                        [
                            'skill_id' => 5,
                            'required_headcount' => 30,
                            'required_level' => 2,
                            'priority' => 'critical',
                            'rationale' => 'Incremento de capacidad operativa'
                        ],
                    ],
                    'suggested_strategies' => ['buy', 'borrow', 'bind'],
                    'kpis' => ['Time to productivity', 'Hiring cost per person', 'New hire retention %'],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Succession Planning',
                'slug' => 'succession-planning',
                'description' => 'Preparación para transiciones de liderazgo',
                'scenario_type' => 'optimization',
                'industry' => 'general',
                'icon' => 'mdi-person-multiple',
                'config' => [
                    'predefined_skills' => [
                        [
                            'skill_id' => 6,
                            'required_headcount' => 5,
                            'required_level' => 5,
                            'priority' => 'critical',
                            'rationale' => 'Líderes de siguiente generación'
                        ],
                    ],
                    'suggested_strategies' => ['build', 'bind', 'bridge'],
                    'kpis' => ['Succession readiness %', 'Internal promotion rate', 'Leadership pipeline depth'],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Cost Optimization',
                'slug' => 'cost-optimization',
                'description' => 'Optimizar costos sin comprometer capacidades',
                'scenario_type' => 'optimization',
                'industry' => 'general',
                'icon' => 'mdi-wallet',
                'config' => [
                    'predefined_skills' => [],
                    'suggested_strategies' => ['build', 'bind', 'bot'],
                    'kpis' => ['Cost per FTE', 'Productivity per $ spend', 'Automation %'],
                ],
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            ScenarioTemplate::firstOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }
}
