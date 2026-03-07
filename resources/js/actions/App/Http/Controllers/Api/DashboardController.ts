import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\DashboardController::metrics
* @see app/Http/Controllers/Api/DashboardController.php:14
* @route '/api/dashboard/metrics'
*/
export const metrics = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: metrics.url(options),
    method: 'get',
})

metrics.definition = {
    methods: ["get","head"],
    url: '/api/dashboard/metrics',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\DashboardController::metrics
* @see app/Http/Controllers/Api/DashboardController.php:14
* @route '/api/dashboard/metrics'
*/
metrics.url = (options?: RouteQueryOptions) => {
    return metrics.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\DashboardController::metrics
* @see app/Http/Controllers/Api/DashboardController.php:14
* @route '/api/dashboard/metrics'
*/
metrics.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: metrics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\DashboardController::metrics
* @see app/Http/Controllers/Api/DashboardController.php:14
* @route '/api/dashboard/metrics'
*/
metrics.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: metrics.url(options),
    method: 'head',
})

const DashboardController = { metrics }

export default DashboardController