<?php

namespace App\Repository;

use App\Models\Roles;

class RolesRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(new Roles);
    }

    // Eager load skills and people relations for detail views
    protected function getSearchQuery()
    {
        return $this->model
            ->with(['skills' => function ($query) {
                $query->select('skills.id', 'skills.name', 'skills.category')
                    ->withPivot('required_level', 'is_critical')
                    ->without('roles'); // Evitar relación circular
            }])
            ->with(['people' => function ($query) {
                $query->withoutGlobalScope('organization')
                    ->select('people.id', 'people.first_name', 'people.last_name', 'people.email', 'people.role_id', 'people.department_id')
                    ->with(['department' => function ($deptQuery) {
                        $deptQuery->select('departments.id', 'departments.name');
                    }]);
            }])
            ->withCount('skills')
            ->withCount(['people as people_count' => function ($query) {
                $query->withoutGlobalScope('organization');
            }]);
    }
}
