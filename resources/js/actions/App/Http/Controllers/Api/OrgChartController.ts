import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\OrgChartController::__invoke
* @see app/Http/Controllers/Api/OrgChartController.php:27
* @route '/api/strategic-planning/scenarios/{scenarioId}/org-chart'
*/
export const __invoke = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: __invoke.url(args, options),
    method: 'get',
})

__invoke.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenarios/{scenarioId}/org-chart',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\OrgChartController::__invoke
* @see app/Http/Controllers/Api/OrgChartController.php:27
* @route '/api/strategic-planning/scenarios/{scenarioId}/org-chart'
*/
__invoke.url = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenarioId: args }
    }

    if (Array.isArray(args)) {
        args = {
            scenarioId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenarioId: args.scenarioId,
    }

    return __invoke.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\OrgChartController::__invoke
* @see app/Http/Controllers/Api/OrgChartController.php:27
* @route '/api/strategic-planning/scenarios/{scenarioId}/org-chart'
*/
__invoke.get = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: __invoke.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\OrgChartController::__invoke
* @see app/Http/Controllers/Api/OrgChartController.php:27
* @route '/api/strategic-planning/scenarios/{scenarioId}/org-chart'
*/
__invoke.head = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: __invoke.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\OrgChartController::__invoke
* @see app/Http/Controllers/Api/OrgChartController.php:27
* @route '/api/strategic-planning/scenarios/{scenarioId}/org-chart'
*/
const __invokeForm = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: __invoke.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\OrgChartController::__invoke
* @see app/Http/Controllers/Api/OrgChartController.php:27
* @route '/api/strategic-planning/scenarios/{scenarioId}/org-chart'
*/
__invokeForm.get = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: __invoke.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\OrgChartController::__invoke
* @see app/Http/Controllers/Api/OrgChartController.php:27
* @route '/api/strategic-planning/scenarios/{scenarioId}/org-chart'
*/
__invokeForm.head = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: __invoke.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

__invoke.form = __invokeForm

const OrgChartController = { __invoke }

export default OrgChartController