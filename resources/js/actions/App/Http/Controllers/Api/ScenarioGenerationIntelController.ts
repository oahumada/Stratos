import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ScenarioGenerationIntelController::generate
* @see app/Http/Controllers/Api/ScenarioGenerationIntelController.php:15
* @route '/api/strategic-planning/scenarios/generate/intel'
*/
export const generate = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(options),
    method: 'post',
})

generate.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/generate/intel',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationIntelController::generate
* @see app/Http/Controllers/Api/ScenarioGenerationIntelController.php:15
* @route '/api/strategic-planning/scenarios/generate/intel'
*/
generate.url = (options?: RouteQueryOptions) => {
    return generate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationIntelController::generate
* @see app/Http/Controllers/Api/ScenarioGenerationIntelController.php:15
* @route '/api/strategic-planning/scenarios/generate/intel'
*/
generate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationIntelController::generate
* @see app/Http/Controllers/Api/ScenarioGenerationIntelController.php:15
* @route '/api/strategic-planning/scenarios/generate/intel'
*/
const generateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationIntelController::generate
* @see app/Http/Controllers/Api/ScenarioGenerationIntelController.php:15
* @route '/api/strategic-planning/scenarios/generate/intel'
*/
generateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generate.url(options),
    method: 'post',
})

generate.form = generateForm

const ScenarioGenerationIntelController = { generate }

export default ScenarioGenerationIntelController