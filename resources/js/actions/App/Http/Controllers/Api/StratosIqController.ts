import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\StratosIqController::getTrends
* @see app/Http/Controllers/Api/StratosIqController.php:24
* @route '/api/stratos-iq/{organizationId}'
*/
export const getTrends = (args: { organizationId: string | number } | [organizationId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getTrends.url(args, options),
    method: 'get',
})

getTrends.definition = {
    methods: ["get","head"],
    url: '/api/stratos-iq/{organizationId}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\StratosIqController::getTrends
* @see app/Http/Controllers/Api/StratosIqController.php:24
* @route '/api/stratos-iq/{organizationId}'
*/
getTrends.url = (args: { organizationId: string | number } | [organizationId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organizationId: args }
    }

    if (Array.isArray(args)) {
        args = {
            organizationId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organizationId: args.organizationId,
    }

    return getTrends.definition.url
            .replace('{organizationId}', parsedArgs.organizationId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\StratosIqController::getTrends
* @see app/Http/Controllers/Api/StratosIqController.php:24
* @route '/api/stratos-iq/{organizationId}'
*/
getTrends.get = (args: { organizationId: string | number } | [organizationId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getTrends.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\StratosIqController::getTrends
* @see app/Http/Controllers/Api/StratosIqController.php:24
* @route '/api/stratos-iq/{organizationId}'
*/
getTrends.head = (args: { organizationId: string | number } | [organizationId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getTrends.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\StratosIqController::captureSnapshot
* @see app/Http/Controllers/Api/StratosIqController.php:40
* @route '/api/stratos-iq/{organizationId}/snapshot'
*/
export const captureSnapshot = (args: { organizationId: string | number } | [organizationId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: captureSnapshot.url(args, options),
    method: 'post',
})

captureSnapshot.definition = {
    methods: ["post"],
    url: '/api/stratos-iq/{organizationId}/snapshot',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\StratosIqController::captureSnapshot
* @see app/Http/Controllers/Api/StratosIqController.php:40
* @route '/api/stratos-iq/{organizationId}/snapshot'
*/
captureSnapshot.url = (args: { organizationId: string | number } | [organizationId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organizationId: args }
    }

    if (Array.isArray(args)) {
        args = {
            organizationId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organizationId: args.organizationId,
    }

    return captureSnapshot.definition.url
            .replace('{organizationId}', parsedArgs.organizationId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\StratosIqController::captureSnapshot
* @see app/Http/Controllers/Api/StratosIqController.php:40
* @route '/api/stratos-iq/{organizationId}/snapshot'
*/
captureSnapshot.post = (args: { organizationId: string | number } | [organizationId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: captureSnapshot.url(args, options),
    method: 'post',
})

const StratosIqController = { getTrends, captureSnapshot }

export default StratosIqController