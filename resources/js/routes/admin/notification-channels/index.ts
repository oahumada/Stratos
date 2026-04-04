import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\NotificationChannelSettingsController::index
* @see app/Http/Controllers/Api/NotificationChannelSettingsController.php:15
* @route '/api/admin/notification-channel-settings'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/admin/notification-channel-settings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\NotificationChannelSettingsController::index
* @see app/Http/Controllers/Api/NotificationChannelSettingsController.php:15
* @route '/api/admin/notification-channel-settings'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\NotificationChannelSettingsController::index
* @see app/Http/Controllers/Api/NotificationChannelSettingsController.php:15
* @route '/api/admin/notification-channel-settings'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\NotificationChannelSettingsController::index
* @see app/Http/Controllers/Api/NotificationChannelSettingsController.php:15
* @route '/api/admin/notification-channel-settings'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\NotificationChannelSettingsController::index
* @see app/Http/Controllers/Api/NotificationChannelSettingsController.php:15
* @route '/api/admin/notification-channel-settings'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\NotificationChannelSettingsController::index
* @see app/Http/Controllers/Api/NotificationChannelSettingsController.php:15
* @route '/api/admin/notification-channel-settings'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\NotificationChannelSettingsController::index
* @see app/Http/Controllers/Api/NotificationChannelSettingsController.php:15
* @route '/api/admin/notification-channel-settings'
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
* @see \App\Http\Controllers\Api\NotificationChannelSettingsController::update
* @see app/Http/Controllers/Api/NotificationChannelSettingsController.php:30
* @route '/api/admin/notification-channel-settings/{channelType}'
*/
export const update = (args: { channelType: string | number } | [channelType: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/admin/notification-channel-settings/{channelType}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\NotificationChannelSettingsController::update
* @see app/Http/Controllers/Api/NotificationChannelSettingsController.php:30
* @route '/api/admin/notification-channel-settings/{channelType}'
*/
update.url = (args: { channelType: string | number } | [channelType: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{channelType}', parsedArgs.channelType.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\NotificationChannelSettingsController::update
* @see app/Http/Controllers/Api/NotificationChannelSettingsController.php:30
* @route '/api/admin/notification-channel-settings/{channelType}'
*/
update.put = (args: { channelType: string | number } | [channelType: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Api\NotificationChannelSettingsController::update
* @see app/Http/Controllers/Api/NotificationChannelSettingsController.php:30
* @route '/api/admin/notification-channel-settings/{channelType}'
*/
const updateForm = (args: { channelType: string | number } | [channelType: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\NotificationChannelSettingsController::update
* @see app/Http/Controllers/Api/NotificationChannelSettingsController.php:30
* @route '/api/admin/notification-channel-settings/{channelType}'
*/
updateForm.put = (args: { channelType: string | number } | [channelType: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Api\NotificationChannelSettingsController::destroy
* @see app/Http/Controllers/Api/NotificationChannelSettingsController.php:59
* @route '/api/admin/notification-channel-settings/{channelType}'
*/
export const destroy = (args: { channelType: string | number } | [channelType: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/admin/notification-channel-settings/{channelType}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\NotificationChannelSettingsController::destroy
* @see app/Http/Controllers/Api/NotificationChannelSettingsController.php:59
* @route '/api/admin/notification-channel-settings/{channelType}'
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
* @see \App\Http\Controllers\Api\NotificationChannelSettingsController::destroy
* @see app/Http/Controllers/Api/NotificationChannelSettingsController.php:59
* @route '/api/admin/notification-channel-settings/{channelType}'
*/
destroy.delete = (args: { channelType: string | number } | [channelType: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\NotificationChannelSettingsController::destroy
* @see app/Http/Controllers/Api/NotificationChannelSettingsController.php:59
* @route '/api/admin/notification-channel-settings/{channelType}'
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
* @see \App\Http\Controllers\Api\NotificationChannelSettingsController::destroy
* @see app/Http/Controllers/Api/NotificationChannelSettingsController.php:59
* @route '/api/admin/notification-channel-settings/{channelType}'
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

const notificationChannels = {
    index: Object.assign(index, index),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default notificationChannels