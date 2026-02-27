import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
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
* @see \App\Http\Controllers\Api\PromptInstructionController::index
* @see app/Http/Controllers/Api/PromptInstructionController.php:13
* @route '/api/strategic-planning/scenarios/instructions'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::index
* @see app/Http/Controllers/Api/PromptInstructionController.php:13
* @route '/api/strategic-planning/scenarios/instructions'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::index
* @see app/Http/Controllers/Api/PromptInstructionController.php:13
* @route '/api/strategic-planning/scenarios/instructions'
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
* @see \App\Http\Controllers\Api\PromptInstructionController::store
* @see app/Http/Controllers/Api/PromptInstructionController.php:95
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
* @see app/Http/Controllers/Api/PromptInstructionController.php:95
* @route '/api/strategic-planning/scenarios/instructions'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::store
* @see app/Http/Controllers/Api/PromptInstructionController.php:95
* @route '/api/strategic-planning/scenarios/instructions'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::store
* @see app/Http/Controllers/Api/PromptInstructionController.php:95
* @route '/api/strategic-planning/scenarios/instructions'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::store
* @see app/Http/Controllers/Api/PromptInstructionController.php:95
* @route '/api/strategic-planning/scenarios/instructions'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::restoreDefault
* @see app/Http/Controllers/Api/PromptInstructionController.php:160
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
* @see app/Http/Controllers/Api/PromptInstructionController.php:160
* @route '/api/strategic-planning/scenarios/instructions/restore-default'
*/
restoreDefault.url = (options?: RouteQueryOptions) => {
    return restoreDefault.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::restoreDefault
* @see app/Http/Controllers/Api/PromptInstructionController.php:160
* @route '/api/strategic-planning/scenarios/instructions/restore-default'
*/
restoreDefault.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restoreDefault.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::restoreDefault
* @see app/Http/Controllers/Api/PromptInstructionController.php:160
* @route '/api/strategic-planning/scenarios/instructions/restore-default'
*/
const restoreDefaultForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restoreDefault.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::restoreDefault
* @see app/Http/Controllers/Api/PromptInstructionController.php:160
* @route '/api/strategic-planning/scenarios/instructions/restore-default'
*/
restoreDefaultForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restoreDefault.url(options),
    method: 'post',
})

restoreDefault.form = restoreDefaultForm

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::show
* @see app/Http/Controllers/Api/PromptInstructionController.php:120
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
* @see app/Http/Controllers/Api/PromptInstructionController.php:120
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
* @see app/Http/Controllers/Api/PromptInstructionController.php:120
* @route '/api/strategic-planning/scenarios/instructions/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::show
* @see app/Http/Controllers/Api/PromptInstructionController.php:120
* @route '/api/strategic-planning/scenarios/instructions/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::show
* @see app/Http/Controllers/Api/PromptInstructionController.php:120
* @route '/api/strategic-planning/scenarios/instructions/{id}'
*/
const showForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::show
* @see app/Http/Controllers/Api/PromptInstructionController.php:120
* @route '/api/strategic-planning/scenarios/instructions/{id}'
*/
showForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::show
* @see app/Http/Controllers/Api/PromptInstructionController.php:120
* @route '/api/strategic-planning/scenarios/instructions/{id}'
*/
showForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::update
* @see app/Http/Controllers/Api/PromptInstructionController.php:133
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
* @see app/Http/Controllers/Api/PromptInstructionController.php:133
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
* @see app/Http/Controllers/Api/PromptInstructionController.php:133
* @route '/api/strategic-planning/scenarios/instructions/{id}'
*/
update.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::update
* @see app/Http/Controllers/Api/PromptInstructionController.php:133
* @route '/api/strategic-planning/scenarios/instructions/{id}'
*/
const updateForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PromptInstructionController::update
* @see app/Http/Controllers/Api/PromptInstructionController.php:133
* @route '/api/strategic-planning/scenarios/instructions/{id}'
*/
updateForm.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

const PromptInstructionController = { index, store, restoreDefault, show, update }

export default PromptInstructionController