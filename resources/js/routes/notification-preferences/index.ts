import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Api\NotificationPreferencesController::index
* @see app/Http/Controllers/Api/NotificationPreferencesController.php:18
* @route '/api/notification-preferences'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/notification-preferences',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\NotificationPreferencesController::index
* @see app/Http/Controllers/Api/NotificationPreferencesController.php:18
* @route '/api/notification-preferences'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\NotificationPreferencesController::index
* @see app/Http/Controllers/Api/NotificationPreferencesController.php:18
* @route '/api/notification-preferences'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\NotificationPreferencesController::index
* @see app/Http/Controllers/Api/NotificationPreferencesController.php:18
* @route '/api/notification-preferences'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\NotificationPreferencesController::index
* @see app/Http/Controllers/Api/NotificationPreferencesController.php:18
* @route '/api/notification-preferences'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\NotificationPreferencesController::index
* @see app/Http/Controllers/Api/NotificationPreferencesController.php:18
* @route '/api/notification-preferences'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\NotificationPreferencesController::index
* @see app/Http/Controllers/Api/NotificationPreferencesController.php:18
* @route '/api/notification-preferences'
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
* @see \App\Http\Controllers\Api\NotificationPreferencesController::store
* @see app/Http/Controllers/Api/NotificationPreferencesController.php:34
* @route '/api/notification-preferences'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/notification-preferences',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\NotificationPreferencesController::store
* @see app/Http/Controllers/Api/NotificationPreferencesController.php:34
* @route '/api/notification-preferences'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\NotificationPreferencesController::store
* @see app/Http/Controllers/Api/NotificationPreferencesController.php:34
* @route '/api/notification-preferences'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\NotificationPreferencesController::store
* @see app/Http/Controllers/Api/NotificationPreferencesController.php:34
* @route '/api/notification-preferences'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\NotificationPreferencesController::store
* @see app/Http/Controllers/Api/NotificationPreferencesController.php:34
* @route '/api/notification-preferences'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\NotificationPreferencesController::toggle
* @see app/Http/Controllers/Api/NotificationPreferencesController.php:61
* @route '/api/notification-preferences/{channelType}/toggle'
*/
export const toggle = (args: { channelType: string | number } | [channelType: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})

toggle.definition = {
    methods: ["post"],
    url: '/api/notification-preferences/{channelType}/toggle',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\NotificationPreferencesController::toggle
* @see app/Http/Controllers/Api/NotificationPreferencesController.php:61
* @route '/api/notification-preferences/{channelType}/toggle'
*/
toggle.url = (args: { channelType: string | number } | [channelType: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { channelType: args }
    }

    if (Array.isArray(args)) {
        args = {
            channelType: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        channelType: args.channelType,
    }

    return toggle.definition.url
            .replace('{channelType}', parsedArgs.channelType.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\NotificationPreferencesController::toggle
* @see app/Http/Controllers/Api/NotificationPreferencesController.php:61
* @route '/api/notification-preferences/{channelType}/toggle'
*/
toggle.post = (args: { channelType: string | number } | [channelType: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\NotificationPreferencesController::toggle
* @see app/Http/Controllers/Api/NotificationPreferencesController.php:61
* @route '/api/notification-preferences/{channelType}/toggle'
*/
const toggleForm = (args: { channelType: string | number } | [channelType: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggle.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\NotificationPreferencesController::toggle
* @see app/Http/Controllers/Api/NotificationPreferencesController.php:61
* @route '/api/notification-preferences/{channelType}/toggle'
*/
toggleForm.post = (args: { channelType: string | number } | [channelType: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggle.url(args, options),
    method: 'post',
})

toggle.form = toggleForm

/**
* @see \App\Http\Controllers\Api\NotificationPreferencesController::destroy
* @see app/Http/Controllers/Api/NotificationPreferencesController.php:77
* @route '/api/notification-preferences/{channelType}'
*/
export const destroy = (args: { channelType: string | number } | [channelType: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/notification-preferences/{channelType}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\NotificationPreferencesController::destroy
* @see app/Http/Controllers/Api/NotificationPreferencesController.php:77
* @route '/api/notification-preferences/{channelType}'
*/
destroy.url = (args: { channelType: string | number } | [channelType: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { channelType: args }
    }

    if (Array.isArray(args)) {
        args = {
            channelType: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        channelType: args.channelType,
    }

    return destroy.definition.url
            .replace('{channelType}', parsedArgs.channelType.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\NotificationPreferencesController::destroy
* @see app/Http/Controllers/Api/NotificationPreferencesController.php:77
* @route '/api/notification-preferences/{channelType}'
*/
destroy.delete = (args: { channelType: string | number } | [channelType: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\NotificationPreferencesController::destroy
* @see app/Http/Controllers/Api/NotificationPreferencesController.php:77
* @route '/api/notification-preferences/{channelType}'
*/
const destroyForm = (args: { channelType: string | number } | [channelType: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\NotificationPreferencesController::destroy
* @see app/Http/Controllers/Api/NotificationPreferencesController.php:77
* @route '/api/notification-preferences/{channelType}'
*/
destroyForm.delete = (args: { channelType: string | number } | [channelType: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const notificationPreferences = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    toggle: Object.assign(toggle, toggle),
    destroy: Object.assign(destroy, destroy),
}

export default notificationPreferences