import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ScenarioGenerationAbacusController::generate
* @see app/Http/Controllers/Api/ScenarioGenerationAbacusController.php:16
* @route '/api/strategic-planning/scenarios/generate/abacus'
*/
export const generate = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(options),
    method: 'post',
})

generate.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/generate/abacus',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationAbacusController::generate
* @see app/Http/Controllers/Api/ScenarioGenerationAbacusController.php:16
* @route '/api/strategic-planning/scenarios/generate/abacus'
*/
generate.url = (options?: RouteQueryOptions) => {
    return generate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationAbacusController::generate
* @see app/Http/Controllers/Api/ScenarioGenerationAbacusController.php:16
* @route '/api/strategic-planning/scenarios/generate/abacus'
*/
generate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationAbacusController::generate
* @see app/Http/Controllers/Api/ScenarioGenerationAbacusController.php:16
* @route '/api/strategic-planning/scenarios/generate/abacus'
*/
const generateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationAbacusController::generate
* @see app/Http/Controllers/Api/ScenarioGenerationAbacusController.php:16
* @route '/api/strategic-planning/scenarios/generate/abacus'
*/
generateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generate.url(options),
    method: 'post',
})

generate.form = generateForm

const ScenarioGenerationAbacusController = { generate }

export default ScenarioGenerationAbacusController