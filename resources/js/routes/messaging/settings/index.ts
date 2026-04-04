import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
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
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::show
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:17
* @route '/api/messaging/settings'
*/
const showForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::show
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:17
* @route '/api/messaging/settings'
*/
showForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::show
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:17
* @route '/api/messaging/settings'
*/
showForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

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

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::update
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:40
* @route '/api/messaging/settings'
*/
const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::update
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:40
* @route '/api/messaging/settings'
*/
updateForm.put = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm
