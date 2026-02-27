import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CompetencyVersionController::index
* @see app/Http/Controllers/CompetencyVersionController.php:12
* @route '/api/competencies/{competencyId}/versions'
*/
export const index = (args: { competencyId: string | number } | [competencyId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/competencies/{competencyId}/versions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CompetencyVersionController::index
* @see app/Http/Controllers/CompetencyVersionController.php:12
* @route '/api/competencies/{competencyId}/versions'
*/
index.url = (args: { competencyId: string | number } | [competencyId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { competencyId: args }
    }

    if (Array.isArray(args)) {
        args = {
            competencyId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competencyId: args.competencyId,
    }

    return index.definition.url
            .replace('{competencyId}', parsedArgs.competencyId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetencyVersionController::index
* @see app/Http/Controllers/CompetencyVersionController.php:12
* @route '/api/competencies/{competencyId}/versions'
*/
index.get = (args: { competencyId: string | number } | [competencyId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CompetencyVersionController::index
* @see app/Http/Controllers/CompetencyVersionController.php:12
* @route '/api/competencies/{competencyId}/versions'
*/
index.head = (args: { competencyId: string | number } | [competencyId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CompetencyVersionController::index
* @see app/Http/Controllers/CompetencyVersionController.php:12
* @route '/api/competencies/{competencyId}/versions'
*/
const indexForm = (args: { competencyId: string | number } | [competencyId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CompetencyVersionController::index
* @see app/Http/Controllers/CompetencyVersionController.php:12
* @route '/api/competencies/{competencyId}/versions'
*/
indexForm.get = (args: { competencyId: string | number } | [competencyId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CompetencyVersionController::index
* @see app/Http/Controllers/CompetencyVersionController.php:12
* @route '/api/competencies/{competencyId}/versions'
*/
indexForm.head = (args: { competencyId: string | number } | [competencyId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\CompetencyVersionController::store
* @see app/Http/Controllers/CompetencyVersionController.php:28
* @route '/api/competencies/{competencyId}/versions'
*/
export const store = (args: { competencyId: string | number } | [competencyId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/competencies/{competencyId}/versions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CompetencyVersionController::store
* @see app/Http/Controllers/CompetencyVersionController.php:28
* @route '/api/competencies/{competencyId}/versions'
*/
store.url = (args: { competencyId: string | number } | [competencyId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { competencyId: args }
    }

    if (Array.isArray(args)) {
        args = {
            competencyId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competencyId: args.competencyId,
    }

    return store.definition.url
            .replace('{competencyId}', parsedArgs.competencyId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetencyVersionController::store
* @see app/Http/Controllers/CompetencyVersionController.php:28
* @route '/api/competencies/{competencyId}/versions'
*/
store.post = (args: { competencyId: string | number } | [competencyId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CompetencyVersionController::store
* @see app/Http/Controllers/CompetencyVersionController.php:28
* @route '/api/competencies/{competencyId}/versions'
*/
const storeForm = (args: { competencyId: string | number } | [competencyId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CompetencyVersionController::store
* @see app/Http/Controllers/CompetencyVersionController.php:28
* @route '/api/competencies/{competencyId}/versions'
*/
storeForm.post = (args: { competencyId: string | number } | [competencyId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\CompetencyVersionController::show
* @see app/Http/Controllers/CompetencyVersionController.php:62
* @route '/api/competencies/{competencyId}/versions/{id}'
*/
export const show = (args: { competencyId: string | number, id: string | number } | [competencyId: string | number, id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/competencies/{competencyId}/versions/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CompetencyVersionController::show
* @see app/Http/Controllers/CompetencyVersionController.php:62
* @route '/api/competencies/{competencyId}/versions/{id}'
*/
show.url = (args: { competencyId: string | number, id: string | number } | [competencyId: string | number, id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competencyId: args[0],
            id: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competencyId: args.competencyId,
        id: args.id,
    }

    return show.definition.url
            .replace('{competencyId}', parsedArgs.competencyId.toString())
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetencyVersionController::show
* @see app/Http/Controllers/CompetencyVersionController.php:62
* @route '/api/competencies/{competencyId}/versions/{id}'
*/
show.get = (args: { competencyId: string | number, id: string | number } | [competencyId: string | number, id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CompetencyVersionController::show
* @see app/Http/Controllers/CompetencyVersionController.php:62
* @route '/api/competencies/{competencyId}/versions/{id}'
*/
show.head = (args: { competencyId: string | number, id: string | number } | [competencyId: string | number, id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CompetencyVersionController::show
* @see app/Http/Controllers/CompetencyVersionController.php:62
* @route '/api/competencies/{competencyId}/versions/{id}'
*/
const showForm = (args: { competencyId: string | number, id: string | number } | [competencyId: string | number, id: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CompetencyVersionController::show
* @see app/Http/Controllers/CompetencyVersionController.php:62
* @route '/api/competencies/{competencyId}/versions/{id}'
*/
showForm.get = (args: { competencyId: string | number, id: string | number } | [competencyId: string | number, id: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CompetencyVersionController::show
* @see app/Http/Controllers/CompetencyVersionController.php:62
* @route '/api/competencies/{competencyId}/versions/{id}'
*/
showForm.head = (args: { competencyId: string | number, id: string | number } | [competencyId: string | number, id: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\CompetencyVersionController::destroy
* @see app/Http/Controllers/CompetencyVersionController.php:76
* @route '/api/competencies/{competencyId}/versions/{id}'
*/
export const destroy = (args: { competencyId: string | number, id: string | number } | [competencyId: string | number, id: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/competencies/{competencyId}/versions/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\CompetencyVersionController::destroy
* @see app/Http/Controllers/CompetencyVersionController.php:76
* @route '/api/competencies/{competencyId}/versions/{id}'
*/
destroy.url = (args: { competencyId: string | number, id: string | number } | [competencyId: string | number, id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competencyId: args[0],
            id: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competencyId: args.competencyId,
        id: args.id,
    }

    return destroy.definition.url
            .replace('{competencyId}', parsedArgs.competencyId.toString())
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetencyVersionController::destroy
* @see app/Http/Controllers/CompetencyVersionController.php:76
* @route '/api/competencies/{competencyId}/versions/{id}'
*/
destroy.delete = (args: { competencyId: string | number, id: string | number } | [competencyId: string | number, id: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\CompetencyVersionController::destroy
* @see app/Http/Controllers/CompetencyVersionController.php:76
* @route '/api/competencies/{competencyId}/versions/{id}'
*/
const destroyForm = (args: { competencyId: string | number, id: string | number } | [competencyId: string | number, id: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CompetencyVersionController::destroy
* @see app/Http/Controllers/CompetencyVersionController.php:76
* @route '/api/competencies/{competencyId}/versions/{id}'
*/
destroyForm.delete = (args: { competencyId: string | number, id: string | number } | [competencyId: string | number, id: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const CompetencyVersionController = { index, store, show, destroy }

export default CompetencyVersionController