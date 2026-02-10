<?php

namespace App\Repository;

use App\Models\RoleSkill;

class RoleSkillRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(new RoleSkill);
    }

    /**
     * Consulta base con eager loading de rol y skill.
     */
    protected function getSearchQuery()
    {
        return $this->model
            ->query()
            ->with([
                'role:id,name,level,organization_id',
                'skill:id,name,category,organization_id,is_critical',
            ])
            ->select('*');
    }
}
