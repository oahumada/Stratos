import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ScenarioStrategyController::getGapsForAssignment
* @see app/Http/Controllers/Api/ScenarioStrategyController.php:18
* @route '/api/strategic-planning/scenarios/{id}/gaps-for-assignment'
*/
export const getGapsForAssignment = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getGapsForAssignment.url(args, options),
    method: 'get',
})

getGapsForAssignment.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenarios/{id}/gaps-for-assignment',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioStrategyController::getGapsForAssignment
* @see app/Http/Controllers/Api/ScenarioStrategyController.php:18
* @route '/api/strategic-planning/scenarios/{id}/gaps-for-assignment'
*/
getGapsForAssignment.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getGapsForAssignment.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioStrategyController::getGapsForAssignment
* @see app/Http/Controllers/Api/ScenarioStrategyController.php:18
* @route '/api/strategic-planning/scenarios/{id}/gaps-for-assignment'
*/
getGapsForAssignment.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getGapsForAssignment.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioStrategyController::getGapsForAssignment
* @see app/Http/Controllers/Api/ScenarioStrategyController.php:18
* @route '/api/strategic-planning/scenarios/{id}/gaps-for-assignment'
*/
getGapsForAssignment.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getGapsForAssignment.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioStrategyController::assignStrategy
* @see app/Http/Controllers/Api/ScenarioStrategyController.php:66
* @route '/api/strategic-planning/strategies/assign'
*/
export const assignStrategy = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: assignStrategy.url(options),
    method: 'post',
})

assignStrategy.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/strategies/assign',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioStrategyController::assignStrategy
* @see app/Http/Controllers/Api/ScenarioStrategyController.php:66
* @route '/api/strategic-planning/strategies/assign'
*/
assignStrategy.url = (options?: RouteQueryOptions) => {
    return assignStrategy.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioStrategyController::assignStrategy
* @see app/Http/Controllers/Api/ScenarioStrategyController.php:66
* @route '/api/strategic-planning/strategies/assign'
*/
assignStrategy.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: assignStrategy.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioStrategyController::getStrategyPortfolio
* @see app/Http/Controllers/Api/ScenarioStrategyController.php:100
* @route '/api/strategic-planning/strategies/portfolio/{scenario_id}'
*/
export const getStrategyPortfolio = (args: { scenario_id: string | number } | [scenario_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStrategyPortfolio.url(args, options),
    method: 'get',
})

getStrategyPortfolio.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/strategies/portfolio/{scenario_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioStrategyController::getStrategyPortfolio
* @see app/Http/Controllers/Api/ScenarioStrategyController.php:100
* @route '/api/strategic-planning/strategies/portfolio/{scenario_id}'
*/
getStrategyPortfolio.url = (args: { scenario_id: string | number } | [scenario_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenario_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            scenario_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenario_id: args.scenario_id,
    }

    return getStrategyPortfolio.definition.url
            .replace('{scenario_id}', parsedArgs.scenario_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioStrategyController::getStrategyPortfolio
* @see app/Http/Controllers/Api/ScenarioStrategyController.php:100
* @route '/api/strategic-planning/strategies/portfolio/{scenario_id}'
*/
getStrategyPortfolio.get = (args: { scenario_id: string | number } | [scenario_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStrategyPortfolio.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioStrategyController::getStrategyPortfolio
* @see app/Http/Controllers/Api/ScenarioStrategyController.php:100
* @route '/api/strategic-planning/strategies/portfolio/{scenario_id}'
*/
getStrategyPortfolio.head = (args: { scenario_id: string | number } | [scenario_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getStrategyPortfolio.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioStrategyController::getStrategiesByScenario
* @see app/Http/Controllers/Api/ScenarioStrategyController.php:156
* @route '/api/strategic-planning/scenarios/{id}/strategies'
*/
export const getStrategiesByScenario = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStrategiesByScenario.url(args, options),
    method: 'get',
})

getStrategiesByScenario.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenarios/{id}/strategies',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioStrategyController::getStrategiesByScenario
* @see app/Http/Controllers/Api/ScenarioStrategyController.php:156
* @route '/api/strategic-planning/scenarios/{id}/strategies'
*/
getStrategiesByScenario.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getStrategiesByScenario.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioStrategyController::getStrategiesByScenario
* @see app/Http/Controllers/Api/ScenarioStrategyController.php:156
* @route '/api/strategic-planning/scenarios/{id}/strategies'
*/
getStrategiesByScenario.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStrategiesByScenario.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioStrategyController::getStrategiesByScenario
* @see app/Http/Controllers/Api/ScenarioStrategyController.php:156
* @route '/api/strategic-planning/scenarios/{id}/strategies'
*/
getStrategiesByScenario.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getStrategiesByScenario.url(args, options),
    method: 'head',
})

const ScenarioStrategyController = { getGapsForAssignment, assignStrategy, getStrategyPortfolio, getStrategiesByScenario }

export default ScenarioStrategyController