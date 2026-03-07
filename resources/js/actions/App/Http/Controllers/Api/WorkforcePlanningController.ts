import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::createScenario
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:20
* @route '/api/workforce-planning/scenarios'
*/
export const createScenario = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createScenario.url(options),
    method: 'post',
})

createScenario.definition = {
    methods: ["post"],
    url: '/api/workforce-planning/scenarios',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::createScenario
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:20
* @route '/api/workforce-planning/scenarios'
*/
createScenario.url = (options?: RouteQueryOptions) => {
    return createScenario.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::createScenario
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:20
* @route '/api/workforce-planning/scenarios'
*/
createScenario.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createScenario.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::getScenarios
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:67
* @route '/api/workforce-planning/scenarios'
*/
export const getScenarios = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getScenarios.url(options),
    method: 'get',
})

getScenarios.definition = {
    methods: ["get","head"],
    url: '/api/workforce-planning/scenarios',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::getScenarios
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:67
* @route '/api/workforce-planning/scenarios'
*/
getScenarios.url = (options?: RouteQueryOptions) => {
    return getScenarios.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::getScenarios
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:67
* @route '/api/workforce-planning/scenarios'
*/
getScenarios.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getScenarios.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::getScenarios
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:67
* @route '/api/workforce-planning/scenarios'
*/
getScenarios.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getScenarios.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::getRecommendations
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:46
* @route '/api/workforce-planning/scenarios/{id}/recommendations'
*/
export const getRecommendations = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getRecommendations.url(args, options),
    method: 'get',
})

getRecommendations.definition = {
    methods: ["get","head"],
    url: '/api/workforce-planning/scenarios/{id}/recommendations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::getRecommendations
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:46
* @route '/api/workforce-planning/scenarios/{id}/recommendations'
*/
getRecommendations.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return getRecommendations.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::getRecommendations
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:46
* @route '/api/workforce-planning/scenarios/{id}/recommendations'
*/
getRecommendations.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getRecommendations.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::getRecommendations
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:46
* @route '/api/workforce-planning/scenarios/{id}/recommendations'
*/
getRecommendations.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getRecommendations.url(args, options),
    method: 'head',
})

const WorkforcePlanningController = { createScenario, getScenarios, getRecommendations }

export default WorkforcePlanningController