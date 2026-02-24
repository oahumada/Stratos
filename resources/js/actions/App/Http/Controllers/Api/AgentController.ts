import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\AgentController::index
* @see app/Http/Controllers/Api/AgentController.php:16
* @route '/api/agents'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/agents',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AgentController::index
* @see app/Http/Controllers/Api/AgentController.php:16
* @route '/api/agents'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AgentController::index
* @see app/Http/Controllers/Api/AgentController.php:16
* @route '/api/agents'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AgentController::index
* @see app/Http/Controllers/Api/AgentController.php:16
* @route '/api/agents'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AgentController::index
* @see app/Http/Controllers/Api/AgentController.php:16
* @route '/api/agents'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AgentController::index
* @see app/Http/Controllers/Api/AgentController.php:16
* @route '/api/agents'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AgentController::index
* @see app/Http/Controllers/Api/AgentController.php:16
* @route '/api/agents'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Api\AgentController::testAgent
* @see app/Http/Controllers/Api/AgentController.php:45
* @route '/api/agents/test'
*/
export const testAgent = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: testAgent.url(options),
    method: 'post',
})

testAgent.definition = {
    methods: ["post"],
    url: '/api/agents/test',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AgentController::testAgent
* @see app/Http/Controllers/Api/AgentController.php:45
* @route '/api/agents/test'
*/
testAgent.url = (options?: RouteQueryOptions) => {
    return testAgent.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AgentController::testAgent
* @see app/Http/Controllers/Api/AgentController.php:45
* @route '/api/agents/test'
*/
testAgent.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: testAgent.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AgentController::testAgent
* @see app/Http/Controllers/Api/AgentController.php:45
* @route '/api/agents/test'
*/
const testAgentForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: testAgent.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AgentController::testAgent
* @see app/Http/Controllers/Api/AgentController.php:45
* @route '/api/agents/test'
*/
testAgentForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: testAgent.url(options),
    method: 'post',
})

testAgent.form = testAgentForm

const AgentController = { index, testAgent }

export default AgentController