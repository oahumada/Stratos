import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
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
* @see \App\Http\Controllers\Api\RBACController::index
* @see app/Http/Controllers/Api/RBACController.php:16
* @route '/api/rbac'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\RBACController::index
* @see app/Http/Controllers/Api/RBACController.php:16
* @route '/api/rbac'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\RBACController::index
* @see app/Http/Controllers/Api/RBACController.php:16
* @route '/api/rbac'
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

/**
* @see \App\Http\Controllers\Api\RBACController::update
* @see app/Http/Controllers/Api/RBACController.php:39
* @route '/api/rbac'
*/
const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RBACController::update
* @see app/Http/Controllers/Api/RBACController.php:39
* @route '/api/rbac'
*/
updateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

update.form = updateForm

const RBACController = { index, update }

export default RBACController