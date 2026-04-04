import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::index
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:18
* @route '/api/lms/discussions'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/lms/discussions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::index
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:18
* @route '/api/lms/discussions'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::index
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:18
* @route '/api/lms/discussions'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::index
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:18
* @route '/api/lms/discussions'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::index
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:18
* @route '/api/lms/discussions'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::index
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:18
* @route '/api/lms/discussions'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::index
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:18
* @route '/api/lms/discussions'
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
* @see \App\Http\Controllers\Api\Lms\DiscussionController::store
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:34
* @route '/api/lms/discussions'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/lms/discussions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::store
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:34
* @route '/api/lms/discussions'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::store
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:34
* @route '/api/lms/discussions'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::store
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:34
* @route '/api/lms/discussions'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::store
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:34
* @route '/api/lms/discussions'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::reply
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:58
* @route '/api/lms/discussions/{id}/reply'
*/
export const reply = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reply.url(args, options),
    method: 'post',
})

reply.definition = {
    methods: ["post"],
    url: '/api/lms/discussions/{id}/reply',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::reply
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:58
* @route '/api/lms/discussions/{id}/reply'
*/
reply.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return reply.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::reply
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:58
* @route '/api/lms/discussions/{id}/reply'
*/
reply.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reply.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::reply
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:58
* @route '/api/lms/discussions/{id}/reply'
*/
const replyForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reply.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::reply
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:58
* @route '/api/lms/discussions/{id}/reply'
*/
replyForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reply.url(args, options),
    method: 'post',
})

reply.form = replyForm

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::like
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:80
* @route '/api/lms/discussions/{id}/like'
*/
export const like = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: like.url(args, options),
    method: 'post',
})

like.definition = {
    methods: ["post"],
    url: '/api/lms/discussions/{id}/like',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::like
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:80
* @route '/api/lms/discussions/{id}/like'
*/
like.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return like.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::like
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:80
* @route '/api/lms/discussions/{id}/like'
*/
like.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: like.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::like
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:80
* @route '/api/lms/discussions/{id}/like'
*/
const likeForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: like.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::like
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:80
* @route '/api/lms/discussions/{id}/like'
*/
likeForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: like.url(args, options),
    method: 'post',
})

like.form = likeForm

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::pin
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:87
* @route '/api/lms/discussions/{id}/pin'
*/
export const pin = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: pin.url(args, options),
    method: 'post',
})

pin.definition = {
    methods: ["post"],
    url: '/api/lms/discussions/{id}/pin',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::pin
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:87
* @route '/api/lms/discussions/{id}/pin'
*/
pin.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return pin.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::pin
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:87
* @route '/api/lms/discussions/{id}/pin'
*/
pin.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: pin.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::pin
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:87
* @route '/api/lms/discussions/{id}/pin'
*/
const pinForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: pin.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::pin
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:87
* @route '/api/lms/discussions/{id}/pin'
*/
pinForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: pin.url(args, options),
    method: 'post',
})

pin.form = pinForm

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::destroy
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:100
* @route '/api/lms/discussions/{id}'
*/
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/lms/discussions/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::destroy
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:100
* @route '/api/lms/discussions/{id}'
*/
destroy.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::destroy
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:100
* @route '/api/lms/discussions/{id}'
*/
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::destroy
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:100
* @route '/api/lms/discussions/{id}'
*/
const destroyForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\DiscussionController::destroy
* @see app/Http/Controllers/Api/Lms/DiscussionController.php:100
* @route '/api/lms/discussions/{id}'
*/
destroyForm.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const DiscussionController = { index, store, reply, like, pin, destroy }

export default DiscussionController