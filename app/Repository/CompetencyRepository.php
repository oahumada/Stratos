<?php

namespace App\Repository;

use App\Models\Competency;

class CompetencyRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(new Competency);
    }

    protected function getSearchQuery()
    {
        return $this->model
            ->with(['agent'])
            ->withCount('skills');
    }
}
