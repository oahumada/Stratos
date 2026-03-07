import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\MiStratosController::dashboard
* @see app/Http/Controllers/Api/MiStratosController.php:26
* @route '/api/mi-stratos/dashboard'
*/
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/api/mi-stratos/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\MiStratosController::dashboard
* @see app/Http/Controllers/Api/MiStratosController.php:26
* @route '/api/mi-stratos/dashboard'
*/
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\MiStratosController::dashboard
* @see app/Http/Controllers/Api/MiStratosController.php:26
* @route '/api/mi-stratos/dashboard'
*/
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MiStratosController::dashboard
* @see app/Http/Controllers/Api/MiStratosController.php:26
* @route '/api/mi-stratos/dashboard'
*/
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

const MiStratosController = { dashboard }

export default MiStratosController