<?php

namespace App\Repository;

use App\Helpers\Tools;
use App\Models\People;
use Illuminate\Support\Facades\Log;

class PeopleRepository extends Repository
{
    public function __construct($model = null)
    {
        if ($model === null) {
            $model = new People;
        }
        parent::__construct($model);
    }

    /**
     * Override getSearchQuery to include eager loading of relationships
     */
    protected function getSearchQuery()
    {
        Log::info('PeopleRepository::getSearchQuery called');
        $query = $this->model->query()
            ->with('department', 'role', 'skills');
        Log::info('PeopleRepository query: '.$query->toSql());

        return $query;
    }

    // Remover el método searchWithPaciente - ya no es necesario
    // El Repository padre manejará los filtros automáticamente usando Tools::filterData()
}
