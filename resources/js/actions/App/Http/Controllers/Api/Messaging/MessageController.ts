import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Messaging\MessageController::index
* @see app/Http/Controllers/Api/Messaging/MessageController.php:22
* @route '/api/messaging/conversations/{conversation}/messages'
*/
export const index = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/messaging/conversations/{conversation}/messages',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Messaging\MessageController::index
* @see app/Http/Controllers/Api/Messaging/MessageController.php:22
* @route '/api/messaging/conversations/{conversation}/messages'
*/
index.url = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
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

    return index.definition.url
            .replace('{conversation}', parsedArgs.conversation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Messaging\MessageController::index
* @see app/Http/Controllers/Api/Messaging/MessageController.php:22
* @route '/api/messaging/conversations/{conversation}/messages'
*/
index.get = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Messaging\MessageController::index
* @see app/Http/Controllers/Api/Messaging/MessageController.php:22
* @route '/api/messaging/conversations/{conversation}/messages'
*/
index.head = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Messaging\MessageController::index
* @see app/Http/Controllers/Api/Messaging/MessageController.php:22
* @route '/api/messaging/conversations/{conversation}/messages'
*/
const indexForm = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Messaging\MessageController::index
* @see app/Http/Controllers/Api/Messaging/MessageController.php:22
* @route '/api/messaging/conversations/{conversation}/messages'
*/
indexForm.get = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Messaging\MessageController::index
* @see app/Http/Controllers/Api/Messaging/MessageController.php:22
* @route '/api/messaging/conversations/{conversation}/messages'
*/
indexForm.head = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Api\Messaging\MessageController::store
* @see app/Http/Controllers/Api/Messaging/MessageController.php:58
* @route '/api/messaging/conversations/{conversation}/messages'
*/
export const store = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/messaging/conversations/{conversation}/messages',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Messaging\MessageController::store
* @see app/Http/Controllers/Api/Messaging/MessageController.php:58
* @route '/api/messaging/conversations/{conversation}/messages'
*/
store.url = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
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

    return store.definition.url
            .replace('{conversation}', parsedArgs.conversation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Messaging\MessageController::store
* @see app/Http/Controllers/Api/Messaging/MessageController.php:58
* @route '/api/messaging/conversations/{conversation}/messages'
*/
store.post = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Messaging\MessageController::store
* @see app/Http/Controllers/Api/Messaging/MessageController.php:58
* @route '/api/messaging/conversations/{conversation}/messages'
*/
const storeForm = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Messaging\MessageController::store
* @see app/Http/Controllers/Api/Messaging/MessageController.php:58
* @route '/api/messaging/conversations/{conversation}/messages'
*/
storeForm.post = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\Messaging\MessageController::markRead
* @see app/Http/Controllers/Api/Messaging/MessageController.php:106
* @route '/api/messaging/messages/{message}/read'
*/
export const markRead = (args: { message: string | { id: string } } | [message: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markRead.url(args, options),
    method: 'post',
})

markRead.definition = {
    methods: ["post"],
    url: '/api/messaging/messages/{message}/read',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Messaging\MessageController::markRead
* @see app/Http/Controllers/Api/Messaging/MessageController.php:106
* @route '/api/messaging/messages/{message}/read'
*/
markRead.url = (args: { message: string | { id: string } } | [message: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { message: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { message: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            message: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        message: typeof args.message === 'object'
        ? args.message.id
        : args.message,
    }

    return markRead.definition.url
            .replace('{message}', parsedArgs.message.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Messaging\MessageController::markRead
* @see app/Http/Controllers/Api/Messaging/MessageController.php:106
* @route '/api/messaging/messages/{message}/read'
*/
markRead.post = (args: { message: string | { id: string } } | [message: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markRead.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Messaging\MessageController::markRead
* @see app/Http/Controllers/Api/Messaging/MessageController.php:106
* @route '/api/messaging/messages/{message}/read'
*/
const markReadForm = (args: { message: string | { id: string } } | [message: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: markRead.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Messaging\MessageController::markRead
* @see app/Http/Controllers/Api/Messaging/MessageController.php:106
* @route '/api/messaging/messages/{message}/read'
*/
markReadForm.post = (args: { message: string | { id: string } } | [message: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: markRead.url(args, options),
    method: 'post',
})

markRead.form = markReadForm

const MessageController = { index, store, markRead }

export default MessageController