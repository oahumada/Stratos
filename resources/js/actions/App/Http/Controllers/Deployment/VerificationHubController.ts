import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::schedulerStatus
* @see app/Http/Controllers/Deployment/VerificationHubController.php:24
* @route '/api/deployment/verification/scheduler-status'
*/
export const schedulerStatus = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: schedulerStatus.url(options),
    method: 'get',
})

schedulerStatus.definition = {
    methods: ["get","head"],
    url: '/api/deployment/verification/scheduler-status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::schedulerStatus
* @see app/Http/Controllers/Deployment/VerificationHubController.php:24
* @route '/api/deployment/verification/scheduler-status'
*/
schedulerStatus.url = (options?: RouteQueryOptions) => {
    return schedulerStatus.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::schedulerStatus
* @see app/Http/Controllers/Deployment/VerificationHubController.php:24
* @route '/api/deployment/verification/scheduler-status'
*/
schedulerStatus.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: schedulerStatus.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::schedulerStatus
* @see app/Http/Controllers/Deployment/VerificationHubController.php:24
* @route '/api/deployment/verification/scheduler-status'
*/
schedulerStatus.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: schedulerStatus.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::recentTransitions
* @see app/Http/Controllers/Deployment/VerificationHubController.php:56
* @route '/api/deployment/verification/transitions'
*/
export const recentTransitions = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recentTransitions.url(options),
    method: 'get',
})

recentTransitions.definition = {
    methods: ["get","head"],
    url: '/api/deployment/verification/transitions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::recentTransitions
* @see app/Http/Controllers/Deployment/VerificationHubController.php:56
* @route '/api/deployment/verification/transitions'
*/
recentTransitions.url = (options?: RouteQueryOptions) => {
    return recentTransitions.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::recentTransitions
* @see app/Http/Controllers/Deployment/VerificationHubController.php:56
* @route '/api/deployment/verification/transitions'
*/
recentTransitions.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recentTransitions.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::recentTransitions
* @see app/Http/Controllers/Deployment/VerificationHubController.php:56
* @route '/api/deployment/verification/transitions'
*/
recentTransitions.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: recentTransitions.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::notifications
* @see app/Http/Controllers/Deployment/VerificationHubController.php:90
* @route '/api/deployment/verification/notifications'
*/
export const notifications = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: notifications.url(options),
    method: 'get',
})

notifications.definition = {
    methods: ["get","head"],
    url: '/api/deployment/verification/notifications',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::notifications
* @see app/Http/Controllers/Deployment/VerificationHubController.php:90
* @route '/api/deployment/verification/notifications'
*/
notifications.url = (options?: RouteQueryOptions) => {
    return notifications.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::notifications
* @see app/Http/Controllers/Deployment/VerificationHubController.php:90
* @route '/api/deployment/verification/notifications'
*/
notifications.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: notifications.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::notifications
* @see app/Http/Controllers/Deployment/VerificationHubController.php:90
* @route '/api/deployment/verification/notifications'
*/
notifications.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: notifications.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::testNotification
* @see app/Http/Controllers/Deployment/VerificationHubController.php:132
* @route '/api/deployment/verification/test-notification'
*/
export const testNotification = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: testNotification.url(options),
    method: 'post',
})

testNotification.definition = {
    methods: ["post"],
    url: '/api/deployment/verification/test-notification',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::testNotification
* @see app/Http/Controllers/Deployment/VerificationHubController.php:132
* @route '/api/deployment/verification/test-notification'
*/
testNotification.url = (options?: RouteQueryOptions) => {
    return testNotification.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::testNotification
* @see app/Http/Controllers/Deployment/VerificationHubController.php:132
* @route '/api/deployment/verification/test-notification'
*/
testNotification.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: testNotification.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::configuration
* @see app/Http/Controllers/Deployment/VerificationHubController.php:164
* @route '/api/deployment/verification/configuration'
*/
export const configuration = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: configuration.url(options),
    method: 'get',
})

configuration.definition = {
    methods: ["get","head"],
    url: '/api/deployment/verification/configuration',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::configuration
* @see app/Http/Controllers/Deployment/VerificationHubController.php:164
* @route '/api/deployment/verification/configuration'
*/
configuration.url = (options?: RouteQueryOptions) => {
    return configuration.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::configuration
* @see app/Http/Controllers/Deployment/VerificationHubController.php:164
* @route '/api/deployment/verification/configuration'
*/
configuration.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: configuration.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::configuration
* @see app/Http/Controllers/Deployment/VerificationHubController.php:164
* @route '/api/deployment/verification/configuration'
*/
configuration.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: configuration.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::auditLogs
* @see app/Http/Controllers/Deployment/VerificationHubController.php:204
* @route '/api/deployment/verification/audit-logs'
*/
export const auditLogs = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: auditLogs.url(options),
    method: 'get',
})

auditLogs.definition = {
    methods: ["get","head"],
    url: '/api/deployment/verification/audit-logs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::auditLogs
* @see app/Http/Controllers/Deployment/VerificationHubController.php:204
* @route '/api/deployment/verification/audit-logs'
*/
auditLogs.url = (options?: RouteQueryOptions) => {
    return auditLogs.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::auditLogs
* @see app/Http/Controllers/Deployment/VerificationHubController.php:204
* @route '/api/deployment/verification/audit-logs'
*/
auditLogs.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: auditLogs.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::auditLogs
* @see app/Http/Controllers/Deployment/VerificationHubController.php:204
* @route '/api/deployment/verification/audit-logs'
*/
auditLogs.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: auditLogs.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::dryRunSimulation
* @see app/Http/Controllers/Deployment/VerificationHubController.php:244
* @route '/api/deployment/verification/dry-run'
*/
export const dryRunSimulation = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: dryRunSimulation.url(options),
    method: 'post',
})

dryRunSimulation.definition = {
    methods: ["post"],
    url: '/api/deployment/verification/dry-run',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::dryRunSimulation
* @see app/Http/Controllers/Deployment/VerificationHubController.php:244
* @route '/api/deployment/verification/dry-run'
*/
dryRunSimulation.url = (options?: RouteQueryOptions) => {
    return dryRunSimulation.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::dryRunSimulation
* @see app/Http/Controllers/Deployment/VerificationHubController.php:244
* @route '/api/deployment/verification/dry-run'
*/
dryRunSimulation.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: dryRunSimulation.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::complianceReport
* @see app/Http/Controllers/Deployment/VerificationHubController.php:335
* @route '/api/deployment/verification/compliance-report'
*/
export const complianceReport = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: complianceReport.url(options),
    method: 'get',
})

complianceReport.definition = {
    methods: ["get","head"],
    url: '/api/deployment/verification/compliance-report',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::complianceReport
* @see app/Http/Controllers/Deployment/VerificationHubController.php:335
* @route '/api/deployment/verification/compliance-report'
*/
complianceReport.url = (options?: RouteQueryOptions) => {
    return complianceReport.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::complianceReport
* @see app/Http/Controllers/Deployment/VerificationHubController.php:335
* @route '/api/deployment/verification/compliance-report'
*/
complianceReport.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: complianceReport.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::complianceReport
* @see app/Http/Controllers/Deployment/VerificationHubController.php:335
* @route '/api/deployment/verification/compliance-report'
*/
complianceReport.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: complianceReport.url(options),
    method: 'head',
})

const VerificationHubController = { schedulerStatus, recentTransitions, notifications, testNotification, configuration, auditLogs, dryRunSimulation, complianceReport }

export default VerificationHubController