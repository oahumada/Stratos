import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Api\AnalyticsController::anomalies
* @see app/Http/Controllers/Api/AnalyticsController.php:39
* @route '/api/analytics/anomalies'
*/
export const anomalies = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: anomalies.url(options),
    method: 'get',
})

anomalies.definition = {
    methods: ["get","head"],
    url: '/api/analytics/anomalies',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AnalyticsController::anomalies
* @see app/Http/Controllers/Api/AnalyticsController.php:39
* @route '/api/analytics/anomalies'
*/
anomalies.url = (options?: RouteQueryOptions) => {
    return anomalies.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AnalyticsController::anomalies
* @see app/Http/Controllers/Api/AnalyticsController.php:39
* @route '/api/analytics/anomalies'
*/
anomalies.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: anomalies.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::anomalies
* @see app/Http/Controllers/Api/AnalyticsController.php:39
* @route '/api/analytics/anomalies'
*/
anomalies.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: anomalies.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::forecastCompliance
* @see app/Http/Controllers/Api/AnalyticsController.php:59
* @route '/api/analytics/predictions/compliance'
*/
export const forecastCompliance = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: forecastCompliance.url(options),
    method: 'get',
})

forecastCompliance.definition = {
    methods: ["get","head"],
    url: '/api/analytics/predictions/compliance',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AnalyticsController::forecastCompliance
* @see app/Http/Controllers/Api/AnalyticsController.php:59
* @route '/api/analytics/predictions/compliance'
*/
forecastCompliance.url = (options?: RouteQueryOptions) => {
    return forecastCompliance.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AnalyticsController::forecastCompliance
* @see app/Http/Controllers/Api/AnalyticsController.php:59
* @route '/api/analytics/predictions/compliance'
*/
forecastCompliance.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: forecastCompliance.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::forecastCompliance
* @see app/Http/Controllers/Api/AnalyticsController.php:59
* @route '/api/analytics/predictions/compliance'
*/
forecastCompliance.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: forecastCompliance.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::deploymentWindow
* @see app/Http/Controllers/Api/AnalyticsController.php:72
* @route '/api/analytics/predictions/deployment-window'
*/
export const deploymentWindow = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: deploymentWindow.url(options),
    method: 'get',
})

deploymentWindow.definition = {
    methods: ["get","head"],
    url: '/api/analytics/predictions/deployment-window',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AnalyticsController::deploymentWindow
* @see app/Http/Controllers/Api/AnalyticsController.php:72
* @route '/api/analytics/predictions/deployment-window'
*/
deploymentWindow.url = (options?: RouteQueryOptions) => {
    return deploymentWindow.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AnalyticsController::deploymentWindow
* @see app/Http/Controllers/Api/AnalyticsController.php:72
* @route '/api/analytics/predictions/deployment-window'
*/
deploymentWindow.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: deploymentWindow.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::deploymentWindow
* @see app/Http/Controllers/Api/AnalyticsController.php:72
* @route '/api/analytics/predictions/deployment-window'
*/
deploymentWindow.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: deploymentWindow.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::resources
* @see app/Http/Controllers/Api/AnalyticsController.php:89
* @route '/api/analytics/predictions/resources'
*/
export const resources = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: resources.url(options),
    method: 'get',
})

resources.definition = {
    methods: ["get","head"],
    url: '/api/analytics/predictions/resources',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AnalyticsController::resources
* @see app/Http/Controllers/Api/AnalyticsController.php:89
* @route '/api/analytics/predictions/resources'
*/
resources.url = (options?: RouteQueryOptions) => {
    return resources.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AnalyticsController::resources
* @see app/Http/Controllers/Api/AnalyticsController.php:89
* @route '/api/analytics/predictions/resources'
*/
resources.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: resources.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::resources
* @see app/Http/Controllers/Api/AnalyticsController.php:89
* @route '/api/analytics/predictions/resources'
*/
resources.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: resources.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::transitionRisk
* @see app/Http/Controllers/Api/AnalyticsController.php:102
* @route '/api/analytics/predictions/transition-risk'
*/
export const transitionRisk = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: transitionRisk.url(options),
    method: 'post',
})

transitionRisk.definition = {
    methods: ["post"],
    url: '/api/analytics/predictions/transition-risk',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AnalyticsController::transitionRisk
* @see app/Http/Controllers/Api/AnalyticsController.php:102
* @route '/api/analytics/predictions/transition-risk'
*/
transitionRisk.url = (options?: RouteQueryOptions) => {
    return transitionRisk.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AnalyticsController::transitionRisk
* @see app/Http/Controllers/Api/AnalyticsController.php:102
* @route '/api/analytics/predictions/transition-risk'
*/
transitionRisk.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: transitionRisk.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::recommendations
* @see app/Http/Controllers/Api/AnalyticsController.php:116
* @route '/api/analytics/recommendations'
*/
export const recommendations = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recommendations.url(options),
    method: 'get',
})

recommendations.definition = {
    methods: ["get","head"],
    url: '/api/analytics/recommendations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AnalyticsController::recommendations
* @see app/Http/Controllers/Api/AnalyticsController.php:116
* @route '/api/analytics/recommendations'
*/
recommendations.url = (options?: RouteQueryOptions) => {
    return recommendations.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AnalyticsController::recommendations
* @see app/Http/Controllers/Api/AnalyticsController.php:116
* @route '/api/analytics/recommendations'
*/
recommendations.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recommendations.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::recommendations
* @see app/Http/Controllers/Api/AnalyticsController.php:116
* @route '/api/analytics/recommendations'
*/
recommendations.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: recommendations.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::metricsCurrent
* @see app/Http/Controllers/Api/AnalyticsController.php:143
* @route '/api/analytics/metrics/current'
*/
export const metricsCurrent = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: metricsCurrent.url(options),
    method: 'get',
})

metricsCurrent.definition = {
    methods: ["get","head"],
    url: '/api/analytics/metrics/current',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AnalyticsController::metricsCurrent
* @see app/Http/Controllers/Api/AnalyticsController.php:143
* @route '/api/analytics/metrics/current'
*/
metricsCurrent.url = (options?: RouteQueryOptions) => {
    return metricsCurrent.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AnalyticsController::metricsCurrent
* @see app/Http/Controllers/Api/AnalyticsController.php:143
* @route '/api/analytics/metrics/current'
*/
metricsCurrent.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: metricsCurrent.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::metricsCurrent
* @see app/Http/Controllers/Api/AnalyticsController.php:143
* @route '/api/analytics/metrics/current'
*/
metricsCurrent.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: metricsCurrent.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::metricsHistory
* @see app/Http/Controllers/Api/AnalyticsController.php:160
* @route '/api/analytics/metrics/history'
*/
export const metricsHistory = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: metricsHistory.url(options),
    method: 'get',
})

metricsHistory.definition = {
    methods: ["get","head"],
    url: '/api/analytics/metrics/history',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AnalyticsController::metricsHistory
* @see app/Http/Controllers/Api/AnalyticsController.php:160
* @route '/api/analytics/metrics/history'
*/
metricsHistory.url = (options?: RouteQueryOptions) => {
    return metricsHistory.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AnalyticsController::metricsHistory
* @see app/Http/Controllers/Api/AnalyticsController.php:160
* @route '/api/analytics/metrics/history'
*/
metricsHistory.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: metricsHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::metricsHistory
* @see app/Http/Controllers/Api/AnalyticsController.php:160
* @route '/api/analytics/metrics/history'
*/
metricsHistory.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: metricsHistory.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::metricsComparison
* @see app/Http/Controllers/Api/AnalyticsController.php:185
* @route '/api/analytics/metrics/comparison'
*/
export const metricsComparison = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: metricsComparison.url(options),
    method: 'get',
})

metricsComparison.definition = {
    methods: ["get","head"],
    url: '/api/analytics/metrics/comparison',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AnalyticsController::metricsComparison
* @see app/Http/Controllers/Api/AnalyticsController.php:185
* @route '/api/analytics/metrics/comparison'
*/
metricsComparison.url = (options?: RouteQueryOptions) => {
    return metricsComparison.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AnalyticsController::metricsComparison
* @see app/Http/Controllers/Api/AnalyticsController.php:185
* @route '/api/analytics/metrics/comparison'
*/
metricsComparison.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: metricsComparison.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::metricsComparison
* @see app/Http/Controllers/Api/AnalyticsController.php:185
* @route '/api/analytics/metrics/comparison'
*/
metricsComparison.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: metricsComparison.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::latencyPercentiles
* @see app/Http/Controllers/Api/AnalyticsController.php:203
* @route '/api/analytics/metrics/latency-percentiles'
*/
export const latencyPercentiles = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: latencyPercentiles.url(options),
    method: 'get',
})

latencyPercentiles.definition = {
    methods: ["get","head"],
    url: '/api/analytics/metrics/latency-percentiles',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AnalyticsController::latencyPercentiles
* @see app/Http/Controllers/Api/AnalyticsController.php:203
* @route '/api/analytics/metrics/latency-percentiles'
*/
latencyPercentiles.url = (options?: RouteQueryOptions) => {
    return latencyPercentiles.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AnalyticsController::latencyPercentiles
* @see app/Http/Controllers/Api/AnalyticsController.php:203
* @route '/api/analytics/metrics/latency-percentiles'
*/
latencyPercentiles.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: latencyPercentiles.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::latencyPercentiles
* @see app/Http/Controllers/Api/AnalyticsController.php:203
* @route '/api/analytics/metrics/latency-percentiles'
*/
latencyPercentiles.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: latencyPercentiles.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::dashboardSummary
* @see app/Http/Controllers/Api/AnalyticsController.php:221
* @route '/api/analytics/dashboard-summary'
*/
export const dashboardSummary = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboardSummary.url(options),
    method: 'get',
})

dashboardSummary.definition = {
    methods: ["get","head"],
    url: '/api/analytics/dashboard-summary',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AnalyticsController::dashboardSummary
* @see app/Http/Controllers/Api/AnalyticsController.php:221
* @route '/api/analytics/dashboard-summary'
*/
dashboardSummary.url = (options?: RouteQueryOptions) => {
    return dashboardSummary.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AnalyticsController::dashboardSummary
* @see app/Http/Controllers/Api/AnalyticsController.php:221
* @route '/api/analytics/dashboard-summary'
*/
dashboardSummary.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboardSummary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::dashboardSummary
* @see app/Http/Controllers/Api/AnalyticsController.php:221
* @route '/api/analytics/dashboard-summary'
*/
dashboardSummary.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboardSummary.url(options),
    method: 'head',
})

const analytics = {
    anomalies: Object.assign(anomalies, anomalies),
    forecastCompliance: Object.assign(forecastCompliance, forecastCompliance),
    deploymentWindow: Object.assign(deploymentWindow, deploymentWindow),
    resources: Object.assign(resources, resources),
    transitionRisk: Object.assign(transitionRisk, transitionRisk),
    recommendations: Object.assign(recommendations, recommendations),
    metricsCurrent: Object.assign(metricsCurrent, metricsCurrent),
    metricsHistory: Object.assign(metricsHistory, metricsHistory),
    metricsComparison: Object.assign(metricsComparison, metricsComparison),
    latencyPercentiles: Object.assign(latencyPercentiles, latencyPercentiles),
    dashboardSummary: Object.assign(dashboardSummary, dashboardSummary),
}

export default analytics