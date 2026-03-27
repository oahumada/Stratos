import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Auth\MagicLinkController::request
* @see app/Http/Controllers/Auth/MagicLinkController.php:18
* @route '/magic-link'
*/
export const request = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: request.url(options),
    method: 'post',
})

request.definition = {
    methods: ["post"],
    url: '/magic-link',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Auth\MagicLinkController::request
* @see app/Http/Controllers/Auth/MagicLinkController.php:18
* @route '/magic-link'
*/
request.url = (options?: RouteQueryOptions) => {
    return request.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\MagicLinkController::request
* @see app/Http/Controllers/Auth/MagicLinkController.php:18
* @route '/magic-link'
*/
request.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: request.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\MagicLinkController::login
* @see app/Http/Controllers/Auth/MagicLinkController.php:50
* @route '/magic-login/{user}'
*/
export const login = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(args, options),
    method: 'get',
})

login.definition = {
    methods: ["get","head"],
    url: '/magic-login/{user}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\MagicLinkController::login
* @see app/Http/Controllers/Auth/MagicLinkController.php:50
* @route '/magic-login/{user}'
*/
login.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return login.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\MagicLinkController::login
* @see app/Http/Controllers/Auth/MagicLinkController.php:50
* @route '/magic-login/{user}'
*/
login.get = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\MagicLinkController::login
* @see app/Http/Controllers/Auth/MagicLinkController.php:50
* @route '/magic-login/{user}'
*/
login.head = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: login.url(args, options),
    method: 'head',
})

const magic = {
    request: Object.assign(request, request),
    login: Object.assign(login, login),
}

export default magic