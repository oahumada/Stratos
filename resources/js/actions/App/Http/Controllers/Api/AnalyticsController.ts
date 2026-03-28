import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\AnalyticsController::getAnomalies
* @see app/Http/Controllers/Api/AnalyticsController.php:39
* @route '/api/analytics/anomalies'
*/
export const getAnomalies = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getAnomalies.url(options),
    method: 'get',
})

getAnomalies.definition = {
    methods: ["get","head"],
    url: '/api/analytics/anomalies',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getAnomalies
* @see app/Http/Controllers/Api/AnalyticsController.php:39
* @route '/api/analytics/anomalies'
*/
getAnomalies.url = (options?: RouteQueryOptions) => {
    return getAnomalies.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getAnomalies
* @see app/Http/Controllers/Api/AnalyticsController.php:39
* @route '/api/analytics/anomalies'
*/
getAnomalies.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getAnomalies.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getAnomalies
* @see app/Http/Controllers/Api/AnalyticsController.php:39
* @route '/api/analytics/anomalies'
*/
getAnomalies.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getAnomalies.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getAnomalies
* @see app/Http/Controllers/Api/AnalyticsController.php:39
* @route '/api/analytics/anomalies'
*/
const getAnomaliesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getAnomalies.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getAnomalies
* @see app/Http/Controllers/Api/AnalyticsController.php:39
* @route '/api/analytics/anomalies'
*/
getAnomaliesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getAnomalies.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getAnomalies
* @see app/Http/Controllers/Api/AnalyticsController.php:39
* @route '/api/analytics/anomalies'
*/
getAnomaliesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getAnomalies.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getAnomalies.form = getAnomaliesForm

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
* @see \App\Http\Controllers\Api\AnalyticsController::forecastCompliance
* @see app/Http/Controllers/Api/AnalyticsController.php:59
* @route '/api/analytics/predictions/compliance'
*/
const forecastComplianceForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: forecastCompliance.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::forecastCompliance
* @see app/Http/Controllers/Api/AnalyticsController.php:59
* @route '/api/analytics/predictions/compliance'
*/
forecastComplianceForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: forecastCompliance.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::forecastCompliance
* @see app/Http/Controllers/Api/AnalyticsController.php:59
* @route '/api/analytics/predictions/compliance'
*/
forecastComplianceForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: forecastCompliance.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

forecastCompliance.form = forecastComplianceForm

/**
* @see \App\Http\Controllers\Api\AnalyticsController::predictDeploymentWindow
* @see app/Http/Controllers/Api/AnalyticsController.php:72
* @route '/api/analytics/predictions/deployment-window'
*/
export const predictDeploymentWindow = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: predictDeploymentWindow.url(options),
    method: 'get',
})

predictDeploymentWindow.definition = {
    methods: ["get","head"],
    url: '/api/analytics/predictions/deployment-window',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AnalyticsController::predictDeploymentWindow
* @see app/Http/Controllers/Api/AnalyticsController.php:72
* @route '/api/analytics/predictions/deployment-window'
*/
predictDeploymentWindow.url = (options?: RouteQueryOptions) => {
    return predictDeploymentWindow.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AnalyticsController::predictDeploymentWindow
* @see app/Http/Controllers/Api/AnalyticsController.php:72
* @route '/api/analytics/predictions/deployment-window'
*/
predictDeploymentWindow.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: predictDeploymentWindow.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::predictDeploymentWindow
* @see app/Http/Controllers/Api/AnalyticsController.php:72
* @route '/api/analytics/predictions/deployment-window'
*/
predictDeploymentWindow.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: predictDeploymentWindow.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::predictDeploymentWindow
* @see app/Http/Controllers/Api/AnalyticsController.php:72
* @route '/api/analytics/predictions/deployment-window'
*/
const predictDeploymentWindowForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: predictDeploymentWindow.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::predictDeploymentWindow
* @see app/Http/Controllers/Api/AnalyticsController.php:72
* @route '/api/analytics/predictions/deployment-window'
*/
predictDeploymentWindowForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: predictDeploymentWindow.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::predictDeploymentWindow
* @see app/Http/Controllers/Api/AnalyticsController.php:72
* @route '/api/analytics/predictions/deployment-window'
*/
predictDeploymentWindowForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: predictDeploymentWindow.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

predictDeploymentWindow.form = predictDeploymentWindowForm

/**
* @see \App\Http\Controllers\Api\AnalyticsController::predictResourceNeeds
* @see app/Http/Controllers/Api/AnalyticsController.php:89
* @route '/api/analytics/predictions/resources'
*/
export const predictResourceNeeds = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: predictResourceNeeds.url(options),
    method: 'get',
})

predictResourceNeeds.definition = {
    methods: ["get","head"],
    url: '/api/analytics/predictions/resources',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AnalyticsController::predictResourceNeeds
* @see app/Http/Controllers/Api/AnalyticsController.php:89
* @route '/api/analytics/predictions/resources'
*/
predictResourceNeeds.url = (options?: RouteQueryOptions) => {
    return predictResourceNeeds.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AnalyticsController::predictResourceNeeds
* @see app/Http/Controllers/Api/AnalyticsController.php:89
* @route '/api/analytics/predictions/resources'
*/
predictResourceNeeds.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: predictResourceNeeds.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::predictResourceNeeds
* @see app/Http/Controllers/Api/AnalyticsController.php:89
* @route '/api/analytics/predictions/resources'
*/
predictResourceNeeds.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: predictResourceNeeds.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::predictResourceNeeds
* @see app/Http/Controllers/Api/AnalyticsController.php:89
* @route '/api/analytics/predictions/resources'
*/
const predictResourceNeedsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: predictResourceNeeds.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::predictResourceNeeds
* @see app/Http/Controllers/Api/AnalyticsController.php:89
* @route '/api/analytics/predictions/resources'
*/
predictResourceNeedsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: predictResourceNeeds.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::predictResourceNeeds
* @see app/Http/Controllers/Api/AnalyticsController.php:89
* @route '/api/analytics/predictions/resources'
*/
predictResourceNeedsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: predictResourceNeeds.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

predictResourceNeeds.form = predictResourceNeedsForm

/**
* @see \App\Http\Controllers\Api\AnalyticsController::assessTransitionRisk
* @see app/Http/Controllers/Api/AnalyticsController.php:102
* @route '/api/analytics/predictions/transition-risk'
*/
export const assessTransitionRisk = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: assessTransitionRisk.url(options),
    method: 'post',
})

assessTransitionRisk.definition = {
    methods: ["post"],
    url: '/api/analytics/predictions/transition-risk',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AnalyticsController::assessTransitionRisk
* @see app/Http/Controllers/Api/AnalyticsController.php:102
* @route '/api/analytics/predictions/transition-risk'
*/
assessTransitionRisk.url = (options?: RouteQueryOptions) => {
    return assessTransitionRisk.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AnalyticsController::assessTransitionRisk
* @see app/Http/Controllers/Api/AnalyticsController.php:102
* @route '/api/analytics/predictions/transition-risk'
*/
assessTransitionRisk.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: assessTransitionRisk.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::assessTransitionRisk
* @see app/Http/Controllers/Api/AnalyticsController.php:102
* @route '/api/analytics/predictions/transition-risk'
*/
const assessTransitionRiskForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: assessTransitionRisk.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::assessTransitionRisk
* @see app/Http/Controllers/Api/AnalyticsController.php:102
* @route '/api/analytics/predictions/transition-risk'
*/
assessTransitionRiskForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: assessTransitionRisk.url(options),
    method: 'post',
})

assessTransitionRisk.form = assessTransitionRiskForm

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getRecommendations
* @see app/Http/Controllers/Api/AnalyticsController.php:116
* @route '/api/analytics/recommendations'
*/
export const getRecommendations = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getRecommendations.url(options),
    method: 'get',
})

getRecommendations.definition = {
    methods: ["get","head"],
    url: '/api/analytics/recommendations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getRecommendations
* @see app/Http/Controllers/Api/AnalyticsController.php:116
* @route '/api/analytics/recommendations'
*/
getRecommendations.url = (options?: RouteQueryOptions) => {
    return getRecommendations.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getRecommendations
* @see app/Http/Controllers/Api/AnalyticsController.php:116
* @route '/api/analytics/recommendations'
*/
getRecommendations.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getRecommendations.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getRecommendations
* @see app/Http/Controllers/Api/AnalyticsController.php:116
* @route '/api/analytics/recommendations'
*/
getRecommendations.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getRecommendations.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getRecommendations
* @see app/Http/Controllers/Api/AnalyticsController.php:116
* @route '/api/analytics/recommendations'
*/
const getRecommendationsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getRecommendations.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getRecommendations
* @see app/Http/Controllers/Api/AnalyticsController.php:116
* @route '/api/analytics/recommendations'
*/
getRecommendationsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getRecommendations.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getRecommendations
* @see app/Http/Controllers/Api/AnalyticsController.php:116
* @route '/api/analytics/recommendations'
*/
getRecommendationsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getRecommendations.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getRecommendations.form = getRecommendationsForm

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getCurrentMetrics
* @see app/Http/Controllers/Api/AnalyticsController.php:143
* @route '/api/analytics/metrics/current'
*/
export const getCurrentMetrics = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCurrentMetrics.url(options),
    method: 'get',
})

getCurrentMetrics.definition = {
    methods: ["get","head"],
    url: '/api/analytics/metrics/current',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getCurrentMetrics
* @see app/Http/Controllers/Api/AnalyticsController.php:143
* @route '/api/analytics/metrics/current'
*/
getCurrentMetrics.url = (options?: RouteQueryOptions) => {
    return getCurrentMetrics.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getCurrentMetrics
* @see app/Http/Controllers/Api/AnalyticsController.php:143
* @route '/api/analytics/metrics/current'
*/
getCurrentMetrics.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCurrentMetrics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getCurrentMetrics
* @see app/Http/Controllers/Api/AnalyticsController.php:143
* @route '/api/analytics/metrics/current'
*/
getCurrentMetrics.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCurrentMetrics.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getCurrentMetrics
* @see app/Http/Controllers/Api/AnalyticsController.php:143
* @route '/api/analytics/metrics/current'
*/
const getCurrentMetricsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCurrentMetrics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getCurrentMetrics
* @see app/Http/Controllers/Api/AnalyticsController.php:143
* @route '/api/analytics/metrics/current'
*/
getCurrentMetricsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCurrentMetrics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getCurrentMetrics
* @see app/Http/Controllers/Api/AnalyticsController.php:143
* @route '/api/analytics/metrics/current'
*/
getCurrentMetricsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCurrentMetrics.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getCurrentMetrics.form = getCurrentMetricsForm

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getMetricsHistory
* @see app/Http/Controllers/Api/AnalyticsController.php:160
* @route '/api/analytics/metrics/history'
*/
export const getMetricsHistory = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMetricsHistory.url(options),
    method: 'get',
})

getMetricsHistory.definition = {
    methods: ["get","head"],
    url: '/api/analytics/metrics/history',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getMetricsHistory
* @see app/Http/Controllers/Api/AnalyticsController.php:160
* @route '/api/analytics/metrics/history'
*/
getMetricsHistory.url = (options?: RouteQueryOptions) => {
    return getMetricsHistory.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getMetricsHistory
* @see app/Http/Controllers/Api/AnalyticsController.php:160
* @route '/api/analytics/metrics/history'
*/
getMetricsHistory.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMetricsHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getMetricsHistory
* @see app/Http/Controllers/Api/AnalyticsController.php:160
* @route '/api/analytics/metrics/history'
*/
getMetricsHistory.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getMetricsHistory.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getMetricsHistory
* @see app/Http/Controllers/Api/AnalyticsController.php:160
* @route '/api/analytics/metrics/history'
*/
const getMetricsHistoryForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getMetricsHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getMetricsHistory
* @see app/Http/Controllers/Api/AnalyticsController.php:160
* @route '/api/analytics/metrics/history'
*/
getMetricsHistoryForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getMetricsHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getMetricsHistory
* @see app/Http/Controllers/Api/AnalyticsController.php:160
* @route '/api/analytics/metrics/history'
*/
getMetricsHistoryForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getMetricsHistory.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getMetricsHistory.form = getMetricsHistoryForm

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getMetricsComparison
* @see app/Http/Controllers/Api/AnalyticsController.php:185
* @route '/api/analytics/metrics/comparison'
*/
export const getMetricsComparison = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMetricsComparison.url(options),
    method: 'get',
})

getMetricsComparison.definition = {
    methods: ["get","head"],
    url: '/api/analytics/metrics/comparison',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getMetricsComparison
* @see app/Http/Controllers/Api/AnalyticsController.php:185
* @route '/api/analytics/metrics/comparison'
*/
getMetricsComparison.url = (options?: RouteQueryOptions) => {
    return getMetricsComparison.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getMetricsComparison
* @see app/Http/Controllers/Api/AnalyticsController.php:185
* @route '/api/analytics/metrics/comparison'
*/
getMetricsComparison.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMetricsComparison.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getMetricsComparison
* @see app/Http/Controllers/Api/AnalyticsController.php:185
* @route '/api/analytics/metrics/comparison'
*/
getMetricsComparison.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getMetricsComparison.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getMetricsComparison
* @see app/Http/Controllers/Api/AnalyticsController.php:185
* @route '/api/analytics/metrics/comparison'
*/
const getMetricsComparisonForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getMetricsComparison.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getMetricsComparison
* @see app/Http/Controllers/Api/AnalyticsController.php:185
* @route '/api/analytics/metrics/comparison'
*/
getMetricsComparisonForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getMetricsComparison.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getMetricsComparison
* @see app/Http/Controllers/Api/AnalyticsController.php:185
* @route '/api/analytics/metrics/comparison'
*/
getMetricsComparisonForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getMetricsComparison.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getMetricsComparison.form = getMetricsComparisonForm

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getLatencyPercentiles
* @see app/Http/Controllers/Api/AnalyticsController.php:203
* @route '/api/analytics/metrics/latency-percentiles'
*/
export const getLatencyPercentiles = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getLatencyPercentiles.url(options),
    method: 'get',
})

getLatencyPercentiles.definition = {
    methods: ["get","head"],
    url: '/api/analytics/metrics/latency-percentiles',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getLatencyPercentiles
* @see app/Http/Controllers/Api/AnalyticsController.php:203
* @route '/api/analytics/metrics/latency-percentiles'
*/
getLatencyPercentiles.url = (options?: RouteQueryOptions) => {
    return getLatencyPercentiles.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getLatencyPercentiles
* @see app/Http/Controllers/Api/AnalyticsController.php:203
* @route '/api/analytics/metrics/latency-percentiles'
*/
getLatencyPercentiles.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getLatencyPercentiles.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getLatencyPercentiles
* @see app/Http/Controllers/Api/AnalyticsController.php:203
* @route '/api/analytics/metrics/latency-percentiles'
*/
getLatencyPercentiles.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getLatencyPercentiles.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getLatencyPercentiles
* @see app/Http/Controllers/Api/AnalyticsController.php:203
* @route '/api/analytics/metrics/latency-percentiles'
*/
const getLatencyPercentilesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getLatencyPercentiles.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getLatencyPercentiles
* @see app/Http/Controllers/Api/AnalyticsController.php:203
* @route '/api/analytics/metrics/latency-percentiles'
*/
getLatencyPercentilesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getLatencyPercentiles.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getLatencyPercentiles
* @see app/Http/Controllers/Api/AnalyticsController.php:203
* @route '/api/analytics/metrics/latency-percentiles'
*/
getLatencyPercentilesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getLatencyPercentiles.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getLatencyPercentiles.form = getLatencyPercentilesForm

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getDashboardSummary
* @see app/Http/Controllers/Api/AnalyticsController.php:221
* @route '/api/analytics/dashboard-summary'
*/
export const getDashboardSummary = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getDashboardSummary.url(options),
    method: 'get',
})

getDashboardSummary.definition = {
    methods: ["get","head"],
    url: '/api/analytics/dashboard-summary',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getDashboardSummary
* @see app/Http/Controllers/Api/AnalyticsController.php:221
* @route '/api/analytics/dashboard-summary'
*/
getDashboardSummary.url = (options?: RouteQueryOptions) => {
    return getDashboardSummary.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getDashboardSummary
* @see app/Http/Controllers/Api/AnalyticsController.php:221
* @route '/api/analytics/dashboard-summary'
*/
getDashboardSummary.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getDashboardSummary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getDashboardSummary
* @see app/Http/Controllers/Api/AnalyticsController.php:221
* @route '/api/analytics/dashboard-summary'
*/
getDashboardSummary.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getDashboardSummary.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getDashboardSummary
* @see app/Http/Controllers/Api/AnalyticsController.php:221
* @route '/api/analytics/dashboard-summary'
*/
const getDashboardSummaryForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getDashboardSummary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getDashboardSummary
* @see app/Http/Controllers/Api/AnalyticsController.php:221
* @route '/api/analytics/dashboard-summary'
*/
getDashboardSummaryForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getDashboardSummary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AnalyticsController::getDashboardSummary
* @see app/Http/Controllers/Api/AnalyticsController.php:221
* @route '/api/analytics/dashboard-summary'
*/
getDashboardSummaryForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getDashboardSummary.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getDashboardSummary.form = getDashboardSummaryForm

const AnalyticsController = { getAnomalies, forecastCompliance, predictDeploymentWindow, predictResourceNeeds, assessTransitionRisk, getRecommendations, getCurrentMetrics, getMetricsHistory, getMetricsComparison, getLatencyPercentiles, getDashboardSummary }

export default AnalyticsController