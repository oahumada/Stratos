import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Talento360Controller::metrics
* @see app/Http/Controllers/Api/Talento360Controller.php:15
* @route '/api/strategic-planning/assessments/metrics'
*/
export const metrics = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: metrics.url(options),
    method: 'get',
})

metrics.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/assessments/metrics',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Talento360Controller::metrics
* @see app/Http/Controllers/Api/Talento360Controller.php:15
* @route '/api/strategic-planning/assessments/metrics'
*/
metrics.url = (options?: RouteQueryOptions) => {
    return metrics.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Talento360Controller::metrics
* @see app/Http/Controllers/Api/Talento360Controller.php:15
* @route '/api/strategic-planning/assessments/metrics'
*/
metrics.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: metrics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Talento360Controller::metrics
* @see app/Http/Controllers/Api/Talento360Controller.php:15
* @route '/api/strategic-planning/assessments/metrics'
*/
metrics.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: metrics.url(options),
    method: 'head',
})

const Talento360Controller = { metrics }

export default Talento360Controller