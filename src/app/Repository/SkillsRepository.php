<?php

namespace App\Repository;

use App\Models\Skill;
use Illuminate\Http\Request;
use App\Helpers\Tools;
use Illuminate\Support\Facades\Log;

class SkillsRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(new Skill());
    }

    /**
     * Override getSearchQuery para búsquedas CRUD
     * Incluye conteos y relaciones de roles y empleados
     */
    protected function getSearchQuery()
    {
        Log::info('SkillsRepository::getSearchQuery called');
        $query = $this->model->query()
            ->select('skills.*')
            ->with([
                'roles',
                'peopleRoleSkills' => function ($query) {
                    $query->where('is_active', true)
                          ->select('id', 'people_id', 'skill_id', 'current_level', 'required_level', 'is_active')
                          ->with('person:id,first_name,last_name,email');
                }
            ])
            ->withCount([
                'roles as roles_count',
                'peopleRoleSkills as people_count' => function ($query) {
                    $query->where('is_active', true)->distinct();
                }
            ]);
        Log::info('SkillsRepository query: ' . $query->toSql());
        return $query;
    }
}

