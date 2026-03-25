import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::index
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:24
* @route '/api/messaging/conversations'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/messaging/conversations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::index
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:24
* @route '/api/messaging/conversations'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::index
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:24
* @route '/api/messaging/conversations'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::index
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:24
* @route '/api/messaging/conversations'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::index
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:24
* @route '/api/messaging/conversations'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::index
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:24
* @route '/api/messaging/conversations'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::index
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:24
* @route '/api/messaging/conversations'
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
* @see \App\Http\Controllers\Api\Messaging\ConversationController::store
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:70
* @route '/api/messaging/conversations'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/messaging/conversations',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::store
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:70
* @route '/api/messaging/conversations'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::store
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:70
* @route '/api/messaging/conversations'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::store
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:70
* @route '/api/messaging/conversations'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::store
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:70
* @route '/api/messaging/conversations'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::show
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:104
* @route '/api/messaging/conversations/{conversation}'
*/
export const show = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/messaging/conversations/{conversation}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::show
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:104
* @route '/api/messaging/conversations/{conversation}'
*/
show.url = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { conversation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { conversation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            conversation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        conversation: typeof args.conversation === 'object'
        ? args.conversation.id
        : args.conversation,
    }

    return show.definition.url
            .replace('{conversation}', parsedArgs.conversation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::show
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:104
* @route '/api/messaging/conversations/{conversation}'
*/
show.get = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::show
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:104
* @route '/api/messaging/conversations/{conversation}'
*/
show.head = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::show
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:104
* @route '/api/messaging/conversations/{conversation}'
*/
const showForm = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::show
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:104
* @route '/api/messaging/conversations/{conversation}'
*/
showForm.get = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::show
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:104
* @route '/api/messaging/conversations/{conversation}'
*/
showForm.head = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Api\Messaging\ConversationController::update
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:136
* @route '/api/messaging/conversations/{conversation}'
*/
export const update = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/messaging/conversations/{conversation}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::update
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:136
* @route '/api/messaging/conversations/{conversation}'
*/
update.url = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { conversation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { conversation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            conversation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        conversation: typeof args.conversation === 'object'
        ? args.conversation.id
        : args.conversation,
    }

    return update.definition.url
            .replace('{conversation}', parsedArgs.conversation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::update
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:136
* @route '/api/messaging/conversations/{conversation}'
*/
update.put = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::update
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:136
* @route '/api/messaging/conversations/{conversation}'
*/
const updateForm = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::update
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:136
* @route '/api/messaging/conversations/{conversation}'
*/
updateForm.put = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Api\Messaging\ConversationController::destroy
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:153
* @route '/api/messaging/conversations/{conversation}'
*/
export const destroy = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/messaging/conversations/{conversation}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::destroy
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:153
* @route '/api/messaging/conversations/{conversation}'
*/
destroy.url = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { conversation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { conversation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            conversation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        conversation: typeof args.conversation === 'object'
        ? args.conversation.id
        : args.conversation,
    }

    return destroy.definition.url
            .replace('{conversation}', parsedArgs.conversation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::destroy
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:153
* @route '/api/messaging/conversations/{conversation}'
*/
destroy.delete = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::destroy
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:153
* @route '/api/messaging/conversations/{conversation}'
*/
const destroyForm = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::destroy
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:153
* @route '/api/messaging/conversations/{conversation}'
*/
destroyForm.delete = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const ConversationController = { index, store, show, update, destroy }

export default ConversationController