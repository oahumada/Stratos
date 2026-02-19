<?php

namespace App\Repository;

use App\Models\CompetencyLevelBars;

class CompetencyLevelBarsRepository extends Repository
{
    public function __construct()
    {
        $this->model = new CompetencyLevelBars();
    }
}
