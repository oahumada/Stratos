import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\RBACController::index
* @see app/Http/Controllers/Api/RBACController.php:16
* @route '/api/rbac'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/rbac',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\RBACController::index
* @see app/Http/Controllers/Api/RBACController.php:16
* @route '/api/rbac'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\RBACController::index
* @see app/Http/Controllers/Api/RBACController.php:16
* @route '/api/rbac'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\RBACController::index
* @see app/Http/Controllers/Api/RBACController.php:16
* @route '/api/rbac'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\RBACController::update
* @see app/Http/Controllers/Api/RBACController.php:39
* @route '/api/rbac'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/api/rbac',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\RBACController::update
* @see app/Http/Controllers/Api/RBACController.php:39
* @route '/api/rbac'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\RBACController::update
* @see app/Http/Controllers/Api/RBACController.php:39
* @route '/api/rbac'
*/
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

const RBACController = { index, update }

export default RBACController