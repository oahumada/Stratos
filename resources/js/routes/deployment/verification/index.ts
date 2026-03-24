import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
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
* @see \App\Http\Controllers\Deployment\VerificationHubController::schedulerStatus
* @see app/Http/Controllers/Deployment/VerificationHubController.php:24
* @route '/api/deployment/verification/scheduler-status'
*/
const schedulerStatusForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: schedulerStatus.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::schedulerStatus
* @see app/Http/Controllers/Deployment/VerificationHubController.php:24
* @route '/api/deployment/verification/scheduler-status'
*/
schedulerStatusForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: schedulerStatus.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::schedulerStatus
* @see app/Http/Controllers/Deployment/VerificationHubController.php:24
* @route '/api/deployment/verification/scheduler-status'
*/
schedulerStatusForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: schedulerStatus.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

schedulerStatus.form = schedulerStatusForm

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::transitions
* @see app/Http/Controllers/Deployment/VerificationHubController.php:56
* @route '/api/deployment/verification/transitions'
*/
export const transitions = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: transitions.url(options),
    method: 'get',
})

transitions.definition = {
    methods: ["get","head"],
    url: '/api/deployment/verification/transitions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::transitions
* @see app/Http/Controllers/Deployment/VerificationHubController.php:56
* @route '/api/deployment/verification/transitions'
*/
transitions.url = (options?: RouteQueryOptions) => {
    return transitions.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::transitions
* @see app/Http/Controllers/Deployment/VerificationHubController.php:56
* @route '/api/deployment/verification/transitions'
*/
transitions.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: transitions.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::transitions
* @see app/Http/Controllers/Deployment/VerificationHubController.php:56
* @route '/api/deployment/verification/transitions'
*/
transitions.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: transitions.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::transitions
* @see app/Http/Controllers/Deployment/VerificationHubController.php:56
* @route '/api/deployment/verification/transitions'
*/
const transitionsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: transitions.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::transitions
* @see app/Http/Controllers/Deployment/VerificationHubController.php:56
* @route '/api/deployment/verification/transitions'
*/
transitionsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: transitions.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::transitions
* @see app/Http/Controllers/Deployment/VerificationHubController.php:56
* @route '/api/deployment/verification/transitions'
*/
transitionsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: transitions.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

transitions.form = transitionsForm

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
* @see \App\Http\Controllers\Deployment\VerificationHubController::notifications
* @see app/Http/Controllers/Deployment/VerificationHubController.php:90
* @route '/api/deployment/verification/notifications'
*/
const notificationsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: notifications.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::notifications
* @see app/Http/Controllers/Deployment/VerificationHubController.php:90
* @route '/api/deployment/verification/notifications'
*/
notificationsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: notifications.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::notifications
* @see app/Http/Controllers/Deployment/VerificationHubController.php:90
* @route '/api/deployment/verification/notifications'
*/
notificationsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: notifications.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

notifications.form = notificationsForm

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
* @see \App\Http\Controllers\Deployment\VerificationHubController::testNotification
* @see app/Http/Controllers/Deployment/VerificationHubController.php:132
* @route '/api/deployment/verification/test-notification'
*/
const testNotificationForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: testNotification.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::testNotification
* @see app/Http/Controllers/Deployment/VerificationHubController.php:132
* @route '/api/deployment/verification/test-notification'
*/
testNotificationForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: testNotification.url(options),
    method: 'post',
})

testNotification.form = testNotificationForm

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::configuration
* @see app/Http/Controllers/Deployment/VerificationHubController.php:187
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
* @see app/Http/Controllers/Deployment/VerificationHubController.php:187
* @route '/api/deployment/verification/configuration'
*/
configuration.url = (options?: RouteQueryOptions) => {
    return configuration.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::configuration
* @see app/Http/Controllers/Deployment/VerificationHubController.php:187
* @route '/api/deployment/verification/configuration'
*/
configuration.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: configuration.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::configuration
* @see app/Http/Controllers/Deployment/VerificationHubController.php:187
* @route '/api/deployment/verification/configuration'
*/
configuration.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: configuration.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::configuration
* @see app/Http/Controllers/Deployment/VerificationHubController.php:187
* @route '/api/deployment/verification/configuration'
*/
const configurationForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: configuration.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::configuration
* @see app/Http/Controllers/Deployment/VerificationHubController.php:187
* @route '/api/deployment/verification/configuration'
*/
configurationForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: configuration.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::configuration
* @see app/Http/Controllers/Deployment/VerificationHubController.php:187
* @route '/api/deployment/verification/configuration'
*/
configurationForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: configuration.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

configuration.form = configurationForm

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::auditLogs
* @see app/Http/Controllers/Deployment/VerificationHubController.php:227
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
* @see app/Http/Controllers/Deployment/VerificationHubController.php:227
* @route '/api/deployment/verification/audit-logs'
*/
auditLogs.url = (options?: RouteQueryOptions) => {
    return auditLogs.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::auditLogs
* @see app/Http/Controllers/Deployment/VerificationHubController.php:227
* @route '/api/deployment/verification/audit-logs'
*/
auditLogs.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: auditLogs.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::auditLogs
* @see app/Http/Controllers/Deployment/VerificationHubController.php:227
* @route '/api/deployment/verification/audit-logs'
*/
auditLogs.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: auditLogs.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::auditLogs
* @see app/Http/Controllers/Deployment/VerificationHubController.php:227
* @route '/api/deployment/verification/audit-logs'
*/
const auditLogsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: auditLogs.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::auditLogs
* @see app/Http/Controllers/Deployment/VerificationHubController.php:227
* @route '/api/deployment/verification/audit-logs'
*/
auditLogsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: auditLogs.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::auditLogs
* @see app/Http/Controllers/Deployment/VerificationHubController.php:227
* @route '/api/deployment/verification/audit-logs'
*/
auditLogsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: auditLogs.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

auditLogs.form = auditLogsForm

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::dryRun
* @see app/Http/Controllers/Deployment/VerificationHubController.php:267
* @route '/api/deployment/verification/dry-run'
*/
export const dryRun = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: dryRun.url(options),
    method: 'post',
})

dryRun.definition = {
    methods: ["post"],
    url: '/api/deployment/verification/dry-run',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::dryRun
* @see app/Http/Controllers/Deployment/VerificationHubController.php:267
* @route '/api/deployment/verification/dry-run'
*/
dryRun.url = (options?: RouteQueryOptions) => {
    return dryRun.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::dryRun
* @see app/Http/Controllers/Deployment/VerificationHubController.php:267
* @route '/api/deployment/verification/dry-run'
*/
dryRun.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: dryRun.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::dryRun
* @see app/Http/Controllers/Deployment/VerificationHubController.php:267
* @route '/api/deployment/verification/dry-run'
*/
const dryRunForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: dryRun.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::dryRun
* @see app/Http/Controllers/Deployment/VerificationHubController.php:267
* @route '/api/deployment/verification/dry-run'
*/
dryRunForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: dryRun.url(options),
    method: 'post',
})

dryRun.form = dryRunForm

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::complianceReport
* @see app/Http/Controllers/Deployment/VerificationHubController.php:358
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
* @see app/Http/Controllers/Deployment/VerificationHubController.php:358
* @route '/api/deployment/verification/compliance-report'
*/
complianceReport.url = (options?: RouteQueryOptions) => {
    return complianceReport.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::complianceReport
* @see app/Http/Controllers/Deployment/VerificationHubController.php:358
* @route '/api/deployment/verification/compliance-report'
*/
complianceReport.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: complianceReport.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::complianceReport
* @see app/Http/Controllers/Deployment/VerificationHubController.php:358
* @route '/api/deployment/verification/compliance-report'
*/
complianceReport.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: complianceReport.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::complianceReport
* @see app/Http/Controllers/Deployment/VerificationHubController.php:358
* @route '/api/deployment/verification/compliance-report'
*/
const complianceReportForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: complianceReport.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::complianceReport
* @see app/Http/Controllers/Deployment/VerificationHubController.php:358
* @route '/api/deployment/verification/compliance-report'
*/
complianceReportForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: complianceReport.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationHubController::complianceReport
* @see app/Http/Controllers/Deployment/VerificationHubController.php:358
* @route '/api/deployment/verification/compliance-report'
*/
complianceReportForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: complianceReport.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

complianceReport.form = complianceReportForm

const verification = {
    schedulerStatus: Object.assign(schedulerStatus, schedulerStatus),
    transitions: Object.assign(transitions, transitions),
    notifications: Object.assign(notifications, notifications),
    testNotification: Object.assign(testNotification, testNotification),
    configuration: Object.assign(configuration, configuration),
    auditLogs: Object.assign(auditLogs, auditLogs),
    dryRun: Object.assign(dryRun, dryRun),
    complianceReport: Object.assign(complianceReport, complianceReport),
}

export default verification