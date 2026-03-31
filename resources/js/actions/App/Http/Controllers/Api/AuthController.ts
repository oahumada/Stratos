import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\AuthController::me
* @see app/Http/Controllers/Api/AuthController.php:13
* @route '/api/auth/me'
*/
export const me = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: me.url(options),
    method: 'get',
})

me.definition = {
    methods: ["get","head"],
    url: '/api/auth/me',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AuthController::me
* @see app/Http/Controllers/Api/AuthController.php:13
* @route '/api/auth/me'
*/
me.url = (options?: RouteQueryOptions) => {
    return me.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AuthController::me
* @see app/Http/Controllers/Api/AuthController.php:13
* @route '/api/auth/me'
*/
me.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: me.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AuthController::me
* @see app/Http/Controllers/Api/AuthController.php:13
* @route '/api/auth/me'
*/
me.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: me.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AuthController::me
* @see app/Http/Controllers/Api/AuthController.php:13
* @route '/api/auth/me'
*/
const meForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: me.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AuthController::me
* @see app/Http/Controllers/Api/AuthController.php:13
* @route '/api/auth/me'
*/
meForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: me.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AuthController::me
* @see app/Http/Controllers/Api/AuthController.php:13
* @route '/api/auth/me'
*/
meForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: me.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

me.form = meForm

const AuthController = { me }

export default AuthController