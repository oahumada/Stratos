import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::simulateGrowth
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:16
* @route '/api/strategic-planning/scenarios/{id}/simulate-growth'
*/
export const simulateGrowth = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: simulateGrowth.url(args, options),
    method: 'post',
})

simulateGrowth.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/{id}/simulate-growth',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::simulateGrowth
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:16
* @route '/api/strategic-planning/scenarios/{id}/simulate-growth'
*/
simulateGrowth.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return simulateGrowth.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::simulateGrowth
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:16
* @route '/api/strategic-planning/scenarios/{id}/simulate-growth'
*/
simulateGrowth.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: simulateGrowth.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::simulateGrowth
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:16
* @route '/api/strategic-planning/scenarios/{id}/simulate-growth'
*/
const simulateGrowthForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: simulateGrowth.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::simulateGrowth
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:16
* @route '/api/strategic-planning/scenarios/{id}/simulate-growth'
*/
simulateGrowthForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: simulateGrowth.url(args, options),
    method: 'post',
})

simulateGrowth.form = simulateGrowthForm

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getCriticalTalents
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:85
* @route '/api/strategic-planning/critical-talents'
*/
export const getCriticalTalents = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCriticalTalents.url(options),
    method: 'get',
})

getCriticalTalents.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/critical-talents',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getCriticalTalents
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:85
* @route '/api/strategic-planning/critical-talents'
*/
getCriticalTalents.url = (options?: RouteQueryOptions) => {
    return getCriticalTalents.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getCriticalTalents
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:85
* @route '/api/strategic-planning/critical-talents'
*/
getCriticalTalents.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCriticalTalents.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getCriticalTalents
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:85
* @route '/api/strategic-planning/critical-talents'
*/
getCriticalTalents.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCriticalTalents.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getCriticalTalents
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:85
* @route '/api/strategic-planning/critical-talents'
*/
const getCriticalTalentsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCriticalTalents.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getCriticalTalents
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:85
* @route '/api/strategic-planning/critical-talents'
*/
getCriticalTalentsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCriticalTalents.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getCriticalTalents
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:85
* @route '/api/strategic-planning/critical-talents'
*/
getCriticalTalentsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCriticalTalents.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getCriticalTalents.form = getCriticalTalentsForm

const ScenarioSimulationController = { simulateGrowth, getCriticalTalents }

export default ScenarioSimulationController