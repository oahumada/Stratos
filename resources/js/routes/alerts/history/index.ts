import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\AlertController::index
* @see app/Http/Controllers/Api/AlertController.php:101
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
* @see app/Http/Controllers/Api/AlertController.php:101
* @route '/api/alerts/history'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::index
* @see app/Http/Controllers/Api/AlertController.php:101
* @route '/api/alerts/history'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::index
* @see app/Http/Controllers/Api/AlertController.php:101
* @route '/api/alerts/history'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AlertController::index
* @see app/Http/Controllers/Api/AlertController.php:101
* @route '/api/alerts/history'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::index
* @see app/Http/Controllers/Api/AlertController.php:101
* @route '/api/alerts/history'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::index
* @see app/Http/Controllers/Api/AlertController.php:101
* @route '/api/alerts/history'
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
* @see \App\Http\Controllers\Api\AlertController::show
* @see app/Http/Controllers/Api/AlertController.php:116
* @route '/api/alerts/history/{alert}'
*/
export const show = (args: { alert: number | { id: number } } | [alert: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/alerts/history/{alert}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AlertController::show
* @see app/Http/Controllers/Api/AlertController.php:116
* @route '/api/alerts/history/{alert}'
*/
show.url = (args: { alert: number | { id: number } } | [alert: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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
* @see app/Http/Controllers/Api/AlertController.php:116
* @route '/api/alerts/history/{alert}'
*/
show.get = (args: { alert: number | { id: number } } | [alert: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::show
* @see app/Http/Controllers/Api/AlertController.php:116
* @route '/api/alerts/history/{alert}'
*/
show.head = (args: { alert: number | { id: number } } | [alert: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AlertController::show
* @see app/Http/Controllers/Api/AlertController.php:116
* @route '/api/alerts/history/{alert}'
*/
const showForm = (args: { alert: number | { id: number } } | [alert: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::show
* @see app/Http/Controllers/Api/AlertController.php:116
* @route '/api/alerts/history/{alert}'
*/
showForm.get = (args: { alert: number | { id: number } } | [alert: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::show
* @see app/Http/Controllers/Api/AlertController.php:116
* @route '/api/alerts/history/{alert}'
*/
showForm.head = (args: { alert: number | { id: number } } | [alert: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

const history = {
    index: Object.assign(index, index),
    show: Object.assign(show, show),
}

export default history