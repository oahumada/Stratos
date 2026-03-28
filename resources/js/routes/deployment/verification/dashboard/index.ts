import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/executive'
*/
export const executive = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: executive.url(options),
    method: 'get',
})

executive.definition = {
    methods: ["get","head"],
    url: '/deployment/verification/dashboard/executive',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/executive'
*/
executive.url = (options?: RouteQueryOptions) => {
    return executive.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/executive'
*/
executive.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: executive.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/executive'
*/
executive.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: executive.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/executive'
*/
const executiveForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: executive.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/executive'
*/
executiveForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: executive.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/executive'
*/
executiveForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: executive.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

executive.form = executiveForm

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/operational'
*/
export const operational = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: operational.url(options),
    method: 'get',
})

operational.definition = {
    methods: ["get","head"],
    url: '/deployment/verification/dashboard/operational',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/operational'
*/
operational.url = (options?: RouteQueryOptions) => {
    return operational.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/operational'
*/
operational.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: operational.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/operational'
*/
operational.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: operational.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/operational'
*/
const operationalForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: operational.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/operational'
*/
operationalForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: operational.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/operational'
*/
operationalForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: operational.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

operational.form = operationalForm

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/compliance'
*/
export const compliance = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: compliance.url(options),
    method: 'get',
})

compliance.definition = {
    methods: ["get","head"],
    url: '/deployment/verification/dashboard/compliance',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/compliance'
*/
compliance.url = (options?: RouteQueryOptions) => {
    return compliance.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/compliance'
*/
compliance.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: compliance.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/compliance'
*/
compliance.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: compliance.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/compliance'
*/
const complianceForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: compliance.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/compliance'
*/
complianceForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: compliance.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/compliance'
*/
complianceForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: compliance.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

compliance.form = complianceForm

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/performance'
*/
export const performance = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: performance.url(options),
    method: 'get',
})

performance.definition = {
    methods: ["get","head"],
    url: '/deployment/verification/dashboard/performance',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/performance'
*/
performance.url = (options?: RouteQueryOptions) => {
    return performance.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/performance'
*/
performance.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: performance.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/performance'
*/
performance.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: performance.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/performance'
*/
const performanceForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: performance.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/performance'
*/
performanceForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: performance.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/performance'
*/
performanceForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: performance.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

performance.form = performanceForm

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/insights'
*/
export const insights = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: insights.url(options),
    method: 'get',
})

insights.definition = {
    methods: ["get","head"],
    url: '/deployment/verification/dashboard/insights',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/insights'
*/
insights.url = (options?: RouteQueryOptions) => {
    return insights.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/insights'
*/
insights.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: insights.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/insights'
*/
insights.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: insights.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/insights'
*/
const insightsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: insights.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/insights'
*/
insightsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: insights.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/insights'
*/
insightsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: insights.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

insights.form = insightsForm

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/realtime'
*/
export const realtime = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: realtime.url(options),
    method: 'get',
})

realtime.definition = {
    methods: ["get","head"],
    url: '/deployment/verification/dashboard/realtime',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/realtime'
*/
realtime.url = (options?: RouteQueryOptions) => {
    return realtime.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/realtime'
*/
realtime.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: realtime.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/realtime'
*/
realtime.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: realtime.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/realtime'
*/
const realtimeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: realtime.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/realtime'
*/
realtimeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: realtime.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/realtime'
*/
realtimeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: realtime.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

realtime.form = realtimeForm

const dashboard = {
    executive: Object.assign(executive, executive),
    operational: Object.assign(operational, operational),
    compliance: Object.assign(compliance, compliance),
    performance: Object.assign(performance, performance),
    insights: Object.assign(insights, insights),
    realtime: Object.assign(realtime, realtime),
}

export default dashboard