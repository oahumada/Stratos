import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\WorkforceDemandLineController::index
* @see app/Http/Controllers/Api/WorkforceDemandLineController.php:23
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines'
*/
export const index = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\WorkforceDemandLineController::index
* @see app/Http/Controllers/Api/WorkforceDemandLineController.php:23
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines'
*/
index.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return index.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WorkforceDemandLineController::index
* @see app/Http/Controllers/Api/WorkforceDemandLineController.php:23
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines'
*/
index.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WorkforceDemandLineController::index
* @see app/Http/Controllers/Api/WorkforceDemandLineController.php:23
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines'
*/
index.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\WorkforceDemandLineController::index
* @see app/Http/Controllers/Api/WorkforceDemandLineController.php:23
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines'
*/
const indexForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WorkforceDemandLineController::index
* @see app/Http/Controllers/Api/WorkforceDemandLineController.php:23
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines'
*/
indexForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WorkforceDemandLineController::index
* @see app/Http/Controllers/Api/WorkforceDemandLineController.php:23
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines'
*/
indexForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Api\WorkforceDemandLineController::store
* @see app/Http/Controllers/Api/WorkforceDemandLineController.php:62
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines'
*/
export const store = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\WorkforceDemandLineController::store
* @see app/Http/Controllers/Api/WorkforceDemandLineController.php:62
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines'
*/
store.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return store.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WorkforceDemandLineController::store
* @see app/Http/Controllers/Api/WorkforceDemandLineController.php:62
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines'
*/
store.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WorkforceDemandLineController::store
* @see app/Http/Controllers/Api/WorkforceDemandLineController.php:62
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines'
*/
const storeForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WorkforceDemandLineController::store
* @see app/Http/Controllers/Api/WorkforceDemandLineController.php:62
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines'
*/
storeForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\WorkforceDemandLineController::update
* @see app/Http/Controllers/Api/WorkforceDemandLineController.php:119
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines/{lineId}'
*/
export const update = (args: { id: string | number, lineId: string | number } | [id: string | number, lineId: string | number ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines/{lineId}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\WorkforceDemandLineController::update
* @see app/Http/Controllers/Api/WorkforceDemandLineController.php:119
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines/{lineId}'
*/
update.url = (args: { id: string | number, lineId: string | number } | [id: string | number, lineId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            id: args[0],
            lineId: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
        lineId: args.lineId,
    }

    return update.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace('{lineId}', parsedArgs.lineId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WorkforceDemandLineController::update
* @see app/Http/Controllers/Api/WorkforceDemandLineController.php:119
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines/{lineId}'
*/
update.patch = (args: { id: string | number, lineId: string | number } | [id: string | number, lineId: string | number ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\WorkforceDemandLineController::update
* @see app/Http/Controllers/Api/WorkforceDemandLineController.php:119
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines/{lineId}'
*/
const updateForm = (args: { id: string | number, lineId: string | number } | [id: string | number, lineId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WorkforceDemandLineController::update
* @see app/Http/Controllers/Api/WorkforceDemandLineController.php:119
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines/{lineId}'
*/
updateForm.patch = (args: { id: string | number, lineId: string | number } | [id: string | number, lineId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\Api\WorkforceDemandLineController::destroy
* @see app/Http/Controllers/Api/WorkforceDemandLineController.php:167
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines/{lineId}'
*/
export const destroy = (args: { id: string | number, lineId: string | number } | [id: string | number, lineId: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines/{lineId}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\WorkforceDemandLineController::destroy
* @see app/Http/Controllers/Api/WorkforceDemandLineController.php:167
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines/{lineId}'
*/
destroy.url = (args: { id: string | number, lineId: string | number } | [id: string | number, lineId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            id: args[0],
            lineId: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
        lineId: args.lineId,
    }

    return destroy.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace('{lineId}', parsedArgs.lineId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WorkforceDemandLineController::destroy
* @see app/Http/Controllers/Api/WorkforceDemandLineController.php:167
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines/{lineId}'
*/
destroy.delete = (args: { id: string | number, lineId: string | number } | [id: string | number, lineId: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\WorkforceDemandLineController::destroy
* @see app/Http/Controllers/Api/WorkforceDemandLineController.php:167
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines/{lineId}'
*/
const destroyForm = (args: { id: string | number, lineId: string | number } | [id: string | number, lineId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WorkforceDemandLineController::destroy
* @see app/Http/Controllers/Api/WorkforceDemandLineController.php:167
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines/{lineId}'
*/
destroyForm.delete = (args: { id: string | number, lineId: string | number } | [id: string | number, lineId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const WorkforceDemandLineController = { index, store, update, destroy }

export default WorkforceDemandLineController