<?php

namespace App\Console\Commands;

use App\Models\ExecutiveAggregate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshExecutiveAggregates extends Command
{
    protected $signature = 'executive:refresh-aggregates
        {--organization_id= : Optional organization_id scope}
        {--apply : Persist changes (default is dry-run)}';

    protected $description = 'Compute and store executive aggregates into executive_aggregates table.';

    public function handle(): int
    {
        $orgInput = $this->option('organization_id');
        $apply = (bool) $this->option('apply');

        $orgs = [];
        if ($orgInput !== null) {
            if (! ctype_digit((string) $orgInput)) {
                $this->error('Invalid --organization_id. It must be an integer.');
                return self::INVALID;
            }
            $orgs = [(int) $orgInput];
        } else {
            $orgs = DB::table('organizations')->pluck('id')->map(fn($v) => (int)$v)->all();
        }

        $this->info('Organizations to process: '.count($orgs));
        $this->line('Mode: '.($apply ? 'apply' : 'dry-run'));

        if (! $apply) {
            $this->warn('Dry-run mode: aggregates will not be persisted. Use --apply to write results.');
        }

        $sql = <<<'SQL'
select
    (select count(*) from people where organization_id = ? and deleted_at is null) as headcount,
    (select count(*) from scenarios where organization_id = ?) as total_scenarios,
    (select count(distinct people_role_skills.people_id) from people_role_skills join people on people_role_skills.people_id = people.id where people.organization_id = ? and people_role_skills.current_level >= people_role_skills.required_level and people_role_skills.required_level > 0) as upskilled_count,
    (select AVG(CASE WHEN prs.required_level > prs.current_level THEN (prs.required_level - prs.current_level) END) from people_role_skills prs join people p on prs.people_id = p.id where p.organization_id = ?) as avg_gap,
    (select count(*) from scenario_closure_strategies sc join scenarios s on sc.scenario_id = s.id where s.organization_id = ? and sc.strategy = 'bot') as bot_strategies,
    (select count(*) from people_role_skills prs join people p on prs.people_id = p.id where p.organization_id = ?) as total_pivot_rows,
    (select AVG(LEAST(1.0, prs.current_level / NULLIF(prs.required_level, 0))) from people_role_skills prs join people p on prs.people_id = p.id where p.organization_id = ?) as avg_readiness,
    (select count(*) from people_role_skills prs join people p on prs.people_id = p.id where p.organization_id = ? and prs.required_level > 0 and prs.current_level < (prs.required_level * 0.5)) as critical_gaps,
    (select count(*) from roles where organization_id = ?) as total_roles,
    (select count(distinct sc.role_id) from scenario_closure_strategies sc join scenarios s on sc.scenario_id = s.id join roles r on sc.role_id = r.id where s.organization_id = ? and r.organization_id = ? and sc.strategy = 'bot') as augmented_roles,
    (select AVG(CASE WHEN ep.ai_turnover_risk = 'low' THEN 25 WHEN ep.ai_turnover_risk = 'medium' THEN 60 WHEN ep.ai_turnover_risk = 'high' THEN 85 ELSE 50 END)
        from employee_pulses ep join people p on ep.people_id = p.id where p.organization_id = ?) as avg_turnover_risk,
    (select count(distinct people_role_skills.people_id) from people_role_skills join people on people_role_skills.people_id = people.id where people.organization_id = ? and people_role_skills.required_level > 0 and people_role_skills.current_level >= people_role_skills.required_level) as ready_now,
    (select count(*) from people_role_skills prs join people p on prs.people_id = p.id where p.organization_id = ? and prs.current_level = 0) as level_0_count,
    (select count(*) from people_role_skills prs join people p on prs.people_id = p.id where p.organization_id = ? and prs.current_level = 1) as level_1_count,
    (select count(*) from people_role_skills prs join people p on prs.people_id = p.id where p.organization_id = ? and prs.current_level = 2) as level_2_count,
    (select count(*) from people_role_skills prs join people p on prs.people_id = p.id where p.organization_id = ? and prs.current_level = 3) as level_3_count,
    (select count(*) from people_role_skills prs join people p on prs.people_id = p.id where p.organization_id = ? and prs.current_level = 4) as level_4_count,
    (select count(*) from people_role_skills prs join people p on prs.people_id = p.id where p.organization_id = ? and prs.current_level = 5) as level_5_count
SQL;

        foreach ($orgs as $orgId) {
            $params = array_fill(0, 19, $orgId);
            $row = DB::selectOne($sql, $params);

            if (! $row) {
                $this->line("No aggregates computed for org {$orgId}");
                continue;
            }

            $data = [
                'organization_id' => $orgId,
                'scenario_id' => null,
                'headcount' => $row->headcount ?? 0,
                'total_scenarios' => $row->total_scenarios ?? 0,
                'upskilled_count' => $row->upskilled_count ?? 0,
                'avg_gap' => $row->avg_gap ?? null,
                'bot_strategies' => $row->bot_strategies ?? 0,
                'total_pivot_rows' => $row->total_pivot_rows ?? 0,
                'avg_readiness' => $row->avg_readiness ?? null,
                'critical_gaps' => $row->critical_gaps ?? 0,
                'total_roles' => $row->total_roles ?? 0,
                'augmented_roles' => $row->augmented_roles ?? 0,
                'avg_turnover_risk' => $row->avg_turnover_risk ?? null,
                'ready_now' => $row->ready_now ?? 0,
                'level_0_count' => $row->level_0_count ?? 0,
                'level_1_count' => $row->level_1_count ?? 0,
                'level_2_count' => $row->level_2_count ?? 0,
                'level_3_count' => $row->level_3_count ?? 0,
                'level_4_count' => $row->level_4_count ?? 0,
                'level_5_count' => $row->level_5_count ?? 0,
            ];

            if ($apply) {
                ExecutiveAggregate::updateOrCreate(
                    ['organization_id' => $orgId, 'scenario_id' => null],
                    $data
                );
                $this->line("Persisted aggregates for org {$orgId}");
            } else {
                $this->line("Dry-run: computed aggregates for org {$orgId} -> ".json_encode($data));
            }
        }

        return self::SUCCESS;
    }
}
