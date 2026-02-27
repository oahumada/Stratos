import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Talento360Controller::metrics
* @see app/Http/Controllers/Api/Talento360Controller.php:17
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
* @see app/Http/Controllers/Api/Talento360Controller.php:17
* @route '/api/strategic-planning/assessments/metrics'
*/
metrics.url = (options?: RouteQueryOptions) => {
    return metrics.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Talento360Controller::metrics
* @see app/Http/Controllers/Api/Talento360Controller.php:17
* @route '/api/strategic-planning/assessments/metrics'
*/
metrics.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: metrics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Talento360Controller::metrics
* @see app/Http/Controllers/Api/Talento360Controller.php:17
* @route '/api/strategic-planning/assessments/metrics'
*/
metrics.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: metrics.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Talento360Controller::metrics
* @see app/Http/Controllers/Api/Talento360Controller.php:17
* @route '/api/strategic-planning/assessments/metrics'
*/
const metricsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: metrics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Talento360Controller::metrics
* @see app/Http/Controllers/Api/Talento360Controller.php:17
* @route '/api/strategic-planning/assessments/metrics'
*/
metricsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: metrics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Talento360Controller::metrics
* @see app/Http/Controllers/Api/Talento360Controller.php:17
* @route '/api/strategic-planning/assessments/metrics'
*/
metricsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: metrics.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

metrics.form = metricsForm

const Talento360Controller = { metrics }

export default Talento360Controller