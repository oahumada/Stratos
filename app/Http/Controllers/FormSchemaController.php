<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class FormSchemaController extends Controller
{
    protected $repository;
    protected $modelClass;
    protected $repositoryClass;

    /**
     * Authorize action for a given model
     */
    protected function authorizeAction(string $modelName, string $action)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        // Special case for admins
        if ($user->isAdmin()) {
            return;
        }

        $module = strtolower($modelName);
        
        // Map common actions to permission suffixes
        $permSuffix = match ($action) {
            'index', 'show', 'search', 'searchWithPeople' => 'view',
            'store', 'update', 'destroy' => 'manage',
            default => 'view'
        };

        $permission = "{$module}.{$permSuffix}";

        if (!$user->hasPermission($permission)) {
            // Check for more specific permissions if general manage fails
            $specificPerm = "{$module}.{$action}";
            if (!$user->hasPermission($specificPerm)) {
                abort(403, "No tienes permiso para '{$permission}' en el módulo {$module}.");
            }
        }
    }

    /**
     * Inicializa el controlador con el modelo y repositorio correspondiente
     */
    public function initializeForModel(string $modelName, string $action = 'view')
    {
        $this->authorizeAction($modelName, $action);
        
        $this->resolveModelAndRepository($modelName);

        $model = new $this->modelClass;
        $this->repository = new $this->repositoryClass($model);

        return $this;
    }

    private function resolveModelAndRepository(string $modelName): void
    {
        $this->modelClass = "App\\Models\\{$modelName}";
        $this->repositoryClass = "App\\Repository\\{$modelName}Repository";

        if (!class_exists($this->modelClass)) {
            $this->tryAlternativeClasses($modelName);
        }

        if (!class_exists($this->modelClass)) {
            throw new \Exception("Model class {$this->modelClass} not found");
        }

        if (!class_exists($this->repositoryClass)) {
            throw new \Exception("Repository class {$this->repositoryClass} not found");
        }
    }

    private function tryAlternativeClasses(string $modelName): void
    {
        // Try singular
        $singular = rtrim($modelName, 's');
        $singularClass = "App\\Models\\{$singular}";
        
        if (class_exists($singularClass)) {
            $this->modelClass = $singularClass;
            $repoClass = "App\\Repository\\{$singular}Repository";
            if (class_exists($repoClass)) {
                $this->repositoryClass = $repoClass;
            }
            return;
        }

        // Try plural
        $plural = $modelName . 's';
        $pluralClass = "App\\Models\\{$plural}";
        if (class_exists($pluralClass)) {
            $this->modelClass = $pluralClass;
            $repoClass = "App\\Repository\\{$plural}Repository";
            if (class_exists($repoClass)) {
                $this->repositoryClass = $repoClass;
            }
        }
    }

    /**
     * Renderizar la vista principal de la tabla
     */
    public function index(string $modelName)
    {
        $this->initializeForModel($modelName, 'index');

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
        $this->initializeForModel($modelName, 'view');

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
            $this->initializeForModel($modelName, 'store');

            return $this->repository->store($request);
        } catch (\Exception $e) {
            Log::error("Error in FormSchemaController::store for {$modelName}: ".$e->getMessage());

            return response()->json([
                'message' => 'Error al crear el registro',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualizar un registro existente
     */
    public function update(Request $request, string $modelName, $id = null)
    {
        try {
            $this->initializeForModel($modelName, 'update');

            // Ensure the ID is present in the request payload.
            // If the route provides an $id, inject it into the payload at the top level
            // so that Repository::update() can extract it via $allData['id'].
            if ($id !== null) {
                $data = $request->get('data', $request->all());
                if (! isset($data['id'])) {
                    $data['id'] = $id;
                    $request->merge(['data' => $data]); // also merge at 'data' key for compatibility
                }
            }

            return $this->repository->update($request);
        } catch (\Exception $e) {
            Log::error("Error in FormSchemaController::update for {$modelName}: ".$e->getMessage());

            return response()->json([
                'message' => 'Error al actualizar el registro',
                'error' => $e->getMessage(),
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
            $this->initializeForModel($modelName, 'destroy');

            return $this->repository->destroy($id);
        } catch (\Exception $e) {
            Log::error("Error in FormSchemaController::destroy for {$modelName}: ".$e->getMessage());

            return response()->json([
                'message' => 'Error al eliminar el registro',
                'error' => $e->getMessage(),
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
            $this->initializeForModel($modelName, 'show');

            return $this->repository->show($request, $id);
        } catch (\Exception $e) {
            Log::error("Error in FormSchemaController::show for {$modelName}: ".$e->getMessage());

            return response()->json([
                'message' => 'Error al obtener el registro',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Buscar registros con filtros
     */
    public function search(Request $request, string $modelName)
    {
        try {

            $this->initializeForModel($modelName, 'search');

            return $this->repository->search($request);
        } catch (\Exception $e) {
            Log::error("Error in FormSchemaController::search for {$modelName}: ".$e->getMessage());

            return response()->json([
                'message' => 'Error en la búsqueda',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Buscar registros con joins (para consultas complejas)
     */
    public function searchWithPeople(Request $request, string $modelName)
    {
        try {
            $this->initializeForModel($modelName, 'searchWithPeople');

            return $this->repository->searchWithPeople($request);
        } catch (\Exception $e) {
            Log::error("Error in FormSchemaController::searchWithPeople for {$modelName}: ".$e->getMessage());

            return response()->json([
                'message' => 'Error en la búsqueda con joins',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


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
