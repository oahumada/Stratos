import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
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
* @see \App\Http\Controllers\Deployment\VerificationMetricsDashboardController::current
* @see app/Http/Controllers/Deployment/VerificationMetricsDashboardController.php:26
* @route '/api/deployment/verification-metrics'
*/
const currentForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: current.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationMetricsDashboardController::current
* @see app/Http/Controllers/Deployment/VerificationMetricsDashboardController.php:26
* @route '/api/deployment/verification-metrics'
*/
currentForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: current.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationMetricsDashboardController::current
* @see app/Http/Controllers/Deployment/VerificationMetricsDashboardController.php:26
* @route '/api/deployment/verification-metrics'
*/
currentForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: current.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

current.form = currentForm

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
* @see \App\Http\Controllers\Deployment\VerificationMetricsDashboardController::byType
* @see app/Http/Controllers/Deployment/VerificationMetricsDashboardController.php:59
* @route '/api/deployment/verification-metrics/by-type'
*/
const byTypeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: byType.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationMetricsDashboardController::byType
* @see app/Http/Controllers/Deployment/VerificationMetricsDashboardController.php:59
* @route '/api/deployment/verification-metrics/by-type'
*/
byTypeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: byType.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationMetricsDashboardController::byType
* @see app/Http/Controllers/Deployment/VerificationMetricsDashboardController.php:59
* @route '/api/deployment/verification-metrics/by-type'
*/
byTypeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: byType.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

byType.form = byTypeForm

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

/**
* @see \App\Http\Controllers\Deployment\VerificationMetricsDashboardController::exportMethod
* @see app/Http/Controllers/Deployment/VerificationMetricsDashboardController.php:85
* @route '/api/deployment/verification-metrics/export'
*/
const exportMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationMetricsDashboardController::exportMethod
* @see app/Http/Controllers/Deployment/VerificationMetricsDashboardController.php:85
* @route '/api/deployment/verification-metrics/export'
*/
exportMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationMetricsDashboardController::exportMethod
* @see app/Http/Controllers/Deployment/VerificationMetricsDashboardController.php:85
* @route '/api/deployment/verification-metrics/export'
*/
exportMethodForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

exportMethod.form = exportMethodForm

const VerificationMetricsDashboardController = { current, byType, exportMethod, export: exportMethod }

export default VerificationMetricsDashboardController