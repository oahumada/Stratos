import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\AlertController::index
* @see app/Http/Controllers/Api/AlertController.php:111
* @route '/api/alerts/history'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/alerts/history',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AlertController::index
* @see app/Http/Controllers/Api/AlertController.php:111
* @route '/api/alerts/history'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::index
* @see app/Http/Controllers/Api/AlertController.php:111
* @route '/api/alerts/history'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::index
* @see app/Http/Controllers/Api/AlertController.php:111
* @route '/api/alerts/history'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AlertController::show
* @see app/Http/Controllers/Api/AlertController.php:126
* @route '/api/alerts/history/{alert}'
*/
export const show = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/alerts/history/{alert}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AlertController::show
* @see app/Http/Controllers/Api/AlertController.php:126
* @route '/api/alerts/history/{alert}'
*/
show.url = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { alert: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { alert: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            alert: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        alert: typeof args.alert === 'object'
        ? args.alert.id
        : args.alert,
    }

    return show.definition.url
            .replace('{alert}', parsedArgs.alert.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::show
* @see app/Http/Controllers/Api/AlertController.php:126
* @route '/api/alerts/history/{alert}'
*/
show.get = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::show
* @see app/Http/Controllers/Api/AlertController.php:126
* @route '/api/alerts/history/{alert}'
*/
show.head = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

const history = {
    index: Object.assign(index, index),
    show: Object.assign(show, show),
}

export default history