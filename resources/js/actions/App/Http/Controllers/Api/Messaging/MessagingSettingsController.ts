import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::getSettings
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:17
* @route '/api/messaging/settings'
*/
export const getSettings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSettings.url(options),
    method: 'get',
})

getSettings.definition = {
    methods: ["get","head"],
    url: '/api/messaging/settings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::getSettings
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:17
* @route '/api/messaging/settings'
*/
getSettings.url = (options?: RouteQueryOptions) => {
    return getSettings.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::getSettings
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:17
* @route '/api/messaging/settings'
*/
getSettings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSettings.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::getSettings
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:17
* @route '/api/messaging/settings'
*/
getSettings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getSettings.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::updateSettings
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:40
* @route '/api/messaging/settings'
*/
export const updateSettings = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateSettings.url(options),
    method: 'put',
})

updateSettings.definition = {
    methods: ["put"],
    url: '/api/messaging/settings',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::updateSettings
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:40
* @route '/api/messaging/settings'
*/
updateSettings.url = (options?: RouteQueryOptions) => {
    return updateSettings.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::updateSettings
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:40
* @route '/api/messaging/settings'
*/
updateSettings.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateSettings.url(options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::getMetrics
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:66
* @route '/api/messaging/metrics'
*/
export const getMetrics = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMetrics.url(options),
    method: 'get',
})

getMetrics.definition = {
    methods: ["get","head"],
    url: '/api/messaging/metrics',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::getMetrics
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:66
* @route '/api/messaging/metrics'
*/
getMetrics.url = (options?: RouteQueryOptions) => {
    return getMetrics.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::getMetrics
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:66
* @route '/api/messaging/metrics'
*/
getMetrics.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMetrics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::getMetrics
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:66
* @route '/api/messaging/metrics'
*/
getMetrics.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getMetrics.url(options),
    method: 'head',
})

const MessagingSettingsController = { getSettings, updateSettings, getMetrics }

export default MessagingSettingsController