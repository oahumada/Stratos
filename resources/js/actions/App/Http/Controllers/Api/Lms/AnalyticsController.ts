import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\AnalyticsController::overview
* @see app/Http/Controllers/Api/Lms/AnalyticsController.php:16
* @route '/api/lms/analytics/overview'
*/
export const overview = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: overview.url(options),
    method: 'get',
})

overview.definition = {
    methods: ["get","head"],
    url: '/api/lms/analytics/overview',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\AnalyticsController::overview
* @see app/Http/Controllers/Api/Lms/AnalyticsController.php:16
* @route '/api/lms/analytics/overview'
*/
overview.url = (options?: RouteQueryOptions) => {
    return overview.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\AnalyticsController::overview
* @see app/Http/Controllers/Api/Lms/AnalyticsController.php:16
* @route '/api/lms/analytics/overview'
*/
overview.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: overview.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\AnalyticsController::overview
* @see app/Http/Controllers/Api/Lms/AnalyticsController.php:16
* @route '/api/lms/analytics/overview'
*/
overview.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: overview.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\AnalyticsController::overview
* @see app/Http/Controllers/Api/Lms/AnalyticsController.php:16
* @route '/api/lms/analytics/overview'
*/
const overviewForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: overview.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\AnalyticsController::overview
* @see app/Http/Controllers/Api/Lms/AnalyticsController.php:16
* @route '/api/lms/analytics/overview'
*/
overviewForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: overview.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\AnalyticsController::overview
* @see app/Http/Controllers/Api/Lms/AnalyticsController.php:16
* @route '/api/lms/analytics/overview'
*/
overviewForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: overview.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

overview.form = overviewForm

const AnalyticsController = { overview }

export default AnalyticsController