import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import aggregates570a0b from './aggregates'
/**
* @see \App\Http\Controllers\Api\IntelligenceAggregatesController::aggregates
* @see app/Http/Controllers/Api/IntelligenceAggregatesController.php:24
* @route '/api/intelligence/aggregates'
*/
export const aggregates = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: aggregates.url(options),
    method: 'get',
})

aggregates.definition = {
    methods: ["get","head"],
    url: '/api/intelligence/aggregates',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\IntelligenceAggregatesController::aggregates
* @see app/Http/Controllers/Api/IntelligenceAggregatesController.php:24
* @route '/api/intelligence/aggregates'
*/
aggregates.url = (options?: RouteQueryOptions) => {
    return aggregates.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\IntelligenceAggregatesController::aggregates
* @see app/Http/Controllers/Api/IntelligenceAggregatesController.php:24
* @route '/api/intelligence/aggregates'
*/
aggregates.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: aggregates.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\IntelligenceAggregatesController::aggregates
* @see app/Http/Controllers/Api/IntelligenceAggregatesController.php:24
* @route '/api/intelligence/aggregates'
*/
aggregates.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: aggregates.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\IntelligenceAggregatesController::aggregates
* @see app/Http/Controllers/Api/IntelligenceAggregatesController.php:24
* @route '/api/intelligence/aggregates'
*/
const aggregatesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: aggregates.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\IntelligenceAggregatesController::aggregates
* @see app/Http/Controllers/Api/IntelligenceAggregatesController.php:24
* @route '/api/intelligence/aggregates'
*/
aggregatesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: aggregates.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\IntelligenceAggregatesController::aggregates
* @see app/Http/Controllers/Api/IntelligenceAggregatesController.php:24
* @route '/api/intelligence/aggregates'
*/
aggregatesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: aggregates.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

aggregates.form = aggregatesForm

/**
* @see routes/web.php:367
* @route '/intelligence/monitoring-hub'
*/
export const monitoringHub = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: monitoringHub.url(options),
    method: 'get',
})

monitoringHub.definition = {
    methods: ["get","head"],
    url: '/intelligence/monitoring-hub',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:367
* @route '/intelligence/monitoring-hub'
*/
monitoringHub.url = (options?: RouteQueryOptions) => {
    return monitoringHub.definition.url + queryParams(options)
}

/**
* @see routes/web.php:367
* @route '/intelligence/monitoring-hub'
*/
monitoringHub.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: monitoringHub.url(options),
    method: 'get',
})

/**
* @see routes/web.php:367
* @route '/intelligence/monitoring-hub'
*/
monitoringHub.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: monitoringHub.url(options),
    method: 'head',
})

/**
* @see routes/web.php:367
* @route '/intelligence/monitoring-hub'
*/
const monitoringHubForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: monitoringHub.url(options),
    method: 'get',
})

/**
* @see routes/web.php:367
* @route '/intelligence/monitoring-hub'
*/
monitoringHubForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: monitoringHub.url(options),
    method: 'get',
})

/**
* @see routes/web.php:367
* @route '/intelligence/monitoring-hub'
*/
monitoringHubForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: monitoringHub.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

monitoringHub.form = monitoringHubForm

/**
* @see routes/web.php:371
* @route '/intelligence/quality-dashboard'
*/
export const qualityDashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: qualityDashboard.url(options),
    method: 'get',
})

qualityDashboard.definition = {
    methods: ["get","head"],
    url: '/intelligence/quality-dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:371
* @route '/intelligence/quality-dashboard'
*/
qualityDashboard.url = (options?: RouteQueryOptions) => {
    return qualityDashboard.definition.url + queryParams(options)
}

/**
* @see routes/web.php:371
* @route '/intelligence/quality-dashboard'
*/
qualityDashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: qualityDashboard.url(options),
    method: 'get',
})

/**
* @see routes/web.php:371
* @route '/intelligence/quality-dashboard'
*/
qualityDashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: qualityDashboard.url(options),
    method: 'head',
})

/**
* @see routes/web.php:371
* @route '/intelligence/quality-dashboard'
*/
const qualityDashboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: qualityDashboard.url(options),
    method: 'get',
})

/**
* @see routes/web.php:371
* @route '/intelligence/quality-dashboard'
*/
qualityDashboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: qualityDashboard.url(options),
    method: 'get',
})

/**
* @see routes/web.php:371
* @route '/intelligence/quality-dashboard'
*/
qualityDashboardForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: qualityDashboard.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

qualityDashboard.form = qualityDashboardForm

/**
* @see routes/web.php:375
* @route '/intelligence/agent-metrics'
*/
export const agentMetrics = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: agentMetrics.url(options),
    method: 'get',
})

agentMetrics.definition = {
    methods: ["get","head"],
    url: '/intelligence/agent-metrics',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:375
* @route '/intelligence/agent-metrics'
*/
agentMetrics.url = (options?: RouteQueryOptions) => {
    return agentMetrics.definition.url + queryParams(options)
}

/**
* @see routes/web.php:375
* @route '/intelligence/agent-metrics'
*/
agentMetrics.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: agentMetrics.url(options),
    method: 'get',
})

/**
* @see routes/web.php:375
* @route '/intelligence/agent-metrics'
*/
agentMetrics.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: agentMetrics.url(options),
    method: 'head',
})

/**
* @see routes/web.php:375
* @route '/intelligence/agent-metrics'
*/
const agentMetricsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: agentMetrics.url(options),
    method: 'get',
})

/**
* @see routes/web.php:375
* @route '/intelligence/agent-metrics'
*/
agentMetricsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: agentMetrics.url(options),
    method: 'get',
})

/**
* @see routes/web.php:375
* @route '/intelligence/agent-metrics'
*/
agentMetricsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: agentMetrics.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

agentMetrics.form = agentMetricsForm

/**
* @see routes/web.php:379
* @route '/intelligence/aggregates'
*/
export const metricsDashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: metricsDashboard.url(options),
    method: 'get',
})

metricsDashboard.definition = {
    methods: ["get","head"],
    url: '/intelligence/aggregates',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:379
* @route '/intelligence/aggregates'
*/
metricsDashboard.url = (options?: RouteQueryOptions) => {
    return metricsDashboard.definition.url + queryParams(options)
}

/**
* @see routes/web.php:379
* @route '/intelligence/aggregates'
*/
metricsDashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: metricsDashboard.url(options),
    method: 'get',
})

/**
* @see routes/web.php:379
* @route '/intelligence/aggregates'
*/
metricsDashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: metricsDashboard.url(options),
    method: 'head',
})

/**
* @see routes/web.php:379
* @route '/intelligence/aggregates'
*/
const metricsDashboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: metricsDashboard.url(options),
    method: 'get',
})

/**
* @see routes/web.php:379
* @route '/intelligence/aggregates'
*/
metricsDashboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: metricsDashboard.url(options),
    method: 'get',
})

/**
* @see routes/web.php:379
* @route '/intelligence/aggregates'
*/
metricsDashboardForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: metricsDashboard.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

metricsDashboard.form = metricsDashboardForm

const intelligence = {
    aggregates: Object.assign(aggregates, aggregates570a0b),
    monitoringHub: Object.assign(monitoringHub, monitoringHub),
    qualityDashboard: Object.assign(qualityDashboard, qualityDashboard),
    agentMetrics: Object.assign(agentMetrics, agentMetrics),
    metricsDashboard: Object.assign(metricsDashboard, metricsDashboard),
}

export default intelligence