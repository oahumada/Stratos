<?php

namespace App\Repository;

use App\Models\Roles;
use Illuminate\Http\Request;
use App\Helpers\Tools;

class RolesRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(new Roles());
    }

    // El Repository padre manejará los filtros automáticamente usando Tools::filterData()
    // No se necesita eager loading de departments aquí porque Roles no tiene esa relación
}
