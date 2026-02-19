<?php

namespace App\Services\Performance;

use App\Models\People;

class PerformanceDataService
{
    /**
     * Get performance data for a person.
     * Currently mocked, in future this would connect to ERP/CRM/HRIS.
     * 
     * @param int $peopleId
     * @return array
     */
    public function getPerformanceData(int $peopleId): array
    {
        // Deterministic mock based on ID to be consistent during demo
        // High ID = High Performance, just for variety
        $basePerformance = 70 + ($peopleId % 30); 
        
        return [
            'overall_rating' => min(100, $basePerformance + rand(-5, 5)), // 0-100 scale
            'period' => '2025-Q4',
            'kpis' => [
                [
                    'name' => 'Project Completion Rate',
                    'target' => 100,
                    'actual' => min(100, $basePerformance + rand(-10, 10)),
                    'unit' => '%'
                ],
                [
                    'name' => 'Client Satisfaction (NPS)',
                    'target' => 50,
                    'actual' => 40 + ($peopleId % 20),
                    'unit' => 'points'
                ],
                [
                    'name' => 'Team Velocity',
                    'target' => 40,
                    'actual' => 35 + ($peopleId % 10),
                    'unit' => 'sp/sprint'
                ]
            ],
            'achievements' => [
                'Delivered Q4 roadmap on time',
                'Reduced legacy debt by 15%'
            ]
        ];
    }
}
