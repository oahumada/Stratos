import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\UgcController::index
* @see app/Http/Controllers/Api/Lms/UgcController.php:19
* @route '/api/lms/ugc'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/lms/ugc',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::index
* @see app/Http/Controllers/Api/Lms/UgcController.php:19
* @route '/api/lms/ugc'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::index
* @see app/Http/Controllers/Api/Lms/UgcController.php:19
* @route '/api/lms/ugc'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::index
* @see app/Http/Controllers/Api/Lms/UgcController.php:19
* @route '/api/lms/ugc'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::index
* @see app/Http/Controllers/Api/Lms/UgcController.php:19
* @route '/api/lms/ugc'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::index
* @see app/Http/Controllers/Api/Lms/UgcController.php:19
* @route '/api/lms/ugc'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::index
* @see app/Http/Controllers/Api/Lms/UgcController.php:19
* @route '/api/lms/ugc'
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
* @see \App\Http\Controllers\Api\Lms\UgcController::pendingReview
* @see app/Http/Controllers/Api/Lms/UgcController.php:35
* @route '/api/lms/ugc/pending'
*/
export const pendingReview = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pendingReview.url(options),
    method: 'get',
})

pendingReview.definition = {
    methods: ["get","head"],
    url: '/api/lms/ugc/pending',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::pendingReview
* @see app/Http/Controllers/Api/Lms/UgcController.php:35
* @route '/api/lms/ugc/pending'
*/
pendingReview.url = (options?: RouteQueryOptions) => {
    return pendingReview.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::pendingReview
* @see app/Http/Controllers/Api/Lms/UgcController.php:35
* @route '/api/lms/ugc/pending'
*/
pendingReview.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pendingReview.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::pendingReview
* @see app/Http/Controllers/Api/Lms/UgcController.php:35
* @route '/api/lms/ugc/pending'
*/
pendingReview.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: pendingReview.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::pendingReview
* @see app/Http/Controllers/Api/Lms/UgcController.php:35
* @route '/api/lms/ugc/pending'
*/
const pendingReviewForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: pendingReview.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::pendingReview
* @see app/Http/Controllers/Api/Lms/UgcController.php:35
* @route '/api/lms/ugc/pending'
*/
pendingReviewForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: pendingReview.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::pendingReview
* @see app/Http/Controllers/Api/Lms/UgcController.php:35
* @route '/api/lms/ugc/pending'
*/
pendingReviewForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: pendingReview.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

pendingReview.form = pendingReviewForm

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::store
* @see app/Http/Controllers/Api/Lms/UgcController.php:48
* @route '/api/lms/ugc'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/lms/ugc',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::store
* @see app/Http/Controllers/Api/Lms/UgcController.php:48
* @route '/api/lms/ugc'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::store
* @see app/Http/Controllers/Api/Lms/UgcController.php:48
* @route '/api/lms/ugc'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::store
* @see app/Http/Controllers/Api/Lms/UgcController.php:48
* @route '/api/lms/ugc'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::store
* @see app/Http/Controllers/Api/Lms/UgcController.php:48
* @route '/api/lms/ugc'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::update
* @see app/Http/Controllers/Api/Lms/UgcController.php:75
* @route '/api/lms/ugc/{userContent}'
*/
export const update = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/lms/ugc/{userContent}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::update
* @see app/Http/Controllers/Api/Lms/UgcController.php:75
* @route '/api/lms/ugc/{userContent}'
*/
update.url = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { userContent: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { userContent: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            userContent: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        userContent: typeof args.userContent === 'object'
        ? args.userContent.id
        : args.userContent,
    }

    return update.definition.url
            .replace('{userContent}', parsedArgs.userContent.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::update
* @see app/Http/Controllers/Api/Lms/UgcController.php:75
* @route '/api/lms/ugc/{userContent}'
*/
update.put = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::update
* @see app/Http/Controllers/Api/Lms/UgcController.php:75
* @route '/api/lms/ugc/{userContent}'
*/
const updateForm = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::update
* @see app/Http/Controllers/Api/Lms/UgcController.php:75
* @route '/api/lms/ugc/{userContent}'
*/
updateForm.put = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::submitForReview
* @see app/Http/Controllers/Api/Lms/UgcController.php:94
* @route '/api/lms/ugc/{userContent}/submit'
*/
export const submitForReview = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submitForReview.url(args, options),
    method: 'post',
})

submitForReview.definition = {
    methods: ["post"],
    url: '/api/lms/ugc/{userContent}/submit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::submitForReview
* @see app/Http/Controllers/Api/Lms/UgcController.php:94
* @route '/api/lms/ugc/{userContent}/submit'
*/
submitForReview.url = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { userContent: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { userContent: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            userContent: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        userContent: typeof args.userContent === 'object'
        ? args.userContent.id
        : args.userContent,
    }

    return submitForReview.definition.url
            .replace('{userContent}', parsedArgs.userContent.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::submitForReview
* @see app/Http/Controllers/Api/Lms/UgcController.php:94
* @route '/api/lms/ugc/{userContent}/submit'
*/
submitForReview.post = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submitForReview.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::submitForReview
* @see app/Http/Controllers/Api/Lms/UgcController.php:94
* @route '/api/lms/ugc/{userContent}/submit'
*/
const submitForReviewForm = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: submitForReview.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::submitForReview
* @see app/Http/Controllers/Api/Lms/UgcController.php:94
* @route '/api/lms/ugc/{userContent}/submit'
*/
submitForReviewForm.post = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: submitForReview.url(args, options),
    method: 'post',
})

submitForReview.form = submitForReviewForm

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::approve
* @see app/Http/Controllers/Api/Lms/UgcController.php:101
* @route '/api/lms/ugc/{userContent}/approve'
*/
export const approve = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

approve.definition = {
    methods: ["post"],
    url: '/api/lms/ugc/{userContent}/approve',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::approve
* @see app/Http/Controllers/Api/Lms/UgcController.php:101
* @route '/api/lms/ugc/{userContent}/approve'
*/
approve.url = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { userContent: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { userContent: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            userContent: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        userContent: typeof args.userContent === 'object'
        ? args.userContent.id
        : args.userContent,
    }

    return approve.definition.url
            .replace('{userContent}', parsedArgs.userContent.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::approve
* @see app/Http/Controllers/Api/Lms/UgcController.php:101
* @route '/api/lms/ugc/{userContent}/approve'
*/
approve.post = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::approve
* @see app/Http/Controllers/Api/Lms/UgcController.php:101
* @route '/api/lms/ugc/{userContent}/approve'
*/
const approveForm = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::approve
* @see app/Http/Controllers/Api/Lms/UgcController.php:101
* @route '/api/lms/ugc/{userContent}/approve'
*/
approveForm.post = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve.url(args, options),
    method: 'post',
})

approve.form = approveForm

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::reject
* @see app/Http/Controllers/Api/Lms/UgcController.php:108
* @route '/api/lms/ugc/{userContent}/reject'
*/
export const reject = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject.url(args, options),
    method: 'post',
})

reject.definition = {
    methods: ["post"],
    url: '/api/lms/ugc/{userContent}/reject',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::reject
* @see app/Http/Controllers/Api/Lms/UgcController.php:108
* @route '/api/lms/ugc/{userContent}/reject'
*/
reject.url = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { userContent: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { userContent: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            userContent: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        userContent: typeof args.userContent === 'object'
        ? args.userContent.id
        : args.userContent,
    }

    return reject.definition.url
            .replace('{userContent}', parsedArgs.userContent.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::reject
* @see app/Http/Controllers/Api/Lms/UgcController.php:108
* @route '/api/lms/ugc/{userContent}/reject'
*/
reject.post = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::reject
* @see app/Http/Controllers/Api/Lms/UgcController.php:108
* @route '/api/lms/ugc/{userContent}/reject'
*/
const rejectForm = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reject.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::reject
* @see app/Http/Controllers/Api/Lms/UgcController.php:108
* @route '/api/lms/ugc/{userContent}/reject'
*/
rejectForm.post = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reject.url(args, options),
    method: 'post',
})

reject.form = rejectForm

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::like
* @see app/Http/Controllers/Api/Lms/UgcController.php:123
* @route '/api/lms/ugc/{userContent}/like'
*/
export const like = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: like.url(args, options),
    method: 'post',
})

like.definition = {
    methods: ["post"],
    url: '/api/lms/ugc/{userContent}/like',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::like
* @see app/Http/Controllers/Api/Lms/UgcController.php:123
* @route '/api/lms/ugc/{userContent}/like'
*/
like.url = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { userContent: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { userContent: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            userContent: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        userContent: typeof args.userContent === 'object'
        ? args.userContent.id
        : args.userContent,
    }

    return like.definition.url
            .replace('{userContent}', parsedArgs.userContent.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::like
* @see app/Http/Controllers/Api/Lms/UgcController.php:123
* @route '/api/lms/ugc/{userContent}/like'
*/
like.post = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: like.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::like
* @see app/Http/Controllers/Api/Lms/UgcController.php:123
* @route '/api/lms/ugc/{userContent}/like'
*/
const likeForm = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: like.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\UgcController::like
* @see app/Http/Controllers/Api/Lms/UgcController.php:123
* @route '/api/lms/ugc/{userContent}/like'
*/
likeForm.post = (args: { userContent: number | { id: number } } | [userContent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: like.url(args, options),
    method: 'post',
})

like.form = likeForm

const UgcController = { index, pendingReview, store, update, submitForReview, approve, reject, like }

export default UgcController