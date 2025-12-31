<?php

namespace App\Repository;

use App\Models\Roles;
use Illuminate\Http\Request;
use App\Helpers\Tools;

class RoleRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(new Roles());
    }

    /**
     * Query con joins específicos para ExEpo usando relaciones Eloquent
     */
   /*  protected function getSearchQuery()
    {
        $query = $this->model->join(
                'paciente',
                'examen_epo.paciente_id',
                '=',
                'paciente.id'
            )->with([
                'paciente' => function ($q) {
                    $q->with(['empresa', 'area', 'ceco'])
                      ->select('id', 'rut', 'nombre', 'apellidos', 'empresa_id', 'area_id', 'ceco_id');
                },
            ])->with([
                'estado_epo', 
                'estado_examen', 
                'bateria', 
                'semaforo', 
                'tipo_examen'
            ])->select(
                'examen_epo.*',
                'paciente.rut',
                'paciente.nombre as paciente_nombre',
                'paciente.apellidos as paciente_apellidos',
                'paciente.empresa_id',
                'paciente.area_id',
                'paciente.ceco_id'
            );
            
        \Log::info('ExEpo Query SQL: ' . $query->toSql());
        return $query;
    } */

    // Remover el método searchWithPaciente - ya no es necesario
    // El Repository padre manejará los filtros automáticamente usando Tools::filterData()
}
