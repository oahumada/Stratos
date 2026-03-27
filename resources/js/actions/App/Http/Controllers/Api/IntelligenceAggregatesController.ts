import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\IntelligenceAggregatesController::index
* @see app/Http/Controllers/Api/IntelligenceAggregatesController.php:24
* @route '/api/intelligence/aggregates'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/intelligence/aggregates',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\IntelligenceAggregatesController::index
* @see app/Http/Controllers/Api/IntelligenceAggregatesController.php:24
* @route '/api/intelligence/aggregates'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\IntelligenceAggregatesController::index
* @see app/Http/Controllers/Api/IntelligenceAggregatesController.php:24
* @route '/api/intelligence/aggregates'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\IntelligenceAggregatesController::index
* @see app/Http/Controllers/Api/IntelligenceAggregatesController.php:24
* @route '/api/intelligence/aggregates'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

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

const IntelligenceAggregatesController = { index, summary }

export default IntelligenceAggregatesController