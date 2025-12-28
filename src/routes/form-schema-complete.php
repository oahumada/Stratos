<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormSchemaController;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| FormSchema Complete Routes - Sistema Genérico Completo
|--------------------------------------------------------------------------
|
| Rutas genéricas para TODOS los modelos que usan FormSchema.
| Reemplaza completamente los controladores individuales.
|
*/

// Mapeo completo de modelos FormSchema: ModelName => route-name
$formSchemaModels = [
    // Tablas hijas principales
    'AntecedenteFamiliar' => 'antecedente-familiar',
    'AtencionDiaria' => 'atencion-diaria',
    'Alergia' => 'alergia',
    'Cirugia' => 'cirugia',
    'Diat' => 'diat',
    'Diep' => 'diep',
    'Enfermedad' => 'enfermedad',

    // Exámenes médicos
    'ExAlcohol' => 'ex-alcohol',
    'ExAldehido' => 'ex-aldehido',
    'ExAsma' => 'ex-asma',
    'ExEpo' => 'ex-epo',
    'ExEquilibrio' => 'ex-equilibrio',
    'ExHumoNegro' => 'ex-humo-negro',
    'ExMetal' => 'ex-metal',
    'ExPsico' => 'ex-psico',
    'ExPVTMERT' => 'ex-pvtmert',
    'ExRespirador' => 'ex-respirador',
    'ExRuido' => 'ex-ruido',
    'ExSalud' => 'ex-salud',
    'ExSilice' => 'ex-silice',
    'ExSolvente' => 'ex-solvente',
    'ExSomnolencia' => 'ex-somnolencia',

    // Otros modelos FormSchema
    'Ges' => 'ges',
    'Certificacion' => 'certificacion',
    'FactorRiesgo' => 'factor-riesgo',
    'LicenciaMedica' => 'licencia-medica',
    'Medicamento' => 'medicamento',
    'Vacuna' => 'vacuna',
];

// Envolver todas las rutas FormSchema en middleware de autenticación
Route::middleware(['auth:sanctum'])->group(function () use ($formSchemaModels) {

    // Generar rutas API para cada modelo
    foreach ($formSchemaModels as $modelName => $routeName) {

        // GET /api/{route-name} - Listar/Index
        Route::get($routeName, function (Request $request) use ($modelName) {
            $controller = new FormSchemaController();
            return $controller->search($request, $modelName);
        })->name("api.{$routeName}.index");

        // POST /api/{route-name} - Crear (store)
        Route::post($routeName, function (Request $request) use ($modelName) {
            $controller = new FormSchemaController();
            return $controller->store($request, $modelName);
        })->name("api.{$routeName}.store");

        // GET /api/{route-name}/{id} - Mostrar (show)
        Route::get("{$routeName}/{id}", function (Request $request, $id) use ($modelName) {
            $controller = new FormSchemaController();
            return $controller->show($request, $modelName, $id);
        })->name("api.{$routeName}.show");

        // PUT /api/{route-name}/{id} - Actualizar (update)
        Route::put("{$routeName}/{id}", function (Request $request, $id) use ($modelName) {
            $controller = new FormSchemaController();
            return $controller->update($request, $modelName);
        })->name("api.{$routeName}.update");

        // PATCH /api/{route-name}/{id} - Actualizar parcial
        Route::patch("{$routeName}/{id}", function (Request $request, $id) use ($modelName) {
            $controller = new FormSchemaController();
            return $controller->update($request, $modelName);
        })->name("api.{$routeName}.patch");

        // DELETE /api/{route-name}/{id} - Eliminar (destroy)
        Route::delete("{$routeName}/{id}", function (Request $request, $id) use ($modelName) {
            $controller = new FormSchemaController();
            return $controller->destroy($modelName, $id);
        })->name("api.{$routeName}.destroy");

        // POST /api/{route-name}/search - Búsqueda con filtros
        Route::post("{$routeName}/search", function (Request $request) use ($modelName) {
            $controller = new FormSchemaController();
            return $controller->search($request, $modelName);
        })->name("api.{$routeName}.search");

        // POST /api/{route-name}/search-with-paciente - Búsqueda con joins
        Route::post("{$routeName}/search-with-paciente", function (Request $request) use ($modelName) {
            $controller = new FormSchemaController();
            return $controller->searchWithPeople($request, $modelName);
        })->name("api.{$routeName}.search-with-paciente");
    }
});

/*
|--------------------------------------------------------------------------
| FormSchema Web Routes - Vistas principales
|--------------------------------------------------------------------------
|
| SECCIÓN ELIMINADA: Las vistas web específicas ahora se manejan
| a través del sistema genérico de consultas (ConsultaSchema).
| Las rutas de consulta se generan automáticamente más abajo.
|
*/

/*
|--------------------------------------------------------------------------
| ConsultaSchema Routes - Sistema Genérico de Consultas
|--------------------------------------------------------------------------
|
| Rutas de vista para consultas genéricas usando ConsultaSchema.vue
| Reutiliza las APIs ya existentes del sistema FormSchema (search)
| No necesita mapeo separado - usa el mapeo FormSchema existente
|
*/
/* 
// Generar rutas de consulta automáticamente desde el mapeo FormSchema existente
foreach ($formSchemaModels as $modelName => $routeName) {
    // Convertir route-name de FormSchema (kebab-case) a consulta (snake_case)
//    $consultaRouteName = str_replace('_', '-', $routeName);
    $consultaRouteName = $routeName;
    
    // Ruta principal de consulta (GET /consulta/{route-name})
    Route::get("consulta/{$consultaRouteName}", function () use ($modelName) {
        // Determinar el componente Vue basado en el modelo
        $vueComponent = 'subpages/consultas/Consulta' . $modelName . 'Generic';
        
        // Si no existe el componente genérico, usar el componente original
        $componentPath = resource_path("js/pages/{$vueComponent}.vue");
        if (!file_exists($componentPath)) {
            $vueComponent = 'subpages/consultas/Consulta' . $modelName;
        }
        
        return Inertia::render($vueComponent);
    })->name($consultaRouteName . '.consulta');
}
 */