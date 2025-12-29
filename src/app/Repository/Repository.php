<?php

namespace App\Repository;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Response;
use App\Helpers\Tools;

abstract class Repository implements RepositoryInterface
{

    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function store(Request $request)
    {
        $query = $request->get('data');
        Log::info($query);
        try {
            $query = array_map(function ($value) {
                return is_array($value) ? implode(',', $value) : $value;
            }, $query);

            $request = $this->model->create($query);
            return response()->json([
                'message' => 'Registro creado con éxito',
            ], 200);
        } catch (QueryException $e) {
            Log::error('store', [$e]);
            return response()->json([
                'message' => 'Se produjo un error: ',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request)
    {
        // Extract the 'id' from the incoming request data
        $id = $request->input('data.id');
        Log::info($request->all());

        // Retrieve the data to update and log it
        $dataToUpdate = $request->input('data');
        //Log::info('Incoming data for update: ', $dataToUpdate);

        // Remove the 'id' key to prepare for updating the model
        unset($dataToUpdate['id']);

        // Log the data that will be used for the update
        //Log::info('Data prepared for update: '.[$dataToUpdate]);

        try {
            // Retrieve the model instance or fail if not found
            $model = $this->model->findOrFail($id);
            Log::info('Model found with ID: ' . $id);
            Log::info('Model found with ID: ' . $model);

            // Update the model instance with the new data
            $model->fill($dataToUpdate);
            $model->save();

            // Log::info('Model updated successfully with ID: ' . $id);

            return response()->json(['message' => 'Model updated successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            Log::error('Model not found for ID: ' . $id);
            return response()->json(['error' => 'Model not found.'], 404);
        } catch (\Exception $e) {
            // Log::error('Error updating model with ID: ' . $id . '. Error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while updating the model.'], 500);
        }
    }

    /**
     * Búsqueda simple para CRUD (por ID o filtros básicos)
     */
    public function search(Request $request)
    {
        try {
            $filters = $request->input('data', []); // Usar 'data' como nuevo estándar
            $query = $this->model->query()->select("*");
            Log::info('Current Model: ' . get_class($this->model));
            Log::info('Search Filters: ' . json_encode($filters));
            Log::info('Search Query: ' . $query->toSql());
            return Tools::filterData($filters, $query);
        } catch (QueryException $e) {
            return response()->json([
                'result' => 'error',
                'message' => 'An error occurred while processing your request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Búsqueda con joins para consultas que requieren datos de múltiples modelos
     * Usa getSearchQuery() que debe ser sobrescrito en repositorios específicos
     */
    public function searchWithPerson(Request $request)
    {
        // Verificar configuración de logging
        $logConfig = config('logging.default');
        $logLevel = config('logging.level');

        // Múltiples métodos de log para asegurar que funcione
        file_put_contents(storage_path('logs/debug-manual.log'), "=== INICIO searchWithPerson ===\n", FILE_APPEND);
        file_put_contents(storage_path('logs/debug-manual.log'), "Log config: $logConfig, Level: $logLevel\n", FILE_APPEND);

        error_log('=== INICIO searchWithPerson ===');
        Log::info('=== INICIO searchWithPerson ===');

        try {
            $filters = $request->input('data', []);

            file_put_contents(storage_path('logs/debug-manual.log'), 'Filters: ' . json_encode($filters) . "\n", FILE_APPEND);

            $query = $this->getSearchQuery();

            file_put_contents(storage_path('logs/debug-manual.log'), 'Modelo: ' . get_class($this->model) . "\n", FILE_APPEND);

            $result = Tools::filterData($filters, $query);

            // Agregar detalles del resultado
            if (is_array($result)) {
                file_put_contents(storage_path('logs/debug-manual.log'), 'Resultado - Tipo: array, Cantidad: ' . count($result) . "\n", FILE_APPEND);
                file_put_contents(storage_path('logs/debug-manual.log'), 'Primeros 2 elementos: ' . json_encode(array_slice($result, 0, 2)) . "\n", FILE_APPEND);
            } else {
                file_put_contents(storage_path('logs/debug-manual.log'), 'Resultado - Tipo: ' . gettype($result) . "\n", FILE_APPEND);
                file_put_contents(storage_path('logs/debug-manual.log'), 'Resultado completo: ' . json_encode($result) . "\n", FILE_APPEND);
            }

            return response()->json([
                'result' => 'success',
                'data' => $result,
                'message' => 'Registros con joins cargados exitosamente'
            ]);
        } catch (\Exception $e) {
            file_put_contents(storage_path('logs/debug-manual.log'), 'ERROR: ' . $e->getMessage() . "\n", FILE_APPEND);
            file_put_contents(storage_path('logs/debug-manual.log'), 'Stack trace: ' . $e->getTraceAsString() . "\n", FILE_APPEND);

            return response()->json([
                'result' => 'error',
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtiene la query base para búsquedas
     * Puede ser sobrescrito en repositorios específicos para agregar joins
     */
    protected function getSearchQuery()
    {
        // Verificar que el modelo esté inicializado
        if (!$this->model) {
            throw new \Exception('Model not initialized in repository');
        }

        // Para consultas simples, usar select * es seguro
        // Para joins, los repositorios hijos deben sobrescribir este método
        return $this->model->query()->select('*');
    }

    public function destroy($id)
    {
        try {
            $this->model->destroy($id);
            return Response::json([
                'message' => 'Registro borrado con exito',
            ], 200);
        } catch (QueryException $e) {
            return Response::json([
                'result' => "500",
                'message' => 'Error al borrar el registro',
                'error' => $e,
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        Log::info($id);
        $paciente_id = $id;
        try {
            if ($paciente_id && is_numeric($paciente_id)) {
                $query = $this->model->query();
                Log::info('Current Model: ' . get_class($this->model));
                $withRelations = $request->input('withRelations', []);
                Log::info($withRelations);

                // Add eager loading with error handling
                if (!empty($withRelations)) {
                    $validRelations = [];
                    $modelInstance = new $this->model;

                    foreach ($withRelations as $relation) {
                        if (method_exists($modelInstance, $relation)) {
                            $validRelations[] = $relation;
                        } else {
                            Log::warning("Invalid relation attempted: $relation");
                        }
                    }

                    try {
                        $query->with($validRelations);
                    } catch (\Exception $e) {
                        Log::error('Eager loading failed: ' . $e->getMessage());
                        Log::error('Failed relations: ' . json_encode($withRelations));
                    }
                }

                // Filter by paciente_id
                $query->where('paciente_id', $paciente_id);

                // MOVE LOGGING HERE - AFTER ALL QUERY CONDITIONS
                Log::info('Final Query: ' . $query->toSql());
                Log::info('Query Bindings: ' . json_encode($query->getBindings()));

                $results = $query->get();
                Log::info($results);

                return response()->json([
                    'result' => 'success',
                    'data' => $results,
                    'message' => 'Registros cargados exitosamente'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener el registro',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
