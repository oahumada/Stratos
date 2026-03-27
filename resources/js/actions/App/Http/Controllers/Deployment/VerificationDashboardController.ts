import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::metrics
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:16
* @route '/api/deployment/verification/metrics'
*/
export const metrics = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: metrics.url(options),
    method: 'get',
})

metrics.definition = {
    methods: ["get","head"],
    url: '/api/deployment/verification/metrics',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::metrics
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:16
* @route '/api/deployment/verification/metrics'
*/
metrics.url = (options?: RouteQueryOptions) => {
    return metrics.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::metrics
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:16
* @route '/api/deployment/verification/metrics'
*/
metrics.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: metrics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::metrics
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:16
* @route '/api/deployment/verification/metrics'
*/
metrics.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: metrics.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::complianceMetrics
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:40
* @route '/api/deployment/verification/compliance-metrics'
*/
export const complianceMetrics = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: complianceMetrics.url(options),
    method: 'get',
})

complianceMetrics.definition = {
    methods: ["get","head"],
    url: '/api/deployment/verification/compliance-metrics',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::complianceMetrics
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:40
* @route '/api/deployment/verification/compliance-metrics'
*/
complianceMetrics.url = (options?: RouteQueryOptions) => {
    return complianceMetrics.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::complianceMetrics
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:40
* @route '/api/deployment/verification/compliance-metrics'
*/
complianceMetrics.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: complianceMetrics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::complianceMetrics
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:40
* @route '/api/deployment/verification/compliance-metrics'
*/
complianceMetrics.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: complianceMetrics.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::metricsHistory
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:71
* @route '/api/deployment/verification/metrics-history'
*/
export const metricsHistory = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: metricsHistory.url(options),
    method: 'get',
})

metricsHistory.definition = {
    methods: ["get","head"],
    url: '/api/deployment/verification/metrics-history',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::metricsHistory
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:71
* @route '/api/deployment/verification/metrics-history'
*/
metricsHistory.url = (options?: RouteQueryOptions) => {
    return metricsHistory.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::metricsHistory
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:71
* @route '/api/deployment/verification/metrics-history'
*/
metricsHistory.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: metricsHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::metricsHistory
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:71
* @route '/api/deployment/verification/metrics-history'
*/
metricsHistory.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: metricsHistory.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::realtimeEvents
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:0
* @route '/api/deployment/verification/realtime-events'
*/
export const realtimeEvents = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: realtimeEvents.url(options),
    method: 'get',
})

realtimeEvents.definition = {
    methods: ["get","head"],
    url: '/api/deployment/verification/realtime-events',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::realtimeEvents
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:0
* @route '/api/deployment/verification/realtime-events'
*/
realtimeEvents.url = (options?: RouteQueryOptions) => {
    return realtimeEvents.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::realtimeEvents
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:0
* @route '/api/deployment/verification/realtime-events'
*/
realtimeEvents.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: realtimeEvents.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::realtimeEvents
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:0
* @route '/api/deployment/verification/realtime-events'
*/
realtimeEvents.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: realtimeEvents.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::realtimeEventsStream
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:99
* @route '/api/deployment/verification/realtime-events-stream'
*/
export const realtimeEventsStream = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: realtimeEventsStream.url(options),
    method: 'get',
})

realtimeEventsStream.definition = {
    methods: ["get","head"],
    url: '/api/deployment/verification/realtime-events-stream',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::realtimeEventsStream
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:99
* @route '/api/deployment/verification/realtime-events-stream'
*/
realtimeEventsStream.url = (options?: RouteQueryOptions) => {
    return realtimeEventsStream.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::realtimeEventsStream
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:99
* @route '/api/deployment/verification/realtime-events-stream'
*/
realtimeEventsStream.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: realtimeEventsStream.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::realtimeEventsStream
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:99
* @route '/api/deployment/verification/realtime-events-stream'
*/
realtimeEventsStream.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: realtimeEventsStream.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::exportMetrics
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:160
* @route '/api/deployment/verification/export-metrics'
*/
export const exportMetrics = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMetrics.url(options),
    method: 'get',
})

exportMetrics.definition = {
    methods: ["get","head"],
    url: '/api/deployment/verification/export-metrics',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::exportMetrics
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:160
* @route '/api/deployment/verification/export-metrics'
*/
exportMetrics.url = (options?: RouteQueryOptions) => {
    return exportMetrics.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::exportMetrics
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:160
* @route '/api/deployment/verification/export-metrics'
*/
exportMetrics.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMetrics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::exportMetrics
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:160
* @route '/api/deployment/verification/export-metrics'
*/
exportMetrics.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMetrics.url(options),
    method: 'head',
})

const VerificationDashboardController = { metrics, complianceMetrics, metricsHistory, realtimeEvents, realtimeEventsStream, exportMetrics }

export default VerificationDashboardController