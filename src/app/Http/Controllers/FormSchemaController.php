<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class FormSchemaController extends Controller
{
    protected $repository;
    protected $modelClass;
    protected $repositoryClass;

    /**
     * Inicializa el controlador con el modelo y repositorio correspondiente
     */
    public function initializeForModel(string $modelName)
    {
        // Mapeo de nombres de modelo a sus clases
        $this->modelClass = "App\\Models\\{$modelName}";
        $this->repositoryClass = "App\\Repository\\{$modelName}Repository";

        // Verificar que las clases existan; si no, intentar formas singular/plural alternas
        if (!class_exists($this->modelClass)) {
            // Try singular (trim trailing 's')
            $altModelName = rtrim($modelName, 's');
            $altModelClass = "App\\Models\\{$altModelName}";
            if (class_exists($altModelClass)) {
                $this->modelClass = $altModelClass;
                // adjust repository class to match available repository (plural/singular)
                $altRepoClass = "App\\Repository\\{$altModelName}Repository";
                if (class_exists($altRepoClass)) {
                    $this->repositoryClass = $altRepoClass;
                }
            } else {
                // Try plural (append 's') only if not already plural
                $altModelName2 = $modelName . 's';
                $altModelClass2 = "App\\Models\\{$altModelName2}";
                if (class_exists($altModelClass2)) {
                    $this->modelClass = $altModelClass2;
                    $altRepoClass2 = "App\\Repository\\{$altModelName2}Repository";
                    if (class_exists($altRepoClass2)) {
                        $this->repositoryClass = $altRepoClass2;
                    }
                }
            }
        }

        // Final verification
        if (!class_exists($this->modelClass)) {
            throw new \Exception("Model class {$this->modelClass} not found");
        }

        if (!class_exists($this->repositoryClass)) {
            throw new \Exception("Repository class {$this->repositoryClass} not found");
        }

        // Instanciar el repositorio
        $model = new $this->modelClass;
        $this->repository = new $this->repositoryClass($model);

        return $this;
    }

    /**
     * Renderizar la vista principal de la tabla
     */
    public function index(string $modelName)
    {
        $this->initializeForModel($modelName);

        // Mapeo de modelos a sus vistas correspondientes
        $viewMap = $this->getViewMap();

        $viewName = $viewMap[$modelName] ?? "subpages/{$modelName}";

        return Inertia::render($viewName);
    }

    /**
     * Renderizar vista de consulta si existe
     */
    public function consulta(string $modelName)
    {
        $this->initializeForModel($modelName);

        $consultaViewMap = $this->getConsultaViewMap();
        $viewName = $consultaViewMap[$modelName] ?? "Consultas/Consulta{$modelName}";

        return Inertia::render($viewName);
    }

    /**
     * Crear un nuevo registro
     */
    public function store(Request $request, string $modelName)
    {
        try {
            $this->initializeForModel($modelName);
            return $this->repository->store($request);
        } catch (\Exception $e) {
            Log::error("Error in FormSchemaController::store for {$modelName}: " . $e->getMessage());
            return response()->json([
                'message' => 'Error al crear el registro',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar un registro existente
     */
    public function update(Request $request, string $modelName, $id = null)
    {
        try {
            $this->initializeForModel($modelName);
            
            // Ensure the ID is present in the request payload.
            // If the route provides an $id, inject it into the payload at the top level
            // so that Repository::update() can extract it via $allData['id'].
            if ($id !== null) {
                $data = $request->get('data', $request->all());
                if (!isset($data['id'])) {
                    $data['id'] = $id;
                    $request->merge(['data' => $data]); // also merge at 'data' key for compatibility
                }
            }

            return $this->repository->update($request);
        } catch (\Exception $e) {
            Log::error("Error in FormSchemaController::update for {$modelName}: " . $e->getMessage());
            return response()->json([
                'message' => 'Error al actualizar el registro',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un registro
     */
    public function destroy(string $modelName, $id)
    {
        Log::info("FormSchemaController::destroy called for {$modelName} with ID: {$id}");
        try {
            $this->initializeForModel($modelName);
            return $this->repository->destroy($id);
        } catch (\Exception $e) {
            Log::error("Error in FormSchemaController::destroy for {$modelName}: " . $e->getMessage());
            return response()->json([
                'message' => 'Error al eliminar el registro',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar registros para FormSchema (filtrados por )
     * Usa eager loading automático para optimizar la carga de relaciones
     */
    public function show(Request $request, string $modelName, $id = null)
    {
        try {
            $this->initializeForModel($modelName);
            return $this->repository->show($request, $id);
        } catch (\Exception $e) {
            Log::error("Error in FormSchemaController::show for {$modelName}: " . $e->getMessage());
            return response()->json([
                'message' => 'Error al obtener el registro',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Buscar registros con filtros
     */
    public function search(Request $request, string $modelName)
    {
        try {

            $this->initializeForModel($modelName);
            return $this->repository->search($request);
        } catch (\Exception $e) {
            Log::error("Error in FormSchemaController::search for {$modelName}: " . $e->getMessage());
            return response()->json([
                'message' => 'Error en la búsqueda',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar registros con joins (para consultas complejas)
     */
    public function searchWithPeople(Request $request, string $modelName)
    {
        try {
            $this->initializeForModel($modelName);
            return $this->repository->searchWithPeople($request);
        } catch (\Exception $e) {
            Log::error("Error in FormSchemaController::searchWithPeople for {$modelName}: " . $e->getMessage());
            return response()->json([
                'message' => 'Error en la búsqueda con joins',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Búsqueda peoplealizada (si el repositorio la implementa)
     */
    /*public function search(Request $request, string $modelName, $id = null)
    {
        try {
            $this->initializeForModel($modelName);

            if (method_exists($this->repository, 'search')) {
                return $this->repository->search($id ?? $request);
            }

            // Fallback a search
            return $this->search($request, $modelName);

        } catch (\Exception $e) {
            Log::error("Error in FormSchemaController::search for {$modelName}: " . $e->getMessage());
            return response()->json([
                'message' => 'Error en la búsqueda',
                'error' => $e->getMessage()
            ], 500);
        }
    } */

    /**
     * Mapeo de modelos a sus vistas principales
     * Puedes peoplealizar esto según tus necesidades
     */
    private function getViewMap(): array
    {
        return [
            'AtencionDiaria' => 'subpages/AtencionesDiarias',
            'ExEquilibrio' => 'subpages/examenes/ExamenEquilibrio',
            'Alergia' => 'subpages/Alergia',
            'ExPsico' => 'subpages/ExamenPsico',
            // Agregar más mapeos según sea necesario
        ];
    }

    /**
     * Mapeo de modelos a sus vistas de consulta
     */
    private function getConsultaViewMap(): array
    {
        return [
            'AtencionDiaria' => 'ConsultaAtencionDiaria',
            'ExEquilibrio' => 'Consultas/ConsultaExEquilibrio',
            // Agregar más mapeos según sea necesario
        ];
    }
}
