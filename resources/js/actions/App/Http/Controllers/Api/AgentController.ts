import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
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
* @see \App\Http\Controllers\Api\AgentController::update
* @see app/Http/Controllers/Api/AgentController.php:63
* @route '/api/agents/{agent}'
*/
export const update = (args: { agent: number | { id: number } } | [agent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/agents/{agent}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\AgentController::update
* @see app/Http/Controllers/Api/AgentController.php:63
* @route '/api/agents/{agent}'
*/
update.url = (args: { agent: number | { id: number } } | [agent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { agent: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { agent: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            agent: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        agent: typeof args.agent === 'object'
        ? args.agent.id
        : args.agent,
    }

    return update.definition.url
            .replace('{agent}', parsedArgs.agent.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AgentController::update
* @see app/Http/Controllers/Api/AgentController.php:63
* @route '/api/agents/{agent}'
*/
update.put = (args: { agent: number | { id: number } } | [agent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Api\AgentController::update
* @see app/Http/Controllers/Api/AgentController.php:63
* @route '/api/agents/{agent}'
*/
const updateForm = (args: { agent: number | { id: number } } | [agent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AgentController::update
* @see app/Http/Controllers/Api/AgentController.php:63
* @route '/api/agents/{agent}'
*/
updateForm.put = (args: { agent: number | { id: number } } | [agent: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

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

const AgentController = { index, update, testAgent }

export default AgentController