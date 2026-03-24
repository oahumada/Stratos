import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\IntelligenceAggregatesController::summary
* @see app/Http/Controllers/Api/IntelligenceAggregatesController.php:90
* @route '/api/intelligence/aggregates/summary'
*/
export const summary = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: summary.url(options),
    method: 'get',
})

summary.definition = {
    methods: ["get","head"],
    url: '/api/intelligence/aggregates/summary',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\IntelligenceAggregatesController::summary
* @see app/Http/Controllers/Api/IntelligenceAggregatesController.php:90
* @route '/api/intelligence/aggregates/summary'
*/
summary.url = (options?: RouteQueryOptions) => {
    return summary.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\IntelligenceAggregatesController::summary
* @see app/Http/Controllers/Api/IntelligenceAggregatesController.php:90
* @route '/api/intelligence/aggregates/summary'
*/
summary.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: summary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\IntelligenceAggregatesController::summary
* @see app/Http/Controllers/Api/IntelligenceAggregatesController.php:90
* @route '/api/intelligence/aggregates/summary'
*/
summary.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: summary.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\IntelligenceAggregatesController::summary
* @see app/Http/Controllers/Api/IntelligenceAggregatesController.php:90
* @route '/api/intelligence/aggregates/summary'
*/
const summaryForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: summary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\IntelligenceAggregatesController::summary
* @see app/Http/Controllers/Api/IntelligenceAggregatesController.php:90
* @route '/api/intelligence/aggregates/summary'
*/
summaryForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: summary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\IntelligenceAggregatesController::summary
* @see app/Http/Controllers/Api/IntelligenceAggregatesController.php:90
* @route '/api/intelligence/aggregates/summary'
*/
summaryForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: summary.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

summary.form = summaryForm

const aggregates = {
    summary: Object.assign(summary, summary),
}

export default aggregates