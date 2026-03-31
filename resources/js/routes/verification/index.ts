import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationPromptController::notice
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationPromptController.php:18
* @route '/email/verify'
*/
export const notice = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: notice.url(options),
    method: 'get',
})

notice.definition = {
    methods: ["get","head"],
    url: '/email/verify',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationPromptController::notice
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationPromptController.php:18
* @route '/email/verify'
*/
notice.url = (options?: RouteQueryOptions) => {
    return notice.definition.url + queryParams(options)
}

/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationPromptController::notice
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationPromptController.php:18
* @route '/email/verify'
*/
notice.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: notice.url(options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationPromptController::notice
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationPromptController.php:18
* @route '/email/verify'
*/
notice.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: notice.url(options),
    method: 'head',
})

/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationPromptController::notice
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationPromptController.php:18
* @route '/email/verify'
*/
const noticeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: notice.url(options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationPromptController::notice
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationPromptController.php:18
* @route '/email/verify'
*/
noticeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: notice.url(options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationPromptController::notice
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationPromptController.php:18
* @route '/email/verify'
*/
noticeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: notice.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

notice.form = noticeForm

/**
* @see \Laravel\Fortify\Http\Controllers\VerifyEmailController::verify
* @see vendor/laravel/fortify/src/Http/Controllers/VerifyEmailController.php:18
* @route '/email/verify/{id}/{hash}'
*/
export const verify = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: verify.url(args, options),
    method: 'get',
})

verify.definition = {
    methods: ["get","head"],
    url: '/email/verify/{id}/{hash}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Fortify\Http\Controllers\VerifyEmailController::verify
* @see vendor/laravel/fortify/src/Http/Controllers/VerifyEmailController.php:18
* @route '/email/verify/{id}/{hash}'
*/
verify.url = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            id: args[0],
            hash: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
        hash: args.hash,
    }

    return verify.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace('{hash}', parsedArgs.hash.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Laravel\Fortify\Http\Controllers\VerifyEmailController::verify
* @see vendor/laravel/fortify/src/Http/Controllers/VerifyEmailController.php:18
* @route '/email/verify/{id}/{hash}'
*/
verify.get = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: verify.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\VerifyEmailController::verify
* @see vendor/laravel/fortify/src/Http/Controllers/VerifyEmailController.php:18
* @route '/email/verify/{id}/{hash}'
*/
verify.head = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: verify.url(args, options),
    method: 'head',
})

/**
* @see \Laravel\Fortify\Http\Controllers\VerifyEmailController::verify
* @see vendor/laravel/fortify/src/Http/Controllers/VerifyEmailController.php:18
* @route '/email/verify/{id}/{hash}'
*/
const verifyForm = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\VerifyEmailController::verify
* @see vendor/laravel/fortify/src/Http/Controllers/VerifyEmailController.php:18
* @route '/email/verify/{id}/{hash}'
*/
verifyForm.get = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\VerifyEmailController::verify
* @see vendor/laravel/fortify/src/Http/Controllers/VerifyEmailController.php:18
* @route '/email/verify/{id}/{hash}'
*/
verifyForm.head = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

verify.form = verifyForm

/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController::send
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationNotificationController.php:19
* @route '/email/verification-notification'
*/
export const send = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send.url(options),
    method: 'post',
})

send.definition = {
    methods: ["post"],
    url: '/email/verification-notification',
} satisfies RouteDefinition<["post"]>

/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController::send
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationNotificationController.php:19
* @route '/email/verification-notification'
*/
send.url = (options?: RouteQueryOptions) => {
    return send.definition.url + queryParams(options)
}

/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController::send
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationNotificationController.php:19
* @route '/email/verification-notification'
*/
send.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send.url(options),
    method: 'post',
})

/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController::send
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationNotificationController.php:19
* @route '/email/verification-notification'
*/
const sendForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: send.url(options),
    method: 'post',
})

/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController::send
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationNotificationController.php:19
* @route '/email/verification-notification'
*/
sendForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: send.url(options),
    method: 'post',
})

send.form = sendForm

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
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::metrics
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:16
* @route '/api/deployment/verification/metrics'
*/
const metricsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: metrics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::metrics
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:16
* @route '/api/deployment/verification/metrics'
*/
metricsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: metrics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::metrics
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:16
* @route '/api/deployment/verification/metrics'
*/
metricsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: metrics.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

metrics.form = metricsForm

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
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::complianceMetrics
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:40
* @route '/api/deployment/verification/compliance-metrics'
*/
const complianceMetricsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: complianceMetrics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::complianceMetrics
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:40
* @route '/api/deployment/verification/compliance-metrics'
*/
complianceMetricsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: complianceMetrics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::complianceMetrics
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:40
* @route '/api/deployment/verification/compliance-metrics'
*/
complianceMetricsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: complianceMetrics.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

complianceMetrics.form = complianceMetricsForm

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
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::metricsHistory
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:71
* @route '/api/deployment/verification/metrics-history'
*/
const metricsHistoryForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: metricsHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::metricsHistory
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:71
* @route '/api/deployment/verification/metrics-history'
*/
metricsHistoryForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: metricsHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::metricsHistory
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:71
* @route '/api/deployment/verification/metrics-history'
*/
metricsHistoryForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: metricsHistory.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

metricsHistory.form = metricsHistoryForm

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
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::realtimeEvents
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:0
* @route '/api/deployment/verification/realtime-events'
*/
const realtimeEventsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: realtimeEvents.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::realtimeEvents
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:0
* @route '/api/deployment/verification/realtime-events'
*/
realtimeEventsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: realtimeEvents.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::realtimeEvents
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:0
* @route '/api/deployment/verification/realtime-events'
*/
realtimeEventsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: realtimeEvents.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

realtimeEvents.form = realtimeEventsForm

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
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::realtimeEventsStream
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:99
* @route '/api/deployment/verification/realtime-events-stream'
*/
const realtimeEventsStreamForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: realtimeEventsStream.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::realtimeEventsStream
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:99
* @route '/api/deployment/verification/realtime-events-stream'
*/
realtimeEventsStreamForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: realtimeEventsStream.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::realtimeEventsStream
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:99
* @route '/api/deployment/verification/realtime-events-stream'
*/
realtimeEventsStreamForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: realtimeEventsStream.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

realtimeEventsStream.form = realtimeEventsStreamForm

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

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::exportMetrics
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:160
* @route '/api/deployment/verification/export-metrics'
*/
const exportMetricsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMetrics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::exportMetrics
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:160
* @route '/api/deployment/verification/export-metrics'
*/
exportMetricsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMetrics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationDashboardController::exportMetrics
* @see app/Http/Controllers/Deployment/VerificationDashboardController.php:160
* @route '/api/deployment/verification/export-metrics'
*/
exportMetricsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMetrics.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

exportMetrics.form = exportMetricsForm

const verification = {
    notice: Object.assign(notice, notice),
    verify: Object.assign(verify, verify),
    send: Object.assign(send, send),
    metrics: Object.assign(metrics, metrics),
    complianceMetrics: Object.assign(complianceMetrics, complianceMetrics),
    metricsHistory: Object.assign(metricsHistory, metricsHistory),
    realtimeEvents: Object.assign(realtimeEvents, realtimeEvents),
    realtimeEventsStream: Object.assign(realtimeEventsStream, realtimeEventsStream),
    exportMetrics: Object.assign(exportMetrics, exportMetrics),
}

export default verification