import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
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
* @see \App\Http\Controllers\Api\AgentController::update
* @see app/Http/Controllers/Api/AgentController.php:71
* @route '/api/agents/{agent}'
*/
export const update = (args: { agent: string | number } | [agent: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/agents/{agent}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\AgentController::update
* @see app/Http/Controllers/Api/AgentController.php:71
* @route '/api/agents/{agent}'
*/
update.url = (args: { agent: string | number } | [agent: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { agent: args }
    }

    if (Array.isArray(args)) {
        args = {
            agent: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        agent: args.agent,
    }

    return update.definition.url
            .replace('{agent}', parsedArgs.agent.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AgentController::update
* @see app/Http/Controllers/Api/AgentController.php:71
* @route '/api/agents/{agent}'
*/
update.put = (args: { agent: string | number } | [agent: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Api\AgentController::testAgent
* @see app/Http/Controllers/Api/AgentController.php:52
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
* @see app/Http/Controllers/Api/AgentController.php:52
* @route '/api/agents/test'
*/
testAgent.url = (options?: RouteQueryOptions) => {
    return testAgent.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AgentController::testAgent
* @see app/Http/Controllers/Api/AgentController.php:52
* @route '/api/agents/test'
*/
testAgent.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: testAgent.url(options),
    method: 'post',
})

const AgentController = { index, update, testAgent }

export default AgentController