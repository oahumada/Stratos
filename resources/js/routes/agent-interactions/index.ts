import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Api\AgentInteractionMetricsController::summary
* @see app/Http/Controllers/Api/AgentInteractionMetricsController.php:22
* @route '/api/agent-interactions/metrics/summary'
*/
export const summary = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: summary.url(options),
    method: 'get',
})

summary.definition = {
    methods: ["get","head"],
    url: '/api/agent-interactions/metrics/summary',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AgentInteractionMetricsController::summary
* @see app/Http/Controllers/Api/AgentInteractionMetricsController.php:22
* @route '/api/agent-interactions/metrics/summary'
*/
summary.url = (options?: RouteQueryOptions) => {
    return summary.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AgentInteractionMetricsController::summary
* @see app/Http/Controllers/Api/AgentInteractionMetricsController.php:22
* @route '/api/agent-interactions/metrics/summary'
*/
summary.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: summary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AgentInteractionMetricsController::summary
* @see app/Http/Controllers/Api/AgentInteractionMetricsController.php:22
* @route '/api/agent-interactions/metrics/summary'
*/
summary.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: summary.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AgentInteractionMetricsController::summary
* @see app/Http/Controllers/Api/AgentInteractionMetricsController.php:22
* @route '/api/agent-interactions/metrics/summary'
*/
const summaryForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: summary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AgentInteractionMetricsController::summary
* @see app/Http/Controllers/Api/AgentInteractionMetricsController.php:22
* @route '/api/agent-interactions/metrics/summary'
*/
summaryForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: summary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AgentInteractionMetricsController::summary
* @see app/Http/Controllers/Api/AgentInteractionMetricsController.php:22
* @route '/api/agent-interactions/metrics/summary'
*/
summaryForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: summary.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

summary.form = summaryForm

/**
* @see \App\Http\Controllers\Api\AgentInteractionMetricsController::failingAgents
* @see app/Http/Controllers/Api/AgentInteractionMetricsController.php:38
* @route '/api/agent-interactions/metrics/failing-agents'
*/
export const failingAgents = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: failingAgents.url(options),
    method: 'get',
})

failingAgents.definition = {
    methods: ["get","head"],
    url: '/api/agent-interactions/metrics/failing-agents',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AgentInteractionMetricsController::failingAgents
* @see app/Http/Controllers/Api/AgentInteractionMetricsController.php:38
* @route '/api/agent-interactions/metrics/failing-agents'
*/
failingAgents.url = (options?: RouteQueryOptions) => {
    return failingAgents.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AgentInteractionMetricsController::failingAgents
* @see app/Http/Controllers/Api/AgentInteractionMetricsController.php:38
* @route '/api/agent-interactions/metrics/failing-agents'
*/
failingAgents.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: failingAgents.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AgentInteractionMetricsController::failingAgents
* @see app/Http/Controllers/Api/AgentInteractionMetricsController.php:38
* @route '/api/agent-interactions/metrics/failing-agents'
*/
failingAgents.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: failingAgents.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AgentInteractionMetricsController::failingAgents
* @see app/Http/Controllers/Api/AgentInteractionMetricsController.php:38
* @route '/api/agent-interactions/metrics/failing-agents'
*/
const failingAgentsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: failingAgents.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AgentInteractionMetricsController::failingAgents
* @see app/Http/Controllers/Api/AgentInteractionMetricsController.php:38
* @route '/api/agent-interactions/metrics/failing-agents'
*/
failingAgentsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: failingAgents.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AgentInteractionMetricsController::failingAgents
* @see app/Http/Controllers/Api/AgentInteractionMetricsController.php:38
* @route '/api/agent-interactions/metrics/failing-agents'
*/
failingAgentsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: failingAgents.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

failingAgents.form = failingAgentsForm

/**
* @see \App\Http\Controllers\Api\AgentInteractionMetricsController::latencyByAgent
* @see app/Http/Controllers/Api/AgentInteractionMetricsController.php:55
* @route '/api/agent-interactions/metrics/latency-by-agent'
*/
export const latencyByAgent = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: latencyByAgent.url(options),
    method: 'get',
})

latencyByAgent.definition = {
    methods: ["get","head"],
    url: '/api/agent-interactions/metrics/latency-by-agent',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AgentInteractionMetricsController::latencyByAgent
* @see app/Http/Controllers/Api/AgentInteractionMetricsController.php:55
* @route '/api/agent-interactions/metrics/latency-by-agent'
*/
latencyByAgent.url = (options?: RouteQueryOptions) => {
    return latencyByAgent.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AgentInteractionMetricsController::latencyByAgent
* @see app/Http/Controllers/Api/AgentInteractionMetricsController.php:55
* @route '/api/agent-interactions/metrics/latency-by-agent'
*/
latencyByAgent.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: latencyByAgent.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AgentInteractionMetricsController::latencyByAgent
* @see app/Http/Controllers/Api/AgentInteractionMetricsController.php:55
* @route '/api/agent-interactions/metrics/latency-by-agent'
*/
latencyByAgent.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: latencyByAgent.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AgentInteractionMetricsController::latencyByAgent
* @see app/Http/Controllers/Api/AgentInteractionMetricsController.php:55
* @route '/api/agent-interactions/metrics/latency-by-agent'
*/
const latencyByAgentForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: latencyByAgent.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AgentInteractionMetricsController::latencyByAgent
* @see app/Http/Controllers/Api/AgentInteractionMetricsController.php:55
* @route '/api/agent-interactions/metrics/latency-by-agent'
*/
latencyByAgentForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: latencyByAgent.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AgentInteractionMetricsController::latencyByAgent
* @see app/Http/Controllers/Api/AgentInteractionMetricsController.php:55
* @route '/api/agent-interactions/metrics/latency-by-agent'
*/
latencyByAgentForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: latencyByAgent.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

latencyByAgent.form = latencyByAgentForm

const agentInteractions = {
    summary: Object.assign(summary, summary),
    failingAgents: Object.assign(failingAgents, failingAgents),
    latencyByAgent: Object.assign(latencyByAgent, latencyByAgent),
}

export default agentInteractions