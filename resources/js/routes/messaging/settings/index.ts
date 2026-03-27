import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::show
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:17
* @route '/api/messaging/settings'
*/
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/messaging/settings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::show
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:17
* @route '/api/messaging/settings'
*/
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::show
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:17
* @route '/api/messaging/settings'
*/
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::show
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:17
* @route '/api/messaging/settings'
*/
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::update
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:40
* @route '/api/messaging/settings'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/messaging/settings',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::update
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:40
* @route '/api/messaging/settings'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::update
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:40
* @route '/api/messaging/settings'
*/
update.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

const settings = {
    show: Object.assign(show, show),
    update: Object.assign(update, update),
}

export default settings