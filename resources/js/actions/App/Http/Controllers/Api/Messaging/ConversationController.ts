import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::index
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:23
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
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:23
* @route '/api/messaging/conversations'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::index
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:23
* @route '/api/messaging/conversations'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::index
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:23
* @route '/api/messaging/conversations'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::store
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:69
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
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:69
* @route '/api/messaging/conversations'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::store
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:69
* @route '/api/messaging/conversations'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::show
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:103
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
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:103
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
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:103
* @route '/api/messaging/conversations/{conversation}'
*/
show.get = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::show
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:103
* @route '/api/messaging/conversations/{conversation}'
*/
show.head = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::update
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:132
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
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:132
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
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:132
* @route '/api/messaging/conversations/{conversation}'
*/
update.put = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Api\Messaging\ConversationController::destroy
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:149
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
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:149
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
* @see app/Http/Controllers/Api/Messaging/ConversationController.php:149
* @route '/api/messaging/conversations/{conversation}'
*/
destroy.delete = (args: { conversation: string | { id: string } } | [conversation: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const ConversationController = { index, store, show, update, destroy }

export default ConversationController