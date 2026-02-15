import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\IncubationController::index
* @see app/Http/Controllers/Api/IncubationController.php:28
* @route '/api/strategic-planning/scenarios/{id}/incubated-items'
*/
export const index = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenarios/{id}/incubated-items',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\IncubationController::index
* @see app/Http/Controllers/Api/IncubationController.php:28
* @route '/api/strategic-planning/scenarios/{id}/incubated-items'
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
* @see \App\Http\Controllers\Api\IncubationController::index
* @see app/Http/Controllers/Api/IncubationController.php:28
* @route '/api/strategic-planning/scenarios/{id}/incubated-items'
*/
index.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\IncubationController::index
* @see app/Http/Controllers/Api/IncubationController.php:28
* @route '/api/strategic-planning/scenarios/{id}/incubated-items'
*/
index.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\IncubationController::index
* @see app/Http/Controllers/Api/IncubationController.php:28
* @route '/api/strategic-planning/scenarios/{id}/incubated-items'
*/
const indexForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\IncubationController::index
* @see app/Http/Controllers/Api/IncubationController.php:28
* @route '/api/strategic-planning/scenarios/{id}/incubated-items'
*/
indexForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\IncubationController::index
* @see app/Http/Controllers/Api/IncubationController.php:28
* @route '/api/strategic-planning/scenarios/{id}/incubated-items'
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
* @see \App\Http\Controllers\Api\IncubationController::approve
* @see app/Http/Controllers/Api/IncubationController.php:130
* @route '/api/strategic-planning/scenarios/{id}/incubated-items/approve'
*/
export const approve = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

approve.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/{id}/incubated-items/approve',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\IncubationController::approve
* @see app/Http/Controllers/Api/IncubationController.php:130
* @route '/api/strategic-planning/scenarios/{id}/incubated-items/approve'
*/
approve.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return approve.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\IncubationController::approve
* @see app/Http/Controllers/Api/IncubationController.php:130
* @route '/api/strategic-planning/scenarios/{id}/incubated-items/approve'
*/
approve.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\IncubationController::approve
* @see app/Http/Controllers/Api/IncubationController.php:130
* @route '/api/strategic-planning/scenarios/{id}/incubated-items/approve'
*/
const approveForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\IncubationController::approve
* @see app/Http/Controllers/Api/IncubationController.php:130
* @route '/api/strategic-planning/scenarios/{id}/incubated-items/approve'
*/
approveForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve.url(args, options),
    method: 'post',
})

approve.form = approveForm

/**
* @see \App\Http\Controllers\Api\IncubationController::reject
* @see app/Http/Controllers/Api/IncubationController.php:237
* @route '/api/strategic-planning/scenarios/{id}/incubated-items/reject'
*/
export const reject = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject.url(args, options),
    method: 'post',
})

reject.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/{id}/incubated-items/reject',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\IncubationController::reject
* @see app/Http/Controllers/Api/IncubationController.php:237
* @route '/api/strategic-planning/scenarios/{id}/incubated-items/reject'
*/
reject.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return reject.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\IncubationController::reject
* @see app/Http/Controllers/Api/IncubationController.php:237
* @route '/api/strategic-planning/scenarios/{id}/incubated-items/reject'
*/
reject.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\IncubationController::reject
* @see app/Http/Controllers/Api/IncubationController.php:237
* @route '/api/strategic-planning/scenarios/{id}/incubated-items/reject'
*/
const rejectForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reject.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\IncubationController::reject
* @see app/Http/Controllers/Api/IncubationController.php:237
* @route '/api/strategic-planning/scenarios/{id}/incubated-items/reject'
*/
rejectForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reject.url(args, options),
    method: 'post',
})

reject.form = rejectForm

const IncubationController = { index, approve, reject }

export default IncubationController