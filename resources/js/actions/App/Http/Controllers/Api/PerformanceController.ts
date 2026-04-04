import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\PerformanceController::indexCycles
* @see app/Http/Controllers/Api/PerformanceController.php:19
* @route '/api/performance/cycles'
*/
export const indexCycles = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexCycles.url(options),
    method: 'get',
})

indexCycles.definition = {
    methods: ["get","head"],
    url: '/api/performance/cycles',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PerformanceController::indexCycles
* @see app/Http/Controllers/Api/PerformanceController.php:19
* @route '/api/performance/cycles'
*/
indexCycles.url = (options?: RouteQueryOptions) => {
    return indexCycles.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PerformanceController::indexCycles
* @see app/Http/Controllers/Api/PerformanceController.php:19
* @route '/api/performance/cycles'
*/
indexCycles.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexCycles.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::indexCycles
* @see app/Http/Controllers/Api/PerformanceController.php:19
* @route '/api/performance/cycles'
*/
indexCycles.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: indexCycles.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::indexCycles
* @see app/Http/Controllers/Api/PerformanceController.php:19
* @route '/api/performance/cycles'
*/
const indexCyclesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexCycles.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::indexCycles
* @see app/Http/Controllers/Api/PerformanceController.php:19
* @route '/api/performance/cycles'
*/
indexCyclesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexCycles.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::indexCycles
* @see app/Http/Controllers/Api/PerformanceController.php:19
* @route '/api/performance/cycles'
*/
indexCyclesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexCycles.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

indexCycles.form = indexCyclesForm

/**
* @see \App\Http\Controllers\Api\PerformanceController::storeCycle
* @see app/Http/Controllers/Api/PerformanceController.php:31
* @route '/api/performance/cycles'
*/
export const storeCycle = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeCycle.url(options),
    method: 'post',
})

storeCycle.definition = {
    methods: ["post"],
    url: '/api/performance/cycles',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\PerformanceController::storeCycle
* @see app/Http/Controllers/Api/PerformanceController.php:31
* @route '/api/performance/cycles'
*/
storeCycle.url = (options?: RouteQueryOptions) => {
    return storeCycle.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PerformanceController::storeCycle
* @see app/Http/Controllers/Api/PerformanceController.php:31
* @route '/api/performance/cycles'
*/
storeCycle.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeCycle.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::storeCycle
* @see app/Http/Controllers/Api/PerformanceController.php:31
* @route '/api/performance/cycles'
*/
const storeCycleForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeCycle.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::storeCycle
* @see app/Http/Controllers/Api/PerformanceController.php:31
* @route '/api/performance/cycles'
*/
storeCycleForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeCycle.url(options),
    method: 'post',
})

storeCycle.form = storeCycleForm

/**
* @see \App\Http\Controllers\Api\PerformanceController::showCycle
* @see app/Http/Controllers/Api/PerformanceController.php:52
* @route '/api/performance/cycles/{id}'
*/
export const showCycle = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showCycle.url(args, options),
    method: 'get',
})

showCycle.definition = {
    methods: ["get","head"],
    url: '/api/performance/cycles/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PerformanceController::showCycle
* @see app/Http/Controllers/Api/PerformanceController.php:52
* @route '/api/performance/cycles/{id}'
*/
showCycle.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return showCycle.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PerformanceController::showCycle
* @see app/Http/Controllers/Api/PerformanceController.php:52
* @route '/api/performance/cycles/{id}'
*/
showCycle.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showCycle.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::showCycle
* @see app/Http/Controllers/Api/PerformanceController.php:52
* @route '/api/performance/cycles/{id}'
*/
showCycle.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showCycle.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::showCycle
* @see app/Http/Controllers/Api/PerformanceController.php:52
* @route '/api/performance/cycles/{id}'
*/
const showCycleForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showCycle.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::showCycle
* @see app/Http/Controllers/Api/PerformanceController.php:52
* @route '/api/performance/cycles/{id}'
*/
showCycleForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showCycle.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::showCycle
* @see app/Http/Controllers/Api/PerformanceController.php:52
* @route '/api/performance/cycles/{id}'
*/
showCycleForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showCycle.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

showCycle.form = showCycleForm

/**
* @see \App\Http\Controllers\Api\PerformanceController::activateCycle
* @see app/Http/Controllers/Api/PerformanceController.php:63
* @route '/api/performance/cycles/{id}/activate'
*/
export const activateCycle = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: activateCycle.url(args, options),
    method: 'post',
})

activateCycle.definition = {
    methods: ["post"],
    url: '/api/performance/cycles/{id}/activate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\PerformanceController::activateCycle
* @see app/Http/Controllers/Api/PerformanceController.php:63
* @route '/api/performance/cycles/{id}/activate'
*/
activateCycle.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return activateCycle.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PerformanceController::activateCycle
* @see app/Http/Controllers/Api/PerformanceController.php:63
* @route '/api/performance/cycles/{id}/activate'
*/
activateCycle.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: activateCycle.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::activateCycle
* @see app/Http/Controllers/Api/PerformanceController.php:63
* @route '/api/performance/cycles/{id}/activate'
*/
const activateCycleForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: activateCycle.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::activateCycle
* @see app/Http/Controllers/Api/PerformanceController.php:63
* @route '/api/performance/cycles/{id}/activate'
*/
activateCycleForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: activateCycle.url(args, options),
    method: 'post',
})

activateCycle.form = activateCycleForm

/**
* @see \App\Http\Controllers\Api\PerformanceController::closeCycle
* @see app/Http/Controllers/Api/PerformanceController.php:73
* @route '/api/performance/cycles/{id}/close'
*/
export const closeCycle = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: closeCycle.url(args, options),
    method: 'post',
})

closeCycle.definition = {
    methods: ["post"],
    url: '/api/performance/cycles/{id}/close',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\PerformanceController::closeCycle
* @see app/Http/Controllers/Api/PerformanceController.php:73
* @route '/api/performance/cycles/{id}/close'
*/
closeCycle.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return closeCycle.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PerformanceController::closeCycle
* @see app/Http/Controllers/Api/PerformanceController.php:73
* @route '/api/performance/cycles/{id}/close'
*/
closeCycle.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: closeCycle.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::closeCycle
* @see app/Http/Controllers/Api/PerformanceController.php:73
* @route '/api/performance/cycles/{id}/close'
*/
const closeCycleForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: closeCycle.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::closeCycle
* @see app/Http/Controllers/Api/PerformanceController.php:73
* @route '/api/performance/cycles/{id}/close'
*/
closeCycleForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: closeCycle.url(args, options),
    method: 'post',
})

closeCycle.form = closeCycleForm

/**
* @see \App\Http\Controllers\Api\PerformanceController::indexReviews
* @see app/Http/Controllers/Api/PerformanceController.php:85
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
export const indexReviews = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexReviews.url(args, options),
    method: 'get',
})

indexReviews.definition = {
    methods: ["get","head"],
    url: '/api/performance/cycles/{cycleId}/reviews',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PerformanceController::indexReviews
* @see app/Http/Controllers/Api/PerformanceController.php:85
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
indexReviews.url = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return indexReviews.definition.url
            .replace('{cycleId}', parsedArgs.cycleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PerformanceController::indexReviews
* @see app/Http/Controllers/Api/PerformanceController.php:85
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
indexReviews.get = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexReviews.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::indexReviews
* @see app/Http/Controllers/Api/PerformanceController.php:85
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
indexReviews.head = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: indexReviews.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::indexReviews
* @see app/Http/Controllers/Api/PerformanceController.php:85
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
const indexReviewsForm = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexReviews.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::indexReviews
* @see app/Http/Controllers/Api/PerformanceController.php:85
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
indexReviewsForm.get = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexReviews.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::indexReviews
* @see app/Http/Controllers/Api/PerformanceController.php:85
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
indexReviewsForm.head = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexReviews.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

indexReviews.form = indexReviewsForm

/**
* @see \App\Http\Controllers\Api\PerformanceController::storeReview
* @see app/Http/Controllers/Api/PerformanceController.php:99
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
export const storeReview = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeReview.url(args, options),
    method: 'post',
})

storeReview.definition = {
    methods: ["post"],
    url: '/api/performance/cycles/{cycleId}/reviews',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\PerformanceController::storeReview
* @see app/Http/Controllers/Api/PerformanceController.php:99
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
storeReview.url = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return storeReview.definition.url
            .replace('{cycleId}', parsedArgs.cycleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PerformanceController::storeReview
* @see app/Http/Controllers/Api/PerformanceController.php:99
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
storeReview.post = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeReview.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::storeReview
* @see app/Http/Controllers/Api/PerformanceController.php:99
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
const storeReviewForm = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeReview.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::storeReview
* @see app/Http/Controllers/Api/PerformanceController.php:99
* @route '/api/performance/cycles/{cycleId}/reviews'
*/
storeReviewForm.post = (args: { cycleId: string | number } | [cycleId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeReview.url(args, options),
    method: 'post',
})

storeReview.form = storeReviewForm

/**
* @see \App\Http\Controllers\Api\PerformanceController::updateReview
* @see app/Http/Controllers/Api/PerformanceController.php:125
* @route '/api/performance/cycles/{cycleId}/reviews/{reviewId}'
*/
export const updateReview = (args: { cycleId: string | number, reviewId: string | number } | [cycleId: string | number, reviewId: string | number ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateReview.url(args, options),
    method: 'patch',
})

updateReview.definition = {
    methods: ["patch"],
    url: '/api/performance/cycles/{cycleId}/reviews/{reviewId}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\PerformanceController::updateReview
* @see app/Http/Controllers/Api/PerformanceController.php:125
* @route '/api/performance/cycles/{cycleId}/reviews/{reviewId}'
*/
updateReview.url = (args: { cycleId: string | number, reviewId: string | number } | [cycleId: string | number, reviewId: string | number ], options?: RouteQueryOptions) => {
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

    return updateReview.definition.url
            .replace('{cycleId}', parsedArgs.cycleId.toString())
            .replace('{reviewId}', parsedArgs.reviewId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PerformanceController::updateReview
* @see app/Http/Controllers/Api/PerformanceController.php:125
* @route '/api/performance/cycles/{cycleId}/reviews/{reviewId}'
*/
updateReview.patch = (args: { cycleId: string | number, reviewId: string | number } | [cycleId: string | number, reviewId: string | number ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateReview.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::updateReview
* @see app/Http/Controllers/Api/PerformanceController.php:125
* @route '/api/performance/cycles/{cycleId}/reviews/{reviewId}'
*/
const updateReviewForm = (args: { cycleId: string | number, reviewId: string | number } | [cycleId: string | number, reviewId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateReview.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::updateReview
* @see app/Http/Controllers/Api/PerformanceController.php:125
* @route '/api/performance/cycles/{cycleId}/reviews/{reviewId}'
*/
updateReviewForm.patch = (args: { cycleId: string | number, reviewId: string | number } | [cycleId: string | number, reviewId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateReview.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateReview.form = updateReviewForm

/**
* @see \App\Http\Controllers\Api\PerformanceController::calibrateCycle
* @see app/Http/Controllers/Api/PerformanceController.php:149
* @route '/api/performance/cycles/{id}/calibrate'
*/
export const calibrateCycle = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: calibrateCycle.url(args, options),
    method: 'post',
})

calibrateCycle.definition = {
    methods: ["post"],
    url: '/api/performance/cycles/{id}/calibrate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\PerformanceController::calibrateCycle
* @see app/Http/Controllers/Api/PerformanceController.php:149
* @route '/api/performance/cycles/{id}/calibrate'
*/
calibrateCycle.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return calibrateCycle.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PerformanceController::calibrateCycle
* @see app/Http/Controllers/Api/PerformanceController.php:149
* @route '/api/performance/cycles/{id}/calibrate'
*/
calibrateCycle.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: calibrateCycle.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::calibrateCycle
* @see app/Http/Controllers/Api/PerformanceController.php:149
* @route '/api/performance/cycles/{id}/calibrate'
*/
const calibrateCycleForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: calibrateCycle.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PerformanceController::calibrateCycle
* @see app/Http/Controllers/Api/PerformanceController.php:149
* @route '/api/performance/cycles/{id}/calibrate'
*/
calibrateCycleForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: calibrateCycle.url(args, options),
    method: 'post',
})

calibrateCycle.form = calibrateCycleForm

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

const PerformanceController = { indexCycles, storeCycle, showCycle, activateCycle, closeCycle, indexReviews, storeReview, updateReview, calibrateCycle, insights }

export default PerformanceController