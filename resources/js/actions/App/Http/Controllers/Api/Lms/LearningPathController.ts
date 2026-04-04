import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::generate
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:147
* @route '/api/lms/learning-paths/generate'
*/
export const generate = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(options),
    method: 'post',
})

generate.definition = {
    methods: ["post"],
    url: '/api/lms/learning-paths/generate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::generate
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:147
* @route '/api/lms/learning-paths/generate'
*/
generate.url = (options?: RouteQueryOptions) => {
    return generate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::generate
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:147
* @route '/api/lms/learning-paths/generate'
*/
generate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::generate
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:147
* @route '/api/lms/learning-paths/generate'
*/
const generateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::generate
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:147
* @route '/api/lms/learning-paths/generate'
*/
generateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generate.url(options),
    method: 'post',
})

generate.form = generateForm

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::index
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:17
* @route '/api/lms/learning-paths'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/lms/learning-paths',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::index
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:17
* @route '/api/lms/learning-paths'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::index
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:17
* @route '/api/lms/learning-paths'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::index
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:17
* @route '/api/lms/learning-paths'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::index
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:17
* @route '/api/lms/learning-paths'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::index
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:17
* @route '/api/lms/learning-paths'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::index
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:17
* @route '/api/lms/learning-paths'
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
* @see \App\Http\Controllers\Api\Lms\LearningPathController::store
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:29
* @route '/api/lms/learning-paths'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/lms/learning-paths',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::store
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:29
* @route '/api/lms/learning-paths'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::store
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:29
* @route '/api/lms/learning-paths'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::store
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:29
* @route '/api/lms/learning-paths'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::store
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:29
* @route '/api/lms/learning-paths'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::show
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:51
* @route '/api/lms/learning-paths/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/lms/learning-paths/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::show
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:51
* @route '/api/lms/learning-paths/{id}'
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
* @see \App\Http\Controllers\Api\Lms\LearningPathController::show
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:51
* @route '/api/lms/learning-paths/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::show
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:51
* @route '/api/lms/learning-paths/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::show
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:51
* @route '/api/lms/learning-paths/{id}'
*/
const showForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::show
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:51
* @route '/api/lms/learning-paths/{id}'
*/
showForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::show
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:51
* @route '/api/lms/learning-paths/{id}'
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
* @see \App\Http\Controllers\Api\Lms\LearningPathController::update
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:62
* @route '/api/lms/learning-paths/{id}'
*/
export const update = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/lms/learning-paths/{id}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::update
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:62
* @route '/api/lms/learning-paths/{id}'
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
* @see \App\Http\Controllers\Api\Lms\LearningPathController::update
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:62
* @route '/api/lms/learning-paths/{id}'
*/
update.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::update
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:62
* @route '/api/lms/learning-paths/{id}'
*/
const updateForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::update
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:62
* @route '/api/lms/learning-paths/{id}'
*/
updateForm.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Api\Lms\LearningPathController::destroy
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:85
* @route '/api/lms/learning-paths/{id}'
*/
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/lms/learning-paths/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::destroy
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:85
* @route '/api/lms/learning-paths/{id}'
*/
destroy.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::destroy
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:85
* @route '/api/lms/learning-paths/{id}'
*/
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::destroy
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:85
* @route '/api/lms/learning-paths/{id}'
*/
const destroyForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::destroy
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:85
* @route '/api/lms/learning-paths/{id}'
*/
destroyForm.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::enroll
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:102
* @route '/api/lms/learning-paths/{id}/enroll'
*/
export const enroll = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: enroll.url(args, options),
    method: 'post',
})

enroll.definition = {
    methods: ["post"],
    url: '/api/lms/learning-paths/{id}/enroll',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::enroll
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:102
* @route '/api/lms/learning-paths/{id}/enroll'
*/
enroll.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return enroll.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::enroll
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:102
* @route '/api/lms/learning-paths/{id}/enroll'
*/
enroll.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: enroll.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::enroll
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:102
* @route '/api/lms/learning-paths/{id}/enroll'
*/
const enrollForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: enroll.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::enroll
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:102
* @route '/api/lms/learning-paths/{id}/enroll'
*/
enrollForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: enroll.url(args, options),
    method: 'post',
})

enroll.form = enrollForm

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::progress
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:114
* @route '/api/lms/learning-paths/{id}/progress'
*/
export const progress = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: progress.url(args, options),
    method: 'get',
})

progress.definition = {
    methods: ["get","head"],
    url: '/api/lms/learning-paths/{id}/progress',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::progress
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:114
* @route '/api/lms/learning-paths/{id}/progress'
*/
progress.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return progress.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::progress
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:114
* @route '/api/lms/learning-paths/{id}/progress'
*/
progress.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: progress.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::progress
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:114
* @route '/api/lms/learning-paths/{id}/progress'
*/
progress.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: progress.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::progress
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:114
* @route '/api/lms/learning-paths/{id}/progress'
*/
const progressForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: progress.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::progress
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:114
* @route '/api/lms/learning-paths/{id}/progress'
*/
progressForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: progress.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::progress
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:114
* @route '/api/lms/learning-paths/{id}/progress'
*/
progressForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: progress.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

progress.form = progressForm

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::recalculate
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:134
* @route '/api/lms/learning-paths/{id}/recalculate'
*/
export const recalculate = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: recalculate.url(args, options),
    method: 'post',
})

recalculate.definition = {
    methods: ["post"],
    url: '/api/lms/learning-paths/{id}/recalculate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::recalculate
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:134
* @route '/api/lms/learning-paths/{id}/recalculate'
*/
recalculate.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return recalculate.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::recalculate
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:134
* @route '/api/lms/learning-paths/{id}/recalculate'
*/
recalculate.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: recalculate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::recalculate
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:134
* @route '/api/lms/learning-paths/{id}/recalculate'
*/
const recalculateForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: recalculate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\LearningPathController::recalculate
* @see app/Http/Controllers/Api/Lms/LearningPathController.php:134
* @route '/api/lms/learning-paths/{id}/recalculate'
*/
recalculateForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: recalculate.url(args, options),
    method: 'post',
})

recalculate.form = recalculateForm

const LearningPathController = { generate, index, store, show, update, destroy, enroll, progress, recalculate }

export default LearningPathController