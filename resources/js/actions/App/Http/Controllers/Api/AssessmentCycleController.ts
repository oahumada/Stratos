import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::index
* @see app/Http/Controllers/Api/AssessmentCycleController.php:15
* @route '/api/assessment-cycles'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/assessment-cycles',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::index
* @see app/Http/Controllers/Api/AssessmentCycleController.php:15
* @route '/api/assessment-cycles'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::index
* @see app/Http/Controllers/Api/AssessmentCycleController.php:15
* @route '/api/assessment-cycles'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::index
* @see app/Http/Controllers/Api/AssessmentCycleController.php:15
* @route '/api/assessment-cycles'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::index
* @see app/Http/Controllers/Api/AssessmentCycleController.php:15
* @route '/api/assessment-cycles'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::index
* @see app/Http/Controllers/Api/AssessmentCycleController.php:15
* @route '/api/assessment-cycles'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::index
* @see app/Http/Controllers/Api/AssessmentCycleController.php:15
* @route '/api/assessment-cycles'
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
* @see \App\Http\Controllers\Api\AssessmentCycleController::store
* @see app/Http/Controllers/Api/AssessmentCycleController.php:29
* @route '/api/assessment-cycles'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/assessment-cycles',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::store
* @see app/Http/Controllers/Api/AssessmentCycleController.php:29
* @route '/api/assessment-cycles'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::store
* @see app/Http/Controllers/Api/AssessmentCycleController.php:29
* @route '/api/assessment-cycles'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::store
* @see app/Http/Controllers/Api/AssessmentCycleController.php:29
* @route '/api/assessment-cycles'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::store
* @see app/Http/Controllers/Api/AssessmentCycleController.php:29
* @route '/api/assessment-cycles'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::show
* @see app/Http/Controllers/Api/AssessmentCycleController.php:69
* @route '/api/assessment-cycles/{assessment_cycle}'
*/
export const show = (args: { assessment_cycle: string | number } | [assessment_cycle: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/assessment-cycles/{assessment_cycle}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::show
* @see app/Http/Controllers/Api/AssessmentCycleController.php:69
* @route '/api/assessment-cycles/{assessment_cycle}'
*/
show.url = (args: { assessment_cycle: string | number } | [assessment_cycle: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { assessment_cycle: args }
    }

    if (Array.isArray(args)) {
        args = {
            assessment_cycle: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        assessment_cycle: args.assessment_cycle,
    }

    return show.definition.url
            .replace('{assessment_cycle}', parsedArgs.assessment_cycle.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::show
* @see app/Http/Controllers/Api/AssessmentCycleController.php:69
* @route '/api/assessment-cycles/{assessment_cycle}'
*/
show.get = (args: { assessment_cycle: string | number } | [assessment_cycle: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::show
* @see app/Http/Controllers/Api/AssessmentCycleController.php:69
* @route '/api/assessment-cycles/{assessment_cycle}'
*/
show.head = (args: { assessment_cycle: string | number } | [assessment_cycle: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::show
* @see app/Http/Controllers/Api/AssessmentCycleController.php:69
* @route '/api/assessment-cycles/{assessment_cycle}'
*/
const showForm = (args: { assessment_cycle: string | number } | [assessment_cycle: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::show
* @see app/Http/Controllers/Api/AssessmentCycleController.php:69
* @route '/api/assessment-cycles/{assessment_cycle}'
*/
showForm.get = (args: { assessment_cycle: string | number } | [assessment_cycle: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::show
* @see app/Http/Controllers/Api/AssessmentCycleController.php:69
* @route '/api/assessment-cycles/{assessment_cycle}'
*/
showForm.head = (args: { assessment_cycle: string | number } | [assessment_cycle: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Api\AssessmentCycleController::update
* @see app/Http/Controllers/Api/AssessmentCycleController.php:81
* @route '/api/assessment-cycles/{assessment_cycle}'
*/
export const update = (args: { assessment_cycle: string | number } | [assessment_cycle: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/api/assessment-cycles/{assessment_cycle}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::update
* @see app/Http/Controllers/Api/AssessmentCycleController.php:81
* @route '/api/assessment-cycles/{assessment_cycle}'
*/
update.url = (args: { assessment_cycle: string | number } | [assessment_cycle: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { assessment_cycle: args }
    }

    if (Array.isArray(args)) {
        args = {
            assessment_cycle: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        assessment_cycle: args.assessment_cycle,
    }

    return update.definition.url
            .replace('{assessment_cycle}', parsedArgs.assessment_cycle.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::update
* @see app/Http/Controllers/Api/AssessmentCycleController.php:81
* @route '/api/assessment-cycles/{assessment_cycle}'
*/
update.put = (args: { assessment_cycle: string | number } | [assessment_cycle: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::update
* @see app/Http/Controllers/Api/AssessmentCycleController.php:81
* @route '/api/assessment-cycles/{assessment_cycle}'
*/
update.patch = (args: { assessment_cycle: string | number } | [assessment_cycle: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::update
* @see app/Http/Controllers/Api/AssessmentCycleController.php:81
* @route '/api/assessment-cycles/{assessment_cycle}'
*/
const updateForm = (args: { assessment_cycle: string | number } | [assessment_cycle: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::update
* @see app/Http/Controllers/Api/AssessmentCycleController.php:81
* @route '/api/assessment-cycles/{assessment_cycle}'
*/
updateForm.put = (args: { assessment_cycle: string | number } | [assessment_cycle: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::update
* @see app/Http/Controllers/Api/AssessmentCycleController.php:81
* @route '/api/assessment-cycles/{assessment_cycle}'
*/
updateForm.patch = (args: { assessment_cycle: string | number } | [assessment_cycle: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Api\AssessmentCycleController::destroy
* @see app/Http/Controllers/Api/AssessmentCycleController.php:118
* @route '/api/assessment-cycles/{assessment_cycle}'
*/
export const destroy = (args: { assessment_cycle: string | number } | [assessment_cycle: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/assessment-cycles/{assessment_cycle}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::destroy
* @see app/Http/Controllers/Api/AssessmentCycleController.php:118
* @route '/api/assessment-cycles/{assessment_cycle}'
*/
destroy.url = (args: { assessment_cycle: string | number } | [assessment_cycle: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { assessment_cycle: args }
    }

    if (Array.isArray(args)) {
        args = {
            assessment_cycle: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        assessment_cycle: args.assessment_cycle,
    }

    return destroy.definition.url
            .replace('{assessment_cycle}', parsedArgs.assessment_cycle.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::destroy
* @see app/Http/Controllers/Api/AssessmentCycleController.php:118
* @route '/api/assessment-cycles/{assessment_cycle}'
*/
destroy.delete = (args: { assessment_cycle: string | number } | [assessment_cycle: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::destroy
* @see app/Http/Controllers/Api/AssessmentCycleController.php:118
* @route '/api/assessment-cycles/{assessment_cycle}'
*/
const destroyForm = (args: { assessment_cycle: string | number } | [assessment_cycle: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AssessmentCycleController::destroy
* @see app/Http/Controllers/Api/AssessmentCycleController.php:118
* @route '/api/assessment-cycles/{assessment_cycle}'
*/
destroyForm.delete = (args: { assessment_cycle: string | number } | [assessment_cycle: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const AssessmentCycleController = { index, store, show, update, destroy }

export default AssessmentCycleController