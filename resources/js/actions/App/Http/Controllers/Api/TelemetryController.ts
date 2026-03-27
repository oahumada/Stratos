import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\TelemetryController::store
* @see app/Http/Controllers/Api/TelemetryController.php:14
* @route '/api/telemetry/event'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/telemetry/event',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\TelemetryController::store
* @see app/Http/Controllers/Api/TelemetryController.php:14
* @route '/api/telemetry/event'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TelemetryController::store
* @see app/Http/Controllers/Api/TelemetryController.php:14
* @route '/api/telemetry/event'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

const TelemetryController = { store }

export default TelemetryController