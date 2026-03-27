import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
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

const agentInteractions = {
    summary: Object.assign(summary, summary),
    failingAgents: Object.assign(failingAgents, failingAgents),
    latencyByAgent: Object.assign(latencyByAgent, latencyByAgent),
}

export default agentInteractions