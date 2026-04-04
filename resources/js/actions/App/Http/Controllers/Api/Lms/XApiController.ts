import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\XApiController::store
* @see app/Http/Controllers/Api/Lms/XApiController.php:18
* @route '/api/lms/xapi/statements'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/lms/xapi/statements',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\XApiController::store
* @see app/Http/Controllers/Api/Lms/XApiController.php:18
* @route '/api/lms/xapi/statements'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\XApiController::store
* @see app/Http/Controllers/Api/Lms/XApiController.php:18
* @route '/api/lms/xapi/statements'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\XApiController::store
* @see app/Http/Controllers/Api/Lms/XApiController.php:18
* @route '/api/lms/xapi/statements'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\XApiController::store
* @see app/Http/Controllers/Api/Lms/XApiController.php:18
* @route '/api/lms/xapi/statements'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\Lms\XApiController::index
* @see app/Http/Controllers/Api/Lms/XApiController.php:45
* @route '/api/lms/xapi/statements'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/lms/xapi/statements',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\XApiController::index
* @see app/Http/Controllers/Api/Lms/XApiController.php:45
* @route '/api/lms/xapi/statements'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\XApiController::index
* @see app/Http/Controllers/Api/Lms/XApiController.php:45
* @route '/api/lms/xapi/statements'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\XApiController::index
* @see app/Http/Controllers/Api/Lms/XApiController.php:45
* @route '/api/lms/xapi/statements'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\XApiController::index
* @see app/Http/Controllers/Api/Lms/XApiController.php:45
* @route '/api/lms/xapi/statements'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\XApiController::index
* @see app/Http/Controllers/Api/Lms/XApiController.php:45
* @route '/api/lms/xapi/statements'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\XApiController::index
* @see app/Http/Controllers/Api/Lms/XApiController.php:45
* @route '/api/lms/xapi/statements'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Api\Lms\XApiController::activityStats
* @see app/Http/Controllers/Api/Lms/XApiController.php:60
* @route '/api/lms/xapi/activities/{objectId}/stats'
*/
export const activityStats = (args: { objectId: string | number } | [objectId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: activityStats.url(args, options),
    method: 'get',
})

activityStats.definition = {
    methods: ["get","head"],
    url: '/api/lms/xapi/activities/{objectId}/stats',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\XApiController::activityStats
* @see app/Http/Controllers/Api/Lms/XApiController.php:60
* @route '/api/lms/xapi/activities/{objectId}/stats'
*/
activityStats.url = (args: { objectId: string | number } | [objectId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { objectId: args }
    }

    if (Array.isArray(args)) {
        args = {
            objectId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        objectId: args.objectId,
    }

    return activityStats.definition.url
            .replace('{objectId}', parsedArgs.objectId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\XApiController::activityStats
* @see app/Http/Controllers/Api/Lms/XApiController.php:60
* @route '/api/lms/xapi/activities/{objectId}/stats'
*/
activityStats.get = (args: { objectId: string | number } | [objectId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: activityStats.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\XApiController::activityStats
* @see app/Http/Controllers/Api/Lms/XApiController.php:60
* @route '/api/lms/xapi/activities/{objectId}/stats'
*/
activityStats.head = (args: { objectId: string | number } | [objectId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: activityStats.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\XApiController::activityStats
* @see app/Http/Controllers/Api/Lms/XApiController.php:60
* @route '/api/lms/xapi/activities/{objectId}/stats'
*/
const activityStatsForm = (args: { objectId: string | number } | [objectId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: activityStats.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\XApiController::activityStats
* @see app/Http/Controllers/Api/Lms/XApiController.php:60
* @route '/api/lms/xapi/activities/{objectId}/stats'
*/
activityStatsForm.get = (args: { objectId: string | number } | [objectId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: activityStats.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\XApiController::activityStats
* @see app/Http/Controllers/Api/Lms/XApiController.php:60
* @route '/api/lms/xapi/activities/{objectId}/stats'
*/
activityStatsForm.head = (args: { objectId: string | number } | [objectId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: activityStats.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

activityStats.form = activityStatsForm

const XApiController = { store, index, activityStats }

export default XApiController