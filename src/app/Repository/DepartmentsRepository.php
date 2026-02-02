<?php

namespace App\Repository;

use App\Models\Departments;
use Illuminate\Http\Request;
use App\Helpers\Tools;

class DepartmentsRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(new Departments());
    }

}
