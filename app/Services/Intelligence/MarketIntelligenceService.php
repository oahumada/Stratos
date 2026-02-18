<?php

namespace App\Services\Intelligence;

use App\Models\Roles;
use Illuminate\Support\Collection;

class MarketIntelligenceService
{
    /**
     * Get market context for a specific role.
     * In a production system, this would fetch from a database or an external API.
     * @param Roles|int $role
     * @return array
     */
    public function getRoleMarketContext($role): array
    {
        if (is_int($role)) {
            $role = Roles::find($role);
        }

        $roleName = $role ? $role->name : 'Unknown Role';
        
        // Defaults for simulation
        $baseContext = [
            'recruitment_cost_avg' => 5000,
            'time_to_hire_days' => 45,
            'training_cost_avg' => 1200,
            'market_scarcity' => 0.5, // 0.0 (abundant) to 1.0 (scarce)
            'competitiveness' => 'Medium',
            'estimated_annual_salary' => 60000,
            'currency' => 'USD'
        ];

        // Specific overrides based on role name/keywords for more realism
        if (stripos($roleName, 'Data') !== false || stripos($roleName, 'AI') !== false) {
            $baseContext['recruitment_cost_avg'] = 12000;
            $baseContext['time_to_hire_days'] = 90;
            $baseContext['market_scarcity'] = 0.9;
            $baseContext['competitiveness'] = 'Very High';
            $baseContext['estimated_annual_salary'] = 110000;
        } elseif (stripos($roleName, 'Senior') !== false || stripos($roleName, 'Lead') !== false) {
            $baseContext['recruitment_cost_avg'] = 15000;
            $baseContext['time_to_hire_days'] = 120;
            $baseContext['market_scarcity'] = 0.85;
            $baseContext['competitiveness'] = 'High';
            $baseContext['estimated_annual_salary'] = 130000;
        } elseif (stripos($roleName, 'Junior') !== false) {
            $baseContext['recruitment_cost_avg'] = 3000;
            $baseContext['time_to_hire_days'] = 30;
            $baseContext['market_scarcity'] = 0.2;
            $baseContext['competitiveness'] = 'Low';
            $baseContext['estimated_annual_salary'] = 45000;
        }

        return $baseContext;
    }
}
