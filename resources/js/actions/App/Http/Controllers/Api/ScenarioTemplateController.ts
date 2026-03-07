import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:12
* @route '/api/strategic-planning/scenario-templates'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenario-templates',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:12
* @route '/api/strategic-planning/scenario-templates'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:12
* @route '/api/strategic-planning/scenario-templates'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:12
* @route '/api/strategic-planning/scenario-templates'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

const ScenarioTemplateController = { index }

export default ScenarioTemplateController