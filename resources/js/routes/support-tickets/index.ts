import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Api\SupportTicketController::index
* @see app/Http/Controllers/Api/SupportTicketController.php:18
* @route '/api/support-tickets'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/support-tickets',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\SupportTicketController::index
* @see app/Http/Controllers/Api/SupportTicketController.php:18
* @route '/api/support-tickets'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SupportTicketController::index
* @see app/Http/Controllers/Api/SupportTicketController.php:18
* @route '/api/support-tickets'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SupportTicketController::index
* @see app/Http/Controllers/Api/SupportTicketController.php:18
* @route '/api/support-tickets'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\SupportTicketController::store
* @see app/Http/Controllers/Api/SupportTicketController.php:43
* @route '/api/support-tickets'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/support-tickets',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\SupportTicketController::store
* @see app/Http/Controllers/Api/SupportTicketController.php:43
* @route '/api/support-tickets'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SupportTicketController::store
* @see app/Http/Controllers/Api/SupportTicketController.php:43
* @route '/api/support-tickets'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\SupportTicketController::show
* @see app/Http/Controllers/Api/SupportTicketController.php:79
* @route '/api/support-tickets/{support_ticket}'
*/
export const show = (args: { support_ticket: string | number } | [support_ticket: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/support-tickets/{support_ticket}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\SupportTicketController::show
* @see app/Http/Controllers/Api/SupportTicketController.php:79
* @route '/api/support-tickets/{support_ticket}'
*/
show.url = (args: { support_ticket: string | number } | [support_ticket: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { support_ticket: args }
    }

    if (Array.isArray(args)) {
        args = {
            support_ticket: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        support_ticket: args.support_ticket,
    }

    return show.definition.url
            .replace('{support_ticket}', parsedArgs.support_ticket.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SupportTicketController::show
* @see app/Http/Controllers/Api/SupportTicketController.php:79
* @route '/api/support-tickets/{support_ticket}'
*/
show.get = (args: { support_ticket: string | number } | [support_ticket: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SupportTicketController::show
* @see app/Http/Controllers/Api/SupportTicketController.php:79
* @route '/api/support-tickets/{support_ticket}'
*/
show.head = (args: { support_ticket: string | number } | [support_ticket: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\SupportTicketController::update
* @see app/Http/Controllers/Api/SupportTicketController.php:89
* @route '/api/support-tickets/{support_ticket}'
*/
export const update = (args: { support_ticket: string | number } | [support_ticket: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/api/support-tickets/{support_ticket}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Api\SupportTicketController::update
* @see app/Http/Controllers/Api/SupportTicketController.php:89
* @route '/api/support-tickets/{support_ticket}'
*/
update.url = (args: { support_ticket: string | number } | [support_ticket: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { support_ticket: args }
    }

    if (Array.isArray(args)) {
        args = {
            support_ticket: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        support_ticket: args.support_ticket,
    }

    return update.definition.url
            .replace('{support_ticket}', parsedArgs.support_ticket.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SupportTicketController::update
* @see app/Http/Controllers/Api/SupportTicketController.php:89
* @route '/api/support-tickets/{support_ticket}'
*/
update.put = (args: { support_ticket: string | number } | [support_ticket: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Api\SupportTicketController::update
* @see app/Http/Controllers/Api/SupportTicketController.php:89
* @route '/api/support-tickets/{support_ticket}'
*/
update.patch = (args: { support_ticket: string | number } | [support_ticket: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\SupportTicketController::destroy
* @see app/Http/Controllers/Api/SupportTicketController.php:110
* @route '/api/support-tickets/{support_ticket}'
*/
export const destroy = (args: { support_ticket: string | number } | [support_ticket: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/support-tickets/{support_ticket}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\SupportTicketController::destroy
* @see app/Http/Controllers/Api/SupportTicketController.php:110
* @route '/api/support-tickets/{support_ticket}'
*/
destroy.url = (args: { support_ticket: string | number } | [support_ticket: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { support_ticket: args }
    }

    if (Array.isArray(args)) {
        args = {
            support_ticket: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        support_ticket: args.support_ticket,
    }

    return destroy.definition.url
            .replace('{support_ticket}', parsedArgs.support_ticket.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SupportTicketController::destroy
* @see app/Http/Controllers/Api/SupportTicketController.php:110
* @route '/api/support-tickets/{support_ticket}'
*/
destroy.delete = (args: { support_ticket: string | number } | [support_ticket: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const supportTickets = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    show: Object.assign(show, show),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default supportTickets