<?php

namespace App\Repository;

use App\Models\People;
use Illuminate\Http\Request;
use App\Helpers\Tools;

class PeopleRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(new People());
    }

    /**
     * Query con joins específicos para People usando relaciones Eloquent
     */
    protected function getSearchQuery()
    {
        $person = People::with('skills', 'currentRole')->find($id);
        if (! $person) {
            return response()->json(['error' => 'Peoplea no encontrada'], 404);
        }

        return response()->json([
            'id' => $person->id,
            'name' => $person->full_name ?? ($person->first_name . ' ' . $person->last_name),
            'email' => $person->email,
            'first_name' => $person->first_name,
            'last_name' => $person->last_name,
            'department' => $person->department,
            'hire_date' => $person->hire_date,
            'current_role' => $person->currentRole?->name,
            'skills' => $person->skills->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'category' => $s->category,
                'level' => $s->pivot->level ?? 0,
                'last_evaluated_at' => $s->pivot->last_evaluated_at,
            ]),
        ]);
        \Log::info('People Query SQL: ' . $query->toSql());
        return $query;
    }

    // Remover el método searchWithPaciente - ya no es necesario
    // El Repository padre manejará los filtros automáticamente usando Tools::filterData()
}
