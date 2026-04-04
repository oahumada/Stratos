import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\PerformanceController::index
* @see app/Http/Controllers/Api/PerformanceController.php:19
* @route '/api/performance/cycles'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/performance/cycles',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PerformanceController::index
* @see app/Http/Controllers/Api/PerformanceController.php:19
* @route '/api/performance/cycles'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PerformanceController::index
* @see app/Http/Controllers/Api/PerformanceController.php:19
* @route '/api/performance/cycles'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::index
* @see app/Http/Controllers/Api/PerformanceController.php:19
* @route '/api/performance/cycles'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::index
* @see app/Http/Controllers/Api/PerformanceController.php:19
* @route '/api/performance/cycles'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::index
* @see app/Http/Controllers/Api/PerformanceController.php:19
* @route '/api/performance/cycles'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::index
* @see app/Http/Controllers/Api/PerformanceController.php:19
* @route '/api/performance/cycles'
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
* @see \App\Http\Controllers\Api\PerformanceController::store
* @see app/Http/Controllers/Api/PerformanceController.php:31
* @route '/api/performance/cycles'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/performance/cycles',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\PerformanceController::store
* @see app/Http/Controllers/Api/PerformanceController.php:31
* @route '/api/performance/cycles'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PerformanceController::store
* @see app/Http/Controllers/Api/PerformanceController.php:31
* @route '/api/performance/cycles'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::store
* @see app/Http/Controllers/Api/PerformanceController.php:31
* @route '/api/performance/cycles'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::store
* @see app/Http/Controllers/Api/PerformanceController.php:31
* @route '/api/performance/cycles'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\PerformanceController::show
* @see app/Http/Controllers/Api/PerformanceController.php:52
* @route '/api/performance/cycles/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/performance/cycles/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PerformanceController::show
* @see app/Http/Controllers/Api/PerformanceController.php:52
* @route '/api/performance/cycles/{id}'
*/
show.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return show.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PerformanceController::show
* @see app/Http/Controllers/Api/PerformanceController.php:52
* @route '/api/performance/cycles/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::show
* @see app/Http/Controllers/Api/PerformanceController.php:52
* @route '/api/performance/cycles/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::show
* @see app/Http/Controllers/Api/PerformanceController.php:52
* @route '/api/performance/cycles/{id}'
*/
const showForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::show
* @see app/Http/Controllers/Api/PerformanceController.php:52
* @route '/api/performance/cycles/{id}'
*/
showForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::show
* @see app/Http/Controllers/Api/PerformanceController.php:52
* @route '/api/performance/cycles/{id}'
*/
showForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\Api\PerformanceController::activate
* @see app/Http/Controllers/Api/PerformanceController.php:63
* @route '/api/performance/cycles/{id}/activate'
*/
export const activate = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: activate.url(args, options),
    method: 'post',
})

activate.definition = {
    methods: ["post"],
    url: '/api/performance/cycles/{id}/activate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\PerformanceController::activate
* @see app/Http/Controllers/Api/PerformanceController.php:63
* @route '/api/performance/cycles/{id}/activate'
*/
activate.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return activate.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PerformanceController::activate
* @see app/Http/Controllers/Api/PerformanceController.php:63
* @route '/api/performance/cycles/{id}/activate'
*/
activate.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: activate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::activate
* @see app/Http/Controllers/Api/PerformanceController.php:63
* @route '/api/performance/cycles/{id}/activate'
*/
const activateForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: activate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::activate
* @see app/Http/Controllers/Api/PerformanceController.php:63
* @route '/api/performance/cycles/{id}/activate'
*/
activateForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: activate.url(args, options),
    method: 'post',
})

activate.form = activateForm

/**
* @see \App\Http\Controllers\Api\PerformanceController::close
* @see app/Http/Controllers/Api/PerformanceController.php:73
* @route '/api/performance/cycles/{id}/close'
*/
export const close = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: close.url(args, options),
    method: 'post',
})

close.definition = {
    methods: ["post"],
    url: '/api/performance/cycles/{id}/close',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\PerformanceController::close
* @see app/Http/Controllers/Api/PerformanceController.php:73
* @route '/api/performance/cycles/{id}/close'
*/
close.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return close.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PerformanceController::close
* @see app/Http/Controllers/Api/PerformanceController.php:73
* @route '/api/performance/cycles/{id}/close'
*/
close.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: close.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::close
* @see app/Http/Controllers/Api/PerformanceController.php:73
* @route '/api/performance/cycles/{id}/close'
*/
const closeForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: close.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::close
* @see app/Http/Controllers/Api/PerformanceController.php:73
* @route '/api/performance/cycles/{id}/close'
*/
closeForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: close.url(args, options),
    method: 'post',
})

close.form = closeForm

/**
* @see \App\Http\Controllers\Api\PerformanceController::calibrate
* @see app/Http/Controllers/Api/PerformanceController.php:149
* @route '/api/performance/cycles/{id}/calibrate'
*/
export const calibrate = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: calibrate.url(args, options),
    method: 'post',
})

calibrate.definition = {
    methods: ["post"],
    url: '/api/performance/cycles/{id}/calibrate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\PerformanceController::calibrate
* @see app/Http/Controllers/Api/PerformanceController.php:149
* @route '/api/performance/cycles/{id}/calibrate'
*/
calibrate.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return calibrate.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PerformanceController::calibrate
* @see app/Http/Controllers/Api/PerformanceController.php:149
* @route '/api/performance/cycles/{id}/calibrate'
*/
calibrate.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: calibrate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::calibrate
* @see app/Http/Controllers/Api/PerformanceController.php:149
* @route '/api/performance/cycles/{id}/calibrate'
*/
const calibrateForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: calibrate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::calibrate
* @see app/Http/Controllers/Api/PerformanceController.php:149
* @route '/api/performance/cycles/{id}/calibrate'
*/
calibrateForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: calibrate.url(args, options),
    method: 'post',
})

calibrate.form = calibrateForm

/**
* @see \App\Http\Controllers\Api\PerformanceController::insights
* @see app/Http/Controllers/Api/PerformanceController.php:159
* @route '/api/performance/cycles/{id}/insights'
*/
export const insights = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: insights.url(args, options),
    method: 'get',
})

insights.definition = {
    methods: ["get","head"],
    url: '/api/performance/cycles/{id}/insights',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PerformanceController::insights
* @see app/Http/Controllers/Api/PerformanceController.php:159
* @route '/api/performance/cycles/{id}/insights'
*/
insights.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return insights.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PerformanceController::insights
* @see app/Http/Controllers/Api/PerformanceController.php:159
* @route '/api/performance/cycles/{id}/insights'
*/
insights.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: insights.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::insights
* @see app/Http/Controllers/Api/PerformanceController.php:159
* @route '/api/performance/cycles/{id}/insights'
*/
insights.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: insights.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::insights
* @see app/Http/Controllers/Api/PerformanceController.php:159
* @route '/api/performance/cycles/{id}/insights'
*/
const insightsForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: insights.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::insights
* @see app/Http/Controllers/Api/PerformanceController.php:159
* @route '/api/performance/cycles/{id}/insights'
*/
insightsForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: insights.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::insights
* @see app/Http/Controllers/Api/PerformanceController.php:159
* @route '/api/performance/cycles/{id}/insights'
*/
insightsForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: insights.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

insights.form = insightsForm

const cycles = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    show: Object.assign(show, show),
    activate: Object.assign(activate, activate),
    close: Object.assign(close, close),
    calibrate: Object.assign(calibrate, calibrate),
    insights: Object.assign(insights, insights),
}

export default cycles