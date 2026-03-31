import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::summary
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:66
* @route '/api/messaging/metrics'
*/
export const summary = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: summary.url(options),
    method: 'get',
})

summary.definition = {
    methods: ["get","head"],
    url: '/api/messaging/metrics',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::summary
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:66
* @route '/api/messaging/metrics'
*/
summary.url = (options?: RouteQueryOptions) => {
    return summary.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::summary
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:66
* @route '/api/messaging/metrics'
*/
summary.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: summary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::summary
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:66
* @route '/api/messaging/metrics'
*/
summary.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: summary.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::summary
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:66
* @route '/api/messaging/metrics'
*/
const summaryForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: summary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::summary
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:66
* @route '/api/messaging/metrics'
*/
summaryForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: summary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Messaging\MessagingSettingsController::summary
* @see app/Http/Controllers/Api/Messaging/MessagingSettingsController.php:66
* @route '/api/messaging/metrics'
*/
summaryForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: summary.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

summary.form = summaryForm

const metrics = {
    summary: Object.assign(summary, summary),
}

export default metrics