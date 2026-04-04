import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see routes/web.php:422
* @route '/quality-hub'
*/
export const hub = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: hub.url(options),
    method: 'get',
})

hub.definition = {
    methods: ["get","head"],
    url: '/quality-hub',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:422
* @route '/quality-hub'
*/
hub.url = (options?: RouteQueryOptions) => {
    return hub.definition.url + queryParams(options)
}

/**
* @see routes/web.php:422
* @route '/quality-hub'
*/
hub.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: hub.url(options),
    method: 'get',
})

/**
* @see routes/web.php:422
* @route '/quality-hub'
*/
hub.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: hub.url(options),
    method: 'head',
})

/**
* @see routes/web.php:422
* @route '/quality-hub'
*/
const hubForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: hub.url(options),
    method: 'get',
})

/**
* @see routes/web.php:422
* @route '/quality-hub'
*/
hubForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: hub.url(options),
    method: 'get',
})

/**
* @see routes/web.php:422
* @route '/quality-hub'
*/
hubForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: hub.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

hub.form = hubForm

/**
* @see routes/web.php:426
* @route '/quality/ragas-metrics'
*/
export const ragasMetrics = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ragasMetrics.url(options),
    method: 'get',
})

ragasMetrics.definition = {
    methods: ["get","head"],
    url: '/quality/ragas-metrics',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:426
* @route '/quality/ragas-metrics'
*/
ragasMetrics.url = (options?: RouteQueryOptions) => {
    return ragasMetrics.definition.url + queryParams(options)
}

/**
* @see routes/web.php:426
* @route '/quality/ragas-metrics'
*/
ragasMetrics.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ragasMetrics.url(options),
    method: 'get',
})

/**
* @see routes/web.php:426
* @route '/quality/ragas-metrics'
*/
ragasMetrics.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ragasMetrics.url(options),
    method: 'head',
})

/**
* @see routes/web.php:426
* @route '/quality/ragas-metrics'
*/
const ragasMetricsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ragasMetrics.url(options),
    method: 'get',
})

/**
* @see routes/web.php:426
* @route '/quality/ragas-metrics'
*/
ragasMetricsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ragasMetrics.url(options),
    method: 'get',
})

/**
* @see routes/web.php:426
* @route '/quality/ragas-metrics'
*/
ragasMetricsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ragasMetrics.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ragasMetrics.form = ragasMetricsForm

/**
* @see routes/web.php:430
* @route '/quality/compliance-audit'
*/
export const complianceAudit = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: complianceAudit.url(options),
    method: 'get',
})

complianceAudit.definition = {
    methods: ["get","head"],
    url: '/quality/compliance-audit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:430
* @route '/quality/compliance-audit'
*/
complianceAudit.url = (options?: RouteQueryOptions) => {
    return complianceAudit.definition.url + queryParams(options)
}

/**
* @see routes/web.php:430
* @route '/quality/compliance-audit'
*/
complianceAudit.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: complianceAudit.url(options),
    method: 'get',
})

/**
* @see routes/web.php:430
* @route '/quality/compliance-audit'
*/
complianceAudit.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: complianceAudit.url(options),
    method: 'head',
})

/**
* @see routes/web.php:430
* @route '/quality/compliance-audit'
*/
const complianceAuditForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: complianceAudit.url(options),
    method: 'get',
})

/**
* @see routes/web.php:430
* @route '/quality/compliance-audit'
*/
complianceAuditForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: complianceAudit.url(options),
    method: 'get',
})

/**
* @see routes/web.php:430
* @route '/quality/compliance-audit'
*/
complianceAuditForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: complianceAudit.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

complianceAudit.form = complianceAuditForm

const quality = {
    hub: Object.assign(hub, hub),
    ragasMetrics: Object.assign(ragasMetrics, ragasMetrics),
    complianceAudit: Object.assign(complianceAudit, complianceAudit),
}

export default quality