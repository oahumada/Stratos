import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\PxController::index
* @see app/Http/Controllers/Api/PxController.php:22
* @route '/api/px/campaigns'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/px/campaigns',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PxController::index
* @see app/Http/Controllers/Api/PxController.php:22
* @route '/api/px/campaigns'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PxController::index
* @see app/Http/Controllers/Api/PxController.php:22
* @route '/api/px/campaigns'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PxController::index
* @see app/Http/Controllers/Api/PxController.php:22
* @route '/api/px/campaigns'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PxController::trigger
* @see app/Http/Controllers/Api/PxController.php:36
* @route '/api/px/campaigns/trigger'
*/
export const trigger = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: trigger.url(options),
    method: 'post',
})

trigger.definition = {
    methods: ["post"],
    url: '/api/px/campaigns/trigger',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\PxController::trigger
* @see app/Http/Controllers/Api/PxController.php:36
* @route '/api/px/campaigns/trigger'
*/
trigger.url = (options?: RouteQueryOptions) => {
    return trigger.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PxController::trigger
* @see app/Http/Controllers/Api/PxController.php:36
* @route '/api/px/campaigns/trigger'
*/
trigger.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: trigger.url(options),
    method: 'post',
})

const PxController = { index, trigger }

export default PxController