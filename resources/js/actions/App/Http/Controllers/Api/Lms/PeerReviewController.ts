import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::index
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:19
* @route '/api/lms/peer-reviews'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/lms/peer-reviews',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::index
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:19
* @route '/api/lms/peer-reviews'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::index
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:19
* @route '/api/lms/peer-reviews'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::index
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:19
* @route '/api/lms/peer-reviews'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::index
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:19
* @route '/api/lms/peer-reviews'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::index
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:19
* @route '/api/lms/peer-reviews'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::index
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:19
* @route '/api/lms/peer-reviews'
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
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::store
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:37
* @route '/api/lms/peer-reviews'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/lms/peer-reviews',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::store
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:37
* @route '/api/lms/peer-reviews'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::store
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:37
* @route '/api/lms/peer-reviews'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::store
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:37
* @route '/api/lms/peer-reviews'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::store
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:37
* @route '/api/lms/peer-reviews'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::submitWork
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:63
* @route '/api/lms/peer-reviews/{peerReview}/submit-work'
*/
export const submitWork = (args: { peerReview: number | { id: number } } | [peerReview: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submitWork.url(args, options),
    method: 'post',
})

submitWork.definition = {
    methods: ["post"],
    url: '/api/lms/peer-reviews/{peerReview}/submit-work',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::submitWork
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:63
* @route '/api/lms/peer-reviews/{peerReview}/submit-work'
*/
submitWork.url = (args: { peerReview: number | { id: number } } | [peerReview: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { peerReview: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { peerReview: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            peerReview: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        peerReview: typeof args.peerReview === 'object'
        ? args.peerReview.id
        : args.peerReview,
    }

    return submitWork.definition.url
            .replace('{peerReview}', parsedArgs.peerReview.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::submitWork
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:63
* @route '/api/lms/peer-reviews/{peerReview}/submit-work'
*/
submitWork.post = (args: { peerReview: number | { id: number } } | [peerReview: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submitWork.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::submitWork
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:63
* @route '/api/lms/peer-reviews/{peerReview}/submit-work'
*/
const submitWorkForm = (args: { peerReview: number | { id: number } } | [peerReview: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: submitWork.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::submitWork
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:63
* @route '/api/lms/peer-reviews/{peerReview}/submit-work'
*/
submitWorkForm.post = (args: { peerReview: number | { id: number } } | [peerReview: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: submitWork.url(args, options),
    method: 'post',
})

submitWork.form = submitWorkForm

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::submitReview
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:79
* @route '/api/lms/peer-reviews/{peerReview}/submit-review'
*/
export const submitReview = (args: { peerReview: number | { id: number } } | [peerReview: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submitReview.url(args, options),
    method: 'post',
})

submitReview.definition = {
    methods: ["post"],
    url: '/api/lms/peer-reviews/{peerReview}/submit-review',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::submitReview
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:79
* @route '/api/lms/peer-reviews/{peerReview}/submit-review'
*/
submitReview.url = (args: { peerReview: number | { id: number } } | [peerReview: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { peerReview: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { peerReview: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            peerReview: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        peerReview: typeof args.peerReview === 'object'
        ? args.peerReview.id
        : args.peerReview,
    }

    return submitReview.definition.url
            .replace('{peerReview}', parsedArgs.peerReview.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::submitReview
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:79
* @route '/api/lms/peer-reviews/{peerReview}/submit-review'
*/
submitReview.post = (args: { peerReview: number | { id: number } } | [peerReview: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submitReview.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::submitReview
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:79
* @route '/api/lms/peer-reviews/{peerReview}/submit-review'
*/
const submitReviewForm = (args: { peerReview: number | { id: number } } | [peerReview: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: submitReview.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::submitReview
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:79
* @route '/api/lms/peer-reviews/{peerReview}/submit-review'
*/
submitReviewForm.post = (args: { peerReview: number | { id: number } } | [peerReview: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: submitReview.url(args, options),
    method: 'post',
})

submitReview.form = submitReviewForm

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::scores
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:101
* @route '/api/lms/lessons/{lesson}/peer-scores'
*/
export const scores = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: scores.url(args, options),
    method: 'get',
})

scores.definition = {
    methods: ["get","head"],
    url: '/api/lms/lessons/{lesson}/peer-scores',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::scores
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:101
* @route '/api/lms/lessons/{lesson}/peer-scores'
*/
scores.url = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { lesson: args }
    }

    if (Array.isArray(args)) {
        args = {
            lesson: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        lesson: args.lesson,
    }

    return scores.definition.url
            .replace('{lesson}', parsedArgs.lesson.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::scores
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:101
* @route '/api/lms/lessons/{lesson}/peer-scores'
*/
scores.get = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: scores.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::scores
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:101
* @route '/api/lms/lessons/{lesson}/peer-scores'
*/
scores.head = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: scores.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::scores
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:101
* @route '/api/lms/lessons/{lesson}/peer-scores'
*/
const scoresForm = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: scores.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::scores
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:101
* @route '/api/lms/lessons/{lesson}/peer-scores'
*/
scoresForm.get = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: scores.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\PeerReviewController::scores
* @see app/Http/Controllers/Api/Lms/PeerReviewController.php:101
* @route '/api/lms/lessons/{lesson}/peer-scores'
*/
scoresForm.head = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: scores.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

scores.form = scoresForm

const PeerReviewController = { index, store, submitWork, submitReview, scores }

export default PeerReviewController