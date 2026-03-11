import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ChangeSetController::store
* @see app/Http/Controllers/Api/ChangeSetController.php:22
* @route '/api/strategic-planning/scenarios/{scenarioId}/change-sets'
*/
export const store = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/{scenarioId}/change-sets',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ChangeSetController::store
* @see app/Http/Controllers/Api/ChangeSetController.php:22
* @route '/api/strategic-planning/scenarios/{scenarioId}/change-sets'
*/
store.url = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenarioId: args }
    }

    if (Array.isArray(args)) {
        args = {
            scenarioId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenarioId: args.scenarioId,
    }

    return store.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ChangeSetController::store
* @see app/Http/Controllers/Api/ChangeSetController.php:22
* @route '/api/strategic-planning/scenarios/{scenarioId}/change-sets'
*/
store.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ChangeSetController::store
* @see app/Http/Controllers/Api/ChangeSetController.php:22
* @route '/api/strategic-planning/scenarios/{scenarioId}/change-sets'
*/
const storeForm = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ChangeSetController::store
* @see app/Http/Controllers/Api/ChangeSetController.php:22
* @route '/api/strategic-planning/scenarios/{scenarioId}/change-sets'
*/
storeForm.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\ChangeSetController::preview
* @see app/Http/Controllers/Api/ChangeSetController.php:49
* @route '/api/strategic-planning/change-sets/{id}/preview'
*/
export const preview = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: preview.url(args, options),
    method: 'get',
})

preview.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/change-sets/{id}/preview',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ChangeSetController::preview
* @see app/Http/Controllers/Api/ChangeSetController.php:49
* @route '/api/strategic-planning/change-sets/{id}/preview'
*/
preview.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return preview.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ChangeSetController::preview
* @see app/Http/Controllers/Api/ChangeSetController.php:49
* @route '/api/strategic-planning/change-sets/{id}/preview'
*/
preview.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: preview.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ChangeSetController::preview
* @see app/Http/Controllers/Api/ChangeSetController.php:49
* @route '/api/strategic-planning/change-sets/{id}/preview'
*/
preview.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: preview.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ChangeSetController::preview
* @see app/Http/Controllers/Api/ChangeSetController.php:49
* @route '/api/strategic-planning/change-sets/{id}/preview'
*/
const previewForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: preview.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ChangeSetController::preview
* @see app/Http/Controllers/Api/ChangeSetController.php:49
* @route '/api/strategic-planning/change-sets/{id}/preview'
*/
previewForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: preview.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ChangeSetController::preview
* @see app/Http/Controllers/Api/ChangeSetController.php:49
* @route '/api/strategic-planning/change-sets/{id}/preview'
*/
previewForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: preview.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

preview.form = previewForm

/**
* @see \App\Http\Controllers\Api\ChangeSetController::apply
* @see app/Http/Controllers/Api/ChangeSetController.php:60
* @route '/api/strategic-planning/change-sets/{id}/apply'
*/
export const apply = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: apply.url(args, options),
    method: 'post',
})

apply.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/change-sets/{id}/apply',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ChangeSetController::apply
* @see app/Http/Controllers/Api/ChangeSetController.php:60
* @route '/api/strategic-planning/change-sets/{id}/apply'
*/
apply.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return apply.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ChangeSetController::apply
* @see app/Http/Controllers/Api/ChangeSetController.php:60
* @route '/api/strategic-planning/change-sets/{id}/apply'
*/
apply.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: apply.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ChangeSetController::apply
* @see app/Http/Controllers/Api/ChangeSetController.php:60
* @route '/api/strategic-planning/change-sets/{id}/apply'
*/
const applyForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: apply.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ChangeSetController::apply
* @see app/Http/Controllers/Api/ChangeSetController.php:60
* @route '/api/strategic-planning/change-sets/{id}/apply'
*/
applyForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: apply.url(args, options),
    method: 'post',
})

apply.form = applyForm

/**
* @see \App\Http\Controllers\Api\ChangeSetController::addOp
* @see app/Http/Controllers/Api/ChangeSetController.php:74
* @route '/api/strategic-planning/change-sets/{id}/ops'
*/
export const addOp = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addOp.url(args, options),
    method: 'post',
})

addOp.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/change-sets/{id}/ops',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ChangeSetController::addOp
* @see app/Http/Controllers/Api/ChangeSetController.php:74
* @route '/api/strategic-planning/change-sets/{id}/ops'
*/
addOp.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return addOp.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ChangeSetController::addOp
* @see app/Http/Controllers/Api/ChangeSetController.php:74
* @route '/api/strategic-planning/change-sets/{id}/ops'
*/
addOp.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addOp.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ChangeSetController::addOp
* @see app/Http/Controllers/Api/ChangeSetController.php:74
* @route '/api/strategic-planning/change-sets/{id}/ops'
*/
const addOpForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: addOp.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ChangeSetController::addOp
* @see app/Http/Controllers/Api/ChangeSetController.php:74
* @route '/api/strategic-planning/change-sets/{id}/ops'
*/
addOpForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: addOp.url(args, options),
    method: 'post',
})

addOp.form = addOpForm

/**
* @see \App\Http\Controllers\Api\ChangeSetController::canApply
* @see app/Http/Controllers/Api/ChangeSetController.php:95
* @route '/api/strategic-planning/change-sets/{id}/can-apply'
*/
export const canApply = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: canApply.url(args, options),
    method: 'get',
})

canApply.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/change-sets/{id}/can-apply',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ChangeSetController::canApply
* @see app/Http/Controllers/Api/ChangeSetController.php:95
* @route '/api/strategic-planning/change-sets/{id}/can-apply'
*/
canApply.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return canApply.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ChangeSetController::canApply
* @see app/Http/Controllers/Api/ChangeSetController.php:95
* @route '/api/strategic-planning/change-sets/{id}/can-apply'
*/
canApply.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: canApply.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ChangeSetController::canApply
* @see app/Http/Controllers/Api/ChangeSetController.php:95
* @route '/api/strategic-planning/change-sets/{id}/can-apply'
*/
canApply.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: canApply.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ChangeSetController::canApply
* @see app/Http/Controllers/Api/ChangeSetController.php:95
* @route '/api/strategic-planning/change-sets/{id}/can-apply'
*/
const canApplyForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: canApply.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ChangeSetController::canApply
* @see app/Http/Controllers/Api/ChangeSetController.php:95
* @route '/api/strategic-planning/change-sets/{id}/can-apply'
*/
canApplyForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: canApply.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ChangeSetController::canApply
* @see app/Http/Controllers/Api/ChangeSetController.php:95
* @route '/api/strategic-planning/change-sets/{id}/can-apply'
*/
canApplyForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: canApply.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

canApply.form = canApplyForm

/**
* @see \App\Http\Controllers\Api\ChangeSetController::approve
* @see app/Http/Controllers/Api/ChangeSetController.php:107
* @route '/api/strategic-planning/change-sets/{id}/approve'
*/
export const approve = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

approve.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/change-sets/{id}/approve',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ChangeSetController::approve
* @see app/Http/Controllers/Api/ChangeSetController.php:107
* @route '/api/strategic-planning/change-sets/{id}/approve'
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
* @see \App\Http\Controllers\Api\ChangeSetController::approve
* @see app/Http/Controllers/Api/ChangeSetController.php:107
* @route '/api/strategic-planning/change-sets/{id}/approve'
*/
approve.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ChangeSetController::approve
* @see app/Http/Controllers/Api/ChangeSetController.php:107
* @route '/api/strategic-planning/change-sets/{id}/approve'
*/
const approveForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ChangeSetController::approve
* @see app/Http/Controllers/Api/ChangeSetController.php:107
* @route '/api/strategic-planning/change-sets/{id}/approve'
*/
approveForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve.url(args, options),
    method: 'post',
})

approve.form = approveForm

/**
* @see \App\Http\Controllers\Api\ChangeSetController::reject
* @see app/Http/Controllers/Api/ChangeSetController.php:159
* @route '/api/strategic-planning/change-sets/{id}/reject'
*/
export const reject = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject.url(args, options),
    method: 'post',
})

reject.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/change-sets/{id}/reject',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ChangeSetController::reject
* @see app/Http/Controllers/Api/ChangeSetController.php:159
* @route '/api/strategic-planning/change-sets/{id}/reject'
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
* @see \App\Http\Controllers\Api\ChangeSetController::reject
* @see app/Http/Controllers/Api/ChangeSetController.php:159
* @route '/api/strategic-planning/change-sets/{id}/reject'
*/
reject.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ChangeSetController::reject
* @see app/Http/Controllers/Api/ChangeSetController.php:159
* @route '/api/strategic-planning/change-sets/{id}/reject'
*/
const rejectForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reject.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ChangeSetController::reject
* @see app/Http/Controllers/Api/ChangeSetController.php:159
* @route '/api/strategic-planning/change-sets/{id}/reject'
*/
rejectForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reject.url(args, options),
    method: 'post',
})

reject.form = rejectForm

const ChangeSetController = { store, preview, apply, addOp, canApply, approve, reject }

export default ChangeSetController