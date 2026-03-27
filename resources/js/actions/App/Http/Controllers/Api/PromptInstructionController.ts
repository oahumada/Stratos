import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\PromptInstructionController::index
* @see app/Http/Controllers/Api/PromptInstructionController.php:13
* @route '/api/strategic-planning/scenarios/instructions'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenarios/instructions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::index
* @see app/Http/Controllers/Api/PromptInstructionController.php:13
* @route '/api/strategic-planning/scenarios/instructions'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::index
* @see app/Http/Controllers/Api/PromptInstructionController.php:13
* @route '/api/strategic-planning/scenarios/instructions'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::index
* @see app/Http/Controllers/Api/PromptInstructionController.php:13
* @route '/api/strategic-planning/scenarios/instructions'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::store
* @see app/Http/Controllers/Api/PromptInstructionController.php:102
* @route '/api/strategic-planning/scenarios/instructions'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/instructions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::store
* @see app/Http/Controllers/Api/PromptInstructionController.php:102
* @route '/api/strategic-planning/scenarios/instructions'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::store
* @see app/Http/Controllers/Api/PromptInstructionController.php:102
* @route '/api/strategic-planning/scenarios/instructions'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::restoreDefault
* @see app/Http/Controllers/Api/PromptInstructionController.php:172
* @route '/api/strategic-planning/scenarios/instructions/restore-default'
*/
export const restoreDefault = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restoreDefault.url(options),
    method: 'post',
})

restoreDefault.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/instructions/restore-default',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::restoreDefault
* @see app/Http/Controllers/Api/PromptInstructionController.php:172
* @route '/api/strategic-planning/scenarios/instructions/restore-default'
*/
restoreDefault.url = (options?: RouteQueryOptions) => {
    return restoreDefault.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::restoreDefault
* @see app/Http/Controllers/Api/PromptInstructionController.php:172
* @route '/api/strategic-planning/scenarios/instructions/restore-default'
*/
restoreDefault.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restoreDefault.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::show
* @see app/Http/Controllers/Api/PromptInstructionController.php:127
* @route '/api/strategic-planning/scenarios/instructions/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenarios/instructions/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::show
* @see app/Http/Controllers/Api/PromptInstructionController.php:127
* @route '/api/strategic-planning/scenarios/instructions/{id}'
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
* @see \App\Http\Controllers\Api\PromptInstructionController::show
* @see app/Http/Controllers/Api/PromptInstructionController.php:127
* @route '/api/strategic-planning/scenarios/instructions/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::show
* @see app/Http/Controllers/Api/PromptInstructionController.php:127
* @route '/api/strategic-planning/scenarios/instructions/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::update
* @see app/Http/Controllers/Api/PromptInstructionController.php:141
* @route '/api/strategic-planning/scenarios/instructions/{id}'
*/
export const update = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/api/strategic-planning/scenarios/instructions/{id}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::update
* @see app/Http/Controllers/Api/PromptInstructionController.php:141
* @route '/api/strategic-planning/scenarios/instructions/{id}'
*/
update.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::update
* @see app/Http/Controllers/Api/PromptInstructionController.php:141
* @route '/api/strategic-planning/scenarios/instructions/{id}'
*/
update.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

const PromptInstructionController = { index, store, restoreDefault, show, update }

export default PromptInstructionController