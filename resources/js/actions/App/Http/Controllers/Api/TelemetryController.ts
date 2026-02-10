import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\TelemetryController::store
* @see app/Http/Controllers/Api/TelemetryController.php:17
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
* @see app/Http/Controllers/Api/TelemetryController.php:17
* @route '/api/telemetry/event'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TelemetryController::store
* @see app/Http/Controllers/Api/TelemetryController.php:17
* @route '/api/telemetry/event'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\TelemetryController::store
* @see app/Http/Controllers/Api/TelemetryController.php:17
* @route '/api/telemetry/event'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\TelemetryController::store
* @see app/Http/Controllers/Api/TelemetryController.php:17
* @route '/api/telemetry/event'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const TelemetryController = { store }

export default TelemetryController