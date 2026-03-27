import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\SecurityAccessController::index
* @see app/Http/Controllers/Api/SecurityAccessController.php:27
* @route '/api/security/access-logs'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/security/access-logs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\SecurityAccessController::index
* @see app/Http/Controllers/Api/SecurityAccessController.php:27
* @route '/api/security/access-logs'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SecurityAccessController::index
* @see app/Http/Controllers/Api/SecurityAccessController.php:27
* @route '/api/security/access-logs'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SecurityAccessController::index
* @see app/Http/Controllers/Api/SecurityAccessController.php:27
* @route '/api/security/access-logs'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\SecurityAccessController::summary
* @see app/Http/Controllers/Api/SecurityAccessController.php:55
* @route '/api/security/access-logs/summary'
*/
export const summary = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: summary.url(options),
    method: 'get',
})

summary.definition = {
    methods: ["get","head"],
    url: '/api/security/access-logs/summary',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\SecurityAccessController::summary
* @see app/Http/Controllers/Api/SecurityAccessController.php:55
* @route '/api/security/access-logs/summary'
*/
summary.url = (options?: RouteQueryOptions) => {
    return summary.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SecurityAccessController::summary
* @see app/Http/Controllers/Api/SecurityAccessController.php:55
* @route '/api/security/access-logs/summary'
*/
summary.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: summary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SecurityAccessController::summary
* @see app/Http/Controllers/Api/SecurityAccessController.php:55
* @route '/api/security/access-logs/summary'
*/
summary.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: summary.url(options),
    method: 'head',
})

const SecurityAccessController = { index, summary }

export default SecurityAccessController