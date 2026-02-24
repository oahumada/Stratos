<?php

namespace App\Repository;

use App\Models\PeopleRelationship;

class PeopleRelationshipRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(new PeopleRelationship);
    }

    protected function getSearchQuery()
    {
        return $this->model->query()->with(['person', 'relatedPerson']);
    }
}
