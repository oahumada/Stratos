<?php

namespace App\Repository;

use App\Models\Departments;

class DepartmentsRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(new Departments);
    }
}
