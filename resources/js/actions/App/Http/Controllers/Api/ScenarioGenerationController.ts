import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ScenarioGenerationController::store
* @see app/Http/Controllers/Api/ScenarioGenerationController.php:14
* @route '/api/strategic-planning/scenarios/generate'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/generate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationController::store
* @see app/Http/Controllers/Api/ScenarioGenerationController.php:14
* @route '/api/strategic-planning/scenarios/generate'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationController::store
* @see app/Http/Controllers/Api/ScenarioGenerationController.php:14
* @route '/api/strategic-planning/scenarios/generate'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationController::demo
* @see app/Http/Controllers/Api/ScenarioGenerationController.php:375
* @route '/api/strategic-planning/scenarios/generate/demo'
*/
export const demo = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: demo.url(options),
    method: 'post',
})

demo.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/generate/demo',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationController::demo
* @see app/Http/Controllers/Api/ScenarioGenerationController.php:375
* @route '/api/strategic-planning/scenarios/generate/demo'
*/
demo.url = (options?: RouteQueryOptions) => {
    return demo.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationController::demo
* @see app/Http/Controllers/Api/ScenarioGenerationController.php:375
* @route '/api/strategic-planning/scenarios/generate/demo'
*/
demo.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: demo.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationController::preview
* @see app/Http/Controllers/Api/ScenarioGenerationController.php:106
* @route '/api/strategic-planning/scenarios/generate/preview'
*/
export const preview = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: preview.url(options),
    method: 'post',
})

preview.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/generate/preview',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationController::preview
* @see app/Http/Controllers/Api/ScenarioGenerationController.php:106
* @route '/api/strategic-planning/scenarios/generate/preview'
*/
preview.url = (options?: RouteQueryOptions) => {
    return preview.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationController::preview
* @see app/Http/Controllers/Api/ScenarioGenerationController.php:106
* @route '/api/strategic-planning/scenarios/generate/preview'
*/
preview.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: preview.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationController::show
* @see app/Http/Controllers/Api/ScenarioGenerationController.php:170
* @route '/api/strategic-planning/scenarios/generate/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenarios/generate/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationController::show
* @see app/Http/Controllers/Api/ScenarioGenerationController.php:170
* @route '/api/strategic-planning/scenarios/generate/{id}'
*/
show.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return show.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationController::show
* @see app/Http/Controllers/Api/ScenarioGenerationController.php:170
* @route '/api/strategic-planning/scenarios/generate/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationController::show
* @see app/Http/Controllers/Api/ScenarioGenerationController.php:170
* @route '/api/strategic-planning/scenarios/generate/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationController::accept
* @see app/Http/Controllers/Api/ScenarioGenerationController.php:204
* @route '/api/strategic-planning/scenarios/generate/{id}/accept'
*/
export const accept = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: accept.url(args, options),
    method: 'post',
})

accept.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/generate/{id}/accept',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationController::accept
* @see app/Http/Controllers/Api/ScenarioGenerationController.php:204
* @route '/api/strategic-planning/scenarios/generate/{id}/accept'
*/
accept.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return accept.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationController::accept
* @see app/Http/Controllers/Api/ScenarioGenerationController.php:204
* @route '/api/strategic-planning/scenarios/generate/{id}/accept'
*/
accept.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: accept.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationController::simulateImport
* @see app/Http/Controllers/Api/ScenarioGenerationController.php:383
* @route '/api/strategic-planning/scenarios/{scenario}/simulate-import'
*/
export const simulateImport = (args: { scenario: string | number } | [scenario: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: simulateImport.url(args, options),
    method: 'post',
})

simulateImport.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/{scenario}/simulate-import',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationController::simulateImport
* @see app/Http/Controllers/Api/ScenarioGenerationController.php:383
* @route '/api/strategic-planning/scenarios/{scenario}/simulate-import'
*/
simulateImport.url = (args: { scenario: string | number } | [scenario: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenario: args }
    }

    if (Array.isArray(args)) {
        args = {
            scenario: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenario: args.scenario,
    }

    return simulateImport.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioGenerationController::simulateImport
* @see app/Http/Controllers/Api/ScenarioGenerationController.php:383
* @route '/api/strategic-planning/scenarios/{scenario}/simulate-import'
*/
simulateImport.post = (args: { scenario: string | number } | [scenario: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: simulateImport.url(args, options),
    method: 'post',
})

const ScenarioGenerationController = { store, demo, preview, show, accept, simulateImport }

export default ScenarioGenerationController