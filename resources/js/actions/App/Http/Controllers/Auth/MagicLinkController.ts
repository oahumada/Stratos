import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Auth\MagicLinkController::requestLink
* @see app/Http/Controllers/Auth/MagicLinkController.php:18
* @route '/magic-link'
*/
export const requestLink = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: requestLink.url(options),
    method: 'post',
})

requestLink.definition = {
    methods: ["post"],
    url: '/magic-link',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Auth\MagicLinkController::requestLink
* @see app/Http/Controllers/Auth/MagicLinkController.php:18
* @route '/magic-link'
*/
requestLink.url = (options?: RouteQueryOptions) => {
    return requestLink.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\MagicLinkController::requestLink
* @see app/Http/Controllers/Auth/MagicLinkController.php:18
* @route '/magic-link'
*/
requestLink.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: requestLink.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\MagicLinkController::requestLink
* @see app/Http/Controllers/Auth/MagicLinkController.php:18
* @route '/magic-link'
*/
const requestLinkForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: requestLink.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\MagicLinkController::requestLink
* @see app/Http/Controllers/Auth/MagicLinkController.php:18
* @route '/magic-link'
*/
requestLinkForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: requestLink.url(options),
    method: 'post',
})

requestLink.form = requestLinkForm

/**
* @see \App\Http\Controllers\Auth\MagicLinkController::authenticate
* @see app/Http/Controllers/Auth/MagicLinkController.php:50
* @route '/magic-login/{user}'
*/
export const authenticate = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: authenticate.url(args, options),
    method: 'get',
})

authenticate.definition = {
    methods: ["get","head"],
    url: '/magic-login/{user}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\MagicLinkController::authenticate
* @see app/Http/Controllers/Auth/MagicLinkController.php:50
* @route '/magic-login/{user}'
*/
authenticate.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { user: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            user: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        user: typeof args.user === 'object'
        ? args.user.id
        : args.user,
    }

    return authenticate.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\MagicLinkController::authenticate
* @see app/Http/Controllers/Auth/MagicLinkController.php:50
* @route '/magic-login/{user}'
*/
authenticate.get = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: authenticate.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\MagicLinkController::authenticate
* @see app/Http/Controllers/Auth/MagicLinkController.php:50
* @route '/magic-login/{user}'
*/
authenticate.head = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: authenticate.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\MagicLinkController::authenticate
* @see app/Http/Controllers/Auth/MagicLinkController.php:50
* @route '/magic-login/{user}'
*/
const authenticateForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: authenticate.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\MagicLinkController::authenticate
* @see app/Http/Controllers/Auth/MagicLinkController.php:50
* @route '/magic-login/{user}'
*/
authenticateForm.get = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: authenticate.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\MagicLinkController::authenticate
* @see app/Http/Controllers/Auth/MagicLinkController.php:50
* @route '/magic-login/{user}'
*/
authenticateForm.head = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: authenticate.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

authenticate.form = authenticateForm

const MagicLinkController = { requestLink, authenticate }

export default MagicLinkController