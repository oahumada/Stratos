<?php

namespace App\Repository;

use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PacienteRepository extends Repository
{
    public function __construct(Paciente $model)
    {
        $this->model = $model;
    }

    /**
     * PacienteRepository simplificado
     * Toda la funcionalidad de búsqueda compleja se delega al método search() robusto de Repository
     * que utiliza Tools::filterData para manejo automático de filtros, catálogos y optimizaciones
     */

    public function update(Request $request)
    {
        $id = $request->input('data.id');
        Log::info('Updating patient with ID: ' . $id);
        Log::info('Request data: ' . json_encode($request->all()));

        try {
            // Get current patient data before update
            $paciente = $this->model::findOrFail($id);
            $estadoAnterior = $paciente->activo;
            
            // Extract and prepare data for update
            $raw = $request->all();
            if (isset($raw['data']) && is_array($raw['data'])) {
                $data = $raw['data'];
            } elseif (count($raw) === 1 && isset($raw['data'])) {
                $data = $raw['data'];
            } else {
                $data = $raw;
            }

            // Remove ID from update data
            unset($data['id']);

            // Validate required fields
            $validated = $request->validate([
                'data.nombre' => 'sometimes|required',
                'data.rut' => 'sometimes|required', 
                'data.apellidos' => 'sometimes|required',
               // 'data.email' => 'sometimes|required|email',
            ]);

            // Update patient data
            $paciente->update($data);
            
            // Check if patient was deactivated (from active to inactive)
            $estadoNuevo = $paciente->fresh()->activo;
            $fueDesactivado = ($estadoAnterior == 1 || $estadoAnterior === true) && 
                             ($estadoNuevo == 0 || $estadoNuevo === false);
            
            if ($fueDesactivado) {
                Log::info('Patient deactivated, sending notification email to: ' . $paciente->email);
                $this->enviarNotificacionDesactivacion($paciente);
            }

            return response()->json([
                'message' => 'Paciente actualizado con éxito',
                'data' => $paciente
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Error updating patient: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al actualizar el paciente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send deactivation notification email to patient
     */
    private function enviarNotificacionDesactivacion(Paciente $paciente)
    {
        try {
            // Get patient exposures
            $exposiciones = $paciente->paciente_exposicion()->with('tipo_exposicion')->get()->toArray();
            
            // Send email
            \Illuminate\Support\Facades\Mail::to($paciente->email)
                ->send(new \App\Mail\PacienteDesactivacionMail($paciente, $exposiciones));
                
            Log::info('Deactivation notification email sent successfully to: ' . $paciente->email);
            
        } catch (\Exception $e) {
            Log::error('Failed to send deactivation notification email: ' . $e->getMessage());
            // Don't throw exception to avoid breaking the update process
        }
    }

   /*  public function store(Request $request)
    {
        // Permitir datos bajo 'data' o directamente en el cuerpo, robusto para cualquier formato
        $raw = $request->all();
        if (isset($raw['data']) && is_array($raw['data'])) {
            $data = $raw['data'];
        } elseif (count($raw) === 1 && isset($raw['data'])) {
            $data = $raw['data'];
        } else {
            $data = $raw;
        }

        // Validar campos obligatorios antes de guardar    
        $camposObligatorios = ['nombre', 'apellidos', 'email', 'rut'];
        foreach ($camposObligatorios as $campo) {
            if (empty($data[$campo])) {
                return response()->json([
                    'message' => "El campo '$campo' es obligatorio.",
                    'error' => 'missing_field',
                    'field' => $campo,
                ], 422);
            }
        }
        // Validar RUT antes de guardar
        if (!\App\Helpers\RutGenerator::validarRut($data['rut'])) {
            return response()->json([
                'message' => 'El RUT ingresado no es válido.',
                'error' => 'invalid_rut',
            ], 422);
        }
        // Continuar con el flujo original
        try {
            $data = array_map(function ($value) {
                return is_array($value) ? implode(',', $value) : $value;
            }, $data);
            $this->model->create($data);
            return response()->json([
                'message' => 'Registro creado con éxito',
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('store', [$e]);
            return response()->json([
                'message' => 'Se produjo un error: ',
                'error' => $e->getMessage(),
            ], 500);
        }
    }*/

    /**
     * Override del método show para obtener un paciente específico por ID
     */
    public function show(Request $request, $id)
    {
        Log::info('PacienteRepository->show() - Buscando paciente ID: ' . $id);
        
        try {
            // Buscar el paciente por su ID primario con relaciones (excepto 'exposicion')
            $paciente = $this->model->with([
                'afp',
                'area', 
                'ceco',
                'estado_civil',
                'empresa',
                'genero',
                'grupo_sanguineo',
                'nivel_instruccion',
                'ley_social',
                'nacionalidad',
                'planta',
                'prevision_social',
                'pueblo_originario',
                'religion',
                'seguro_salud',
                'unidad'
            ])->findOrFail($id);
            
            Log::info('Paciente encontrado: ' . $paciente->nombre . ' ' . $paciente->apellidos);
            
            return response()->json([
                'result' => 'success',
                'data' => $paciente,
                'message' => 'Paciente cargado exitosamente'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Paciente no encontrado con ID: ' . $id);
            return response()->json([
                'result' => 'error',
                'data' => null,
                'message' => 'Paciente no encontrado'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Error al obtener paciente: ' . $e->getMessage());
            return response()->json([
                'result' => 'error',
                'data' => null,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Buscar paciente por user_id
     */
    public function showByUserId(Request $request, $userId)
    {
        Log::info('PacienteRepository->showByUserId() - Buscando paciente por user_id: ' . $userId);
        
        try {
            // Buscar el paciente por user_id con las relaciones
            $paciente = $this->model->with([
                'afp',
                'area', 
                'ceco',
                'estado_civil',
                'empresa',
                'genero',
                'grupo_sanguineo',
                'nivel_instruccion',
                'ley_social',
                'nacionalidad',
                'planta',
                'prevision_social',
                'pueblo_originario',
                'religion',
                'seguro_salud',
                'unidad'
            ])->where('user_id', $userId)->first();
            
            if (!$paciente) {
                Log::warning('Paciente no encontrado para user_id: ' . $userId);
                return response()->json([
                    'result' => 'error',
                    'data' => null,
                    'message' => 'Paciente no encontrado para este usuario'
                ], 404);
            }
            
            Log::info('Paciente encontrado por user_id: ' . $paciente->nombre . ' ' . $paciente->apellidos);
            
            return response()->json([
                'result' => 'success',
                'data' => $paciente,
                'message' => 'Paciente cargado exitosamente'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al obtener paciente por user_id: ' . $e->getMessage());
            return response()->json([
                'result' => 'error',
                'data' => null,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

}