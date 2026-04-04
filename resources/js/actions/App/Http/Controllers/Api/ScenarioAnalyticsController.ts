import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::compareScenarios
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:25
* @route '/api/scenarios/compare'
*/
export const compareScenarios = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: compareScenarios.url(options),
    method: 'post',
})

compareScenarios.definition = {
    methods: ["post"],
    url: '/api/scenarios/compare',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::compareScenarios
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:25
* @route '/api/scenarios/compare'
*/
compareScenarios.url = (options?: RouteQueryOptions) => {
    return compareScenarios.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::compareScenarios
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:25
* @route '/api/scenarios/compare'
*/
compareScenarios.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: compareScenarios.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::compareScenarios
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:25
* @route '/api/scenarios/compare'
*/
const compareScenariosForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: compareScenarios.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::compareScenarios
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:25
* @route '/api/scenarios/compare'
*/
compareScenariosForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: compareScenarios.url(options),
    method: 'post',
})

compareScenarios.form = compareScenariosForm

const ScenarioAnalyticsController = { compareScenarios }

export default ScenarioAnalyticsController