import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\QuizController::index
* @see app/Http/Controllers/Api/Lms/QuizController.php:18
* @route '/api/lms/quizzes'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/lms/quizzes',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::index
* @see app/Http/Controllers/Api/Lms/QuizController.php:18
* @route '/api/lms/quizzes'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::index
* @see app/Http/Controllers/Api/Lms/QuizController.php:18
* @route '/api/lms/quizzes'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::index
* @see app/Http/Controllers/Api/Lms/QuizController.php:18
* @route '/api/lms/quizzes'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::index
* @see app/Http/Controllers/Api/Lms/QuizController.php:18
* @route '/api/lms/quizzes'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::index
* @see app/Http/Controllers/Api/Lms/QuizController.php:18
* @route '/api/lms/quizzes'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::index
* @see app/Http/Controllers/Api/Lms/QuizController.php:18
* @route '/api/lms/quizzes'
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
* @see \App\Http\Controllers\Api\Lms\QuizController::store
* @see app/Http/Controllers/Api/Lms/QuizController.php:29
* @route '/api/lms/quizzes'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/lms/quizzes',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::store
* @see app/Http/Controllers/Api/Lms/QuizController.php:29
* @route '/api/lms/quizzes'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::store
* @see app/Http/Controllers/Api/Lms/QuizController.php:29
* @route '/api/lms/quizzes'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::store
* @see app/Http/Controllers/Api/Lms/QuizController.php:29
* @route '/api/lms/quizzes'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::store
* @see app/Http/Controllers/Api/Lms/QuizController.php:29
* @route '/api/lms/quizzes'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::show
* @see app/Http/Controllers/Api/Lms/QuizController.php:56
* @route '/api/lms/quizzes/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/lms/quizzes/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::show
* @see app/Http/Controllers/Api/Lms/QuizController.php:56
* @route '/api/lms/quizzes/{id}'
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
* @see \App\Http\Controllers\Api\Lms\QuizController::show
* @see app/Http/Controllers/Api/Lms/QuizController.php:56
* @route '/api/lms/quizzes/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::show
* @see app/Http/Controllers/Api/Lms/QuizController.php:56
* @route '/api/lms/quizzes/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::show
* @see app/Http/Controllers/Api/Lms/QuizController.php:56
* @route '/api/lms/quizzes/{id}'
*/
const showForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::show
* @see app/Http/Controllers/Api/Lms/QuizController.php:56
* @route '/api/lms/quizzes/{id}'
*/
showForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::show
* @see app/Http/Controllers/Api/Lms/QuizController.php:56
* @route '/api/lms/quizzes/{id}'
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
* @see \App\Http\Controllers\Api\Lms\QuizController::update
* @see app/Http/Controllers/Api/Lms/QuizController.php:68
* @route '/api/lms/quizzes/{id}'
*/
export const update = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/lms/quizzes/{id}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::update
* @see app/Http/Controllers/Api/Lms/QuizController.php:68
* @route '/api/lms/quizzes/{id}'
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
* @see \App\Http\Controllers\Api\Lms\QuizController::update
* @see app/Http/Controllers/Api/Lms/QuizController.php:68
* @route '/api/lms/quizzes/{id}'
*/
update.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::update
* @see app/Http/Controllers/Api/Lms/QuizController.php:68
* @route '/api/lms/quizzes/{id}'
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
* @see \App\Http\Controllers\Api\Lms\QuizController::update
* @see app/Http/Controllers/Api/Lms/QuizController.php:68
* @route '/api/lms/quizzes/{id}'
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
* @see \App\Http\Controllers\Api\Lms\QuizController::destroy
* @see app/Http/Controllers/Api/Lms/QuizController.php:94
* @route '/api/lms/quizzes/{id}'
*/
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/lms/quizzes/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::destroy
* @see app/Http/Controllers/Api/Lms/QuizController.php:94
* @route '/api/lms/quizzes/{id}'
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
* @see \App\Http\Controllers\Api\Lms\QuizController::destroy
* @see app/Http/Controllers/Api/Lms/QuizController.php:94
* @route '/api/lms/quizzes/{id}'
*/
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::destroy
* @see app/Http/Controllers/Api/Lms/QuizController.php:94
* @route '/api/lms/quizzes/{id}'
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
* @see \App\Http\Controllers\Api\Lms\QuizController::destroy
* @see app/Http/Controllers/Api/Lms/QuizController.php:94
* @route '/api/lms/quizzes/{id}'
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
* @see \App\Http\Controllers\Api\Lms\QuizController::startAttempt
* @see app/Http/Controllers/Api/Lms/QuizController.php:107
* @route '/api/lms/quizzes/{id}/start'
*/
export const startAttempt = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: startAttempt.url(args, options),
    method: 'post',
})

startAttempt.definition = {
    methods: ["post"],
    url: '/api/lms/quizzes/{id}/start',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::startAttempt
* @see app/Http/Controllers/Api/Lms/QuizController.php:107
* @route '/api/lms/quizzes/{id}/start'
*/
startAttempt.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return startAttempt.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::startAttempt
* @see app/Http/Controllers/Api/Lms/QuizController.php:107
* @route '/api/lms/quizzes/{id}/start'
*/
startAttempt.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: startAttempt.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::startAttempt
* @see app/Http/Controllers/Api/Lms/QuizController.php:107
* @route '/api/lms/quizzes/{id}/start'
*/
const startAttemptForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: startAttempt.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::startAttempt
* @see app/Http/Controllers/Api/Lms/QuizController.php:107
* @route '/api/lms/quizzes/{id}/start'
*/
startAttemptForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: startAttempt.url(args, options),
    method: 'post',
})

startAttempt.form = startAttemptForm

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::submitAttempt
* @see app/Http/Controllers/Api/Lms/QuizController.php:117
* @route '/api/lms/quizzes/{id}/submit'
*/
export const submitAttempt = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submitAttempt.url(args, options),
    method: 'post',
})

submitAttempt.definition = {
    methods: ["post"],
    url: '/api/lms/quizzes/{id}/submit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::submitAttempt
* @see app/Http/Controllers/Api/Lms/QuizController.php:117
* @route '/api/lms/quizzes/{id}/submit'
*/
submitAttempt.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return submitAttempt.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::submitAttempt
* @see app/Http/Controllers/Api/Lms/QuizController.php:117
* @route '/api/lms/quizzes/{id}/submit'
*/
submitAttempt.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submitAttempt.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::submitAttempt
* @see app/Http/Controllers/Api/Lms/QuizController.php:117
* @route '/api/lms/quizzes/{id}/submit'
*/
const submitAttemptForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: submitAttempt.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::submitAttempt
* @see app/Http/Controllers/Api/Lms/QuizController.php:117
* @route '/api/lms/quizzes/{id}/submit'
*/
submitAttemptForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: submitAttempt.url(args, options),
    method: 'post',
})

submitAttempt.form = submitAttemptForm

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::attempts
* @see app/Http/Controllers/Api/Lms/QuizController.php:136
* @route '/api/lms/quizzes/{id}/attempts'
*/
export const attempts = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: attempts.url(args, options),
    method: 'get',
})

attempts.definition = {
    methods: ["get","head"],
    url: '/api/lms/quizzes/{id}/attempts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::attempts
* @see app/Http/Controllers/Api/Lms/QuizController.php:136
* @route '/api/lms/quizzes/{id}/attempts'
*/
attempts.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return attempts.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::attempts
* @see app/Http/Controllers/Api/Lms/QuizController.php:136
* @route '/api/lms/quizzes/{id}/attempts'
*/
attempts.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: attempts.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::attempts
* @see app/Http/Controllers/Api/Lms/QuizController.php:136
* @route '/api/lms/quizzes/{id}/attempts'
*/
attempts.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: attempts.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::attempts
* @see app/Http/Controllers/Api/Lms/QuizController.php:136
* @route '/api/lms/quizzes/{id}/attempts'
*/
const attemptsForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: attempts.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::attempts
* @see app/Http/Controllers/Api/Lms/QuizController.php:136
* @route '/api/lms/quizzes/{id}/attempts'
*/
attemptsForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: attempts.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::attempts
* @see app/Http/Controllers/Api/Lms/QuizController.php:136
* @route '/api/lms/quizzes/{id}/attempts'
*/
attemptsForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: attempts.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

attempts.form = attemptsForm

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::stats
* @see app/Http/Controllers/Api/Lms/QuizController.php:150
* @route '/api/lms/quizzes/{id}/stats'
*/
export const stats = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: stats.url(args, options),
    method: 'get',
})

stats.definition = {
    methods: ["get","head"],
    url: '/api/lms/quizzes/{id}/stats',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::stats
* @see app/Http/Controllers/Api/Lms/QuizController.php:150
* @route '/api/lms/quizzes/{id}/stats'
*/
stats.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return stats.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::stats
* @see app/Http/Controllers/Api/Lms/QuizController.php:150
* @route '/api/lms/quizzes/{id}/stats'
*/
stats.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: stats.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::stats
* @see app/Http/Controllers/Api/Lms/QuizController.php:150
* @route '/api/lms/quizzes/{id}/stats'
*/
stats.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: stats.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::stats
* @see app/Http/Controllers/Api/Lms/QuizController.php:150
* @route '/api/lms/quizzes/{id}/stats'
*/
const statsForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: stats.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::stats
* @see app/Http/Controllers/Api/Lms/QuizController.php:150
* @route '/api/lms/quizzes/{id}/stats'
*/
statsForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: stats.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::stats
* @see app/Http/Controllers/Api/Lms/QuizController.php:150
* @route '/api/lms/quizzes/{id}/stats'
*/
statsForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: stats.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

stats.form = statsForm

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::generateQuestions
* @see app/Http/Controllers/Api/Lms/QuizController.php:158
* @route '/api/lms/quizzes/{id}/generate-questions'
*/
export const generateQuestions = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateQuestions.url(args, options),
    method: 'post',
})

generateQuestions.definition = {
    methods: ["post"],
    url: '/api/lms/quizzes/{id}/generate-questions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::generateQuestions
* @see app/Http/Controllers/Api/Lms/QuizController.php:158
* @route '/api/lms/quizzes/{id}/generate-questions'
*/
generateQuestions.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return generateQuestions.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::generateQuestions
* @see app/Http/Controllers/Api/Lms/QuizController.php:158
* @route '/api/lms/quizzes/{id}/generate-questions'
*/
generateQuestions.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateQuestions.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::generateQuestions
* @see app/Http/Controllers/Api/Lms/QuizController.php:158
* @route '/api/lms/quizzes/{id}/generate-questions'
*/
const generateQuestionsForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generateQuestions.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\QuizController::generateQuestions
* @see app/Http/Controllers/Api/Lms/QuizController.php:158
* @route '/api/lms/quizzes/{id}/generate-questions'
*/
generateQuestionsForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generateQuestions.url(args, options),
    method: 'post',
})

generateQuestions.form = generateQuestionsForm

const QuizController = { index, store, show, update, destroy, startAttempt, submitAttempt, attempts, stats, generateQuestions }

export default QuizController