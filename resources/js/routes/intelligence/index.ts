import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see routes/web.php:250
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
* @see routes/web.php:250
* @route '/intelligence/monitoring-hub'
*/
monitoringHub.url = (options?: RouteQueryOptions) => {
    return monitoringHub.definition.url + queryParams(options)
}

/**
* @see routes/web.php:250
* @route '/intelligence/monitoring-hub'
*/
monitoringHub.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: monitoringHub.url(options),
    method: 'get',
})

/**
* @see routes/web.php:250
* @route '/intelligence/monitoring-hub'
*/
monitoringHub.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: monitoringHub.url(options),
    method: 'head',
})

/**
* @see routes/web.php:250
* @route '/intelligence/monitoring-hub'
*/
const monitoringHubForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: monitoringHub.url(options),
    method: 'get',
})

/**
* @see routes/web.php:250
* @route '/intelligence/monitoring-hub'
*/
monitoringHubForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: monitoringHub.url(options),
    method: 'get',
})

/**
* @see routes/web.php:250
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
* @see routes/web.php:254
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
* @see routes/web.php:254
* @route '/intelligence/quality-dashboard'
*/
qualityDashboard.url = (options?: RouteQueryOptions) => {
    return qualityDashboard.definition.url + queryParams(options)
}

/**
* @see routes/web.php:254
* @route '/intelligence/quality-dashboard'
*/
qualityDashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: qualityDashboard.url(options),
    method: 'get',
})

/**
* @see routes/web.php:254
* @route '/intelligence/quality-dashboard'
*/
qualityDashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: qualityDashboard.url(options),
    method: 'head',
})

/**
* @see routes/web.php:254
* @route '/intelligence/quality-dashboard'
*/
const qualityDashboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: qualityDashboard.url(options),
    method: 'get',
})

/**
* @see routes/web.php:254
* @route '/intelligence/quality-dashboard'
*/
qualityDashboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: qualityDashboard.url(options),
    method: 'get',
})

/**
* @see routes/web.php:254
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
* @see routes/web.php:258
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
* @see routes/web.php:258
* @route '/intelligence/agent-metrics'
*/
agentMetrics.url = (options?: RouteQueryOptions) => {
    return agentMetrics.definition.url + queryParams(options)
}

/**
* @see routes/web.php:258
* @route '/intelligence/agent-metrics'
*/
agentMetrics.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: agentMetrics.url(options),
    method: 'get',
})

/**
* @see routes/web.php:258
* @route '/intelligence/agent-metrics'
*/
agentMetrics.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: agentMetrics.url(options),
    method: 'head',
})

/**
* @see routes/web.php:258
* @route '/intelligence/agent-metrics'
*/
const agentMetricsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: agentMetrics.url(options),
    method: 'get',
})

/**
* @see routes/web.php:258
* @route '/intelligence/agent-metrics'
*/
agentMetricsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: agentMetrics.url(options),
    method: 'get',
})

/**
* @see routes/web.php:258
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

const intelligence = {
    monitoringHub: Object.assign(monitoringHub, monitoringHub),
    qualityDashboard: Object.assign(qualityDashboard, qualityDashboard),
    agentMetrics: Object.assign(agentMetrics, agentMetrics),
}

export default intelligence