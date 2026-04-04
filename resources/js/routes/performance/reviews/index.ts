import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\PerformanceController::index
* @see app/Http/Controllers/Api/PerformanceController.php:85
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
export const index = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/performance/cycles/{cycleId}/reviews',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PerformanceController::index
* @see app/Http/Controllers/Api/PerformanceController.php:85
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
index.url = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { cycleId: args }
    }

    if (Array.isArray(args)) {
        args = {
            cycleId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        cycleId: args.cycleId,
    }

    return index.definition.url
            .replace('{cycleId}', parsedArgs.cycleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PerformanceController::index
* @see app/Http/Controllers/Api/PerformanceController.php:85
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
index.get = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::index
* @see app/Http/Controllers/Api/PerformanceController.php:85
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
index.head = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::index
* @see app/Http/Controllers/Api/PerformanceController.php:85
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
const indexForm = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::index
* @see app/Http/Controllers/Api/PerformanceController.php:85
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
indexForm.get = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::index
* @see app/Http/Controllers/Api/PerformanceController.php:85
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
indexForm.head = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, {
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
* @see app/Http/Controllers/Api/PerformanceController.php:99
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
export const store = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/performance/cycles/{cycleId}/reviews',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\PerformanceController::store
* @see app/Http/Controllers/Api/PerformanceController.php:99
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
store.url = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { cycleId: args }
    }

    if (Array.isArray(args)) {
        args = {
            cycleId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        cycleId: args.cycleId,
    }

    return store.definition.url
            .replace('{cycleId}', parsedArgs.cycleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PerformanceController::store
* @see app/Http/Controllers/Api/PerformanceController.php:99
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
store.post = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::store
* @see app/Http/Controllers/Api/PerformanceController.php:99
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
const storeForm = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::store
* @see app/Http/Controllers/Api/PerformanceController.php:99
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
storeForm.post = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\PerformanceController::update
* @see app/Http/Controllers/Api/PerformanceController.php:125
* @route '/api/performance/cycles/{cycleId}/reviews/{reviewId}'
*/
export const update = (args: { cycleId: string | number, reviewId: string | number } | [cycleId: string | number, reviewId: string | number ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/api/performance/cycles/{cycleId}/reviews/{reviewId}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\PerformanceController::update
* @see app/Http/Controllers/Api/PerformanceController.php:125
* @route '/api/performance/cycles/{cycleId}/reviews/{reviewId}'
*/
update.url = (args: { cycleId: string | number, reviewId: string | number } | [cycleId: string | number, reviewId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            cycleId: args[0],
            reviewId: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        cycleId: args.cycleId,
        reviewId: args.reviewId,
    }

    return update.definition.url
            .replace('{cycleId}', parsedArgs.cycleId.toString())
            .replace('{reviewId}', parsedArgs.reviewId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PerformanceController::update
* @see app/Http/Controllers/Api/PerformanceController.php:125
* @route '/api/performance/cycles/{cycleId}/reviews/{reviewId}'
*/
update.patch = (args: { cycleId: string | number, reviewId: string | number } | [cycleId: string | number, reviewId: string | number ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::update
* @see app/Http/Controllers/Api/PerformanceController.php:125
* @route '/api/performance/cycles/{cycleId}/reviews/{reviewId}'
*/
const updateForm = (args: { cycleId: string | number, reviewId: string | number } | [cycleId: string | number, reviewId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::update
* @see app/Http/Controllers/Api/PerformanceController.php:125
* @route '/api/performance/cycles/{cycleId}/reviews/{reviewId}'
*/
updateForm.patch = (args: { cycleId: string | number, reviewId: string | number } | [cycleId: string | number, reviewId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

const reviews = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    update: Object.assign(update, update),
}

export default reviews