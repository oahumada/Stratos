import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Deployment\VerificationMetricsDashboardController::current
* @see app/Http/Controllers/Deployment/VerificationMetricsDashboardController.php:26
* @route '/api/deployment/verification-metrics'
*/
export const current = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: current.url(options),
    method: 'get',
})

current.definition = {
    methods: ["get","head"],
    url: '/api/deployment/verification-metrics',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Deployment\VerificationMetricsDashboardController::current
* @see app/Http/Controllers/Deployment/VerificationMetricsDashboardController.php:26
* @route '/api/deployment/verification-metrics'
*/
current.url = (options?: RouteQueryOptions) => {
    return current.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationMetricsDashboardController::current
* @see app/Http/Controllers/Deployment/VerificationMetricsDashboardController.php:26
* @route '/api/deployment/verification-metrics'
*/
current.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: current.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationMetricsDashboardController::current
* @see app/Http/Controllers/Deployment/VerificationMetricsDashboardController.php:26
* @route '/api/deployment/verification-metrics'
*/
current.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: current.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationMetricsDashboardController::byType
* @see app/Http/Controllers/Deployment/VerificationMetricsDashboardController.php:59
* @route '/api/deployment/verification-metrics/by-type'
*/
export const byType = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: byType.url(options),
    method: 'get',
})

byType.definition = {
    methods: ["get","head"],
    url: '/api/deployment/verification-metrics/by-type',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Deployment\VerificationMetricsDashboardController::byType
* @see app/Http/Controllers/Deployment/VerificationMetricsDashboardController.php:59
* @route '/api/deployment/verification-metrics/by-type'
*/
byType.url = (options?: RouteQueryOptions) => {
    return byType.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationMetricsDashboardController::byType
* @see app/Http/Controllers/Deployment/VerificationMetricsDashboardController.php:59
* @route '/api/deployment/verification-metrics/by-type'
*/
byType.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: byType.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationMetricsDashboardController::byType
* @see app/Http/Controllers/Deployment/VerificationMetricsDashboardController.php:59
* @route '/api/deployment/verification-metrics/by-type'
*/
byType.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: byType.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationMetricsDashboardController::exportMethod
* @see app/Http/Controllers/Deployment/VerificationMetricsDashboardController.php:85
* @route '/api/deployment/verification-metrics/export'
*/
export const exportMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/api/deployment/verification-metrics/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Deployment\VerificationMetricsDashboardController::exportMethod
* @see app/Http/Controllers/Deployment/VerificationMetricsDashboardController.php:85
* @route '/api/deployment/verification-metrics/export'
*/
exportMethod.url = (options?: RouteQueryOptions) => {
    return exportMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationMetricsDashboardController::exportMethod
* @see app/Http/Controllers/Deployment/VerificationMetricsDashboardController.php:85
* @route '/api/deployment/verification-metrics/export'
*/
exportMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationMetricsDashboardController::exportMethod
* @see app/Http/Controllers/Deployment/VerificationMetricsDashboardController.php:85
* @route '/api/deployment/verification-metrics/export'
*/
exportMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(options),
    method: 'head',
})

const verificationMetrics = {
    current: Object.assign(current, current),
    byType: Object.assign(byType, byType),
    export: Object.assign(exportMethod, exportMethod),
}

export default verificationMetrics