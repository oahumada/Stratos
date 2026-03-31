import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ScenarioRoiController::calculate
* @see app/Http/Controllers/Api/ScenarioRoiController.php:15
* @route '/api/strategic-planning/roi-calculator/calculate'
*/
export const calculate = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: calculate.url(options),
    method: 'post',
})

calculate.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/roi-calculator/calculate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioRoiController::calculate
* @see app/Http/Controllers/Api/ScenarioRoiController.php:15
* @route '/api/strategic-planning/roi-calculator/calculate'
*/
calculate.url = (options?: RouteQueryOptions) => {
    return calculate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioRoiController::calculate
* @see app/Http/Controllers/Api/ScenarioRoiController.php:15
* @route '/api/strategic-planning/roi-calculator/calculate'
*/
calculate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: calculate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioRoiController::calculate
* @see app/Http/Controllers/Api/ScenarioRoiController.php:15
* @route '/api/strategic-planning/roi-calculator/calculate'
*/
const calculateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: calculate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioRoiController::calculate
* @see app/Http/Controllers/Api/ScenarioRoiController.php:15
* @route '/api/strategic-planning/roi-calculator/calculate'
*/
calculateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: calculate.url(options),
    method: 'post',
})

calculate.form = calculateForm

/**
* @see \App\Http\Controllers\Api\ScenarioRoiController::listCalculations
* @see app/Http/Controllers/Api/ScenarioRoiController.php:141
* @route '/api/strategic-planning/roi-calculator/scenarios'
*/
export const listCalculations = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listCalculations.url(options),
    method: 'get',
})

listCalculations.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/roi-calculator/scenarios',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioRoiController::listCalculations
* @see app/Http/Controllers/Api/ScenarioRoiController.php:141
* @route '/api/strategic-planning/roi-calculator/scenarios'
*/
listCalculations.url = (options?: RouteQueryOptions) => {
    return listCalculations.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioRoiController::listCalculations
* @see app/Http/Controllers/Api/ScenarioRoiController.php:141
* @route '/api/strategic-planning/roi-calculator/scenarios'
*/
listCalculations.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listCalculations.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioRoiController::listCalculations
* @see app/Http/Controllers/Api/ScenarioRoiController.php:141
* @route '/api/strategic-planning/roi-calculator/scenarios'
*/
listCalculations.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: listCalculations.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioRoiController::listCalculations
* @see app/Http/Controllers/Api/ScenarioRoiController.php:141
* @route '/api/strategic-planning/roi-calculator/scenarios'
*/
const listCalculationsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listCalculations.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioRoiController::listCalculations
* @see app/Http/Controllers/Api/ScenarioRoiController.php:141
* @route '/api/strategic-planning/roi-calculator/scenarios'
*/
listCalculationsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listCalculations.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioRoiController::listCalculations
* @see app/Http/Controllers/Api/ScenarioRoiController.php:141
* @route '/api/strategic-planning/roi-calculator/scenarios'
*/
listCalculationsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listCalculations.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

listCalculations.form = listCalculationsForm

const ScenarioRoiController = { calculate, listCalculations }

export default ScenarioRoiController