import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Intelligence\ImpactEngineController::getSummary
* @see app/Http/Controllers/Intelligence/ImpactEngineController.php:20
* @route '/api/investor/impact-summary'
*/
export const getSummary = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSummary.url(options),
    method: 'get',
})

getSummary.definition = {
    methods: ["get","head"],
    url: '/api/investor/impact-summary',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Intelligence\ImpactEngineController::getSummary
* @see app/Http/Controllers/Intelligence/ImpactEngineController.php:20
* @route '/api/investor/impact-summary'
*/
getSummary.url = (options?: RouteQueryOptions) => {
    return getSummary.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Intelligence\ImpactEngineController::getSummary
* @see app/Http/Controllers/Intelligence/ImpactEngineController.php:20
* @route '/api/investor/impact-summary'
*/
getSummary.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSummary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Intelligence\ImpactEngineController::getSummary
* @see app/Http/Controllers/Intelligence/ImpactEngineController.php:20
* @route '/api/investor/impact-summary'
*/
getSummary.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getSummary.url(options),
    method: 'head',
})

const ImpactEngineController = { getSummary }

export default ImpactEngineController