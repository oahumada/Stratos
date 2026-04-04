import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\SurveyController::show
* @see app/Http/Controllers/Api/Lms/SurveyController.php:18
* @route '/api/lms/courses/{course}/survey'
*/
export const show = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/lms/courses/{course}/survey',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\SurveyController::show
* @see app/Http/Controllers/Api/Lms/SurveyController.php:18
* @route '/api/lms/courses/{course}/survey'
*/
show.url = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { course: args }
    }

    if (Array.isArray(args)) {
        args = {
            course: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        course: args.course,
    }

    return show.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\SurveyController::show
* @see app/Http/Controllers/Api/Lms/SurveyController.php:18
* @route '/api/lms/courses/{course}/survey'
*/
show.get = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\SurveyController::show
* @see app/Http/Controllers/Api/Lms/SurveyController.php:18
* @route '/api/lms/courses/{course}/survey'
*/
show.head = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\SurveyController::show
* @see app/Http/Controllers/Api/Lms/SurveyController.php:18
* @route '/api/lms/courses/{course}/survey'
*/
const showForm = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\SurveyController::show
* @see app/Http/Controllers/Api/Lms/SurveyController.php:18
* @route '/api/lms/courses/{course}/survey'
*/
showForm.get = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\SurveyController::show
* @see app/Http/Controllers/Api/Lms/SurveyController.php:18
* @route '/api/lms/courses/{course}/survey'
*/
showForm.head = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Api\Lms\SurveyController::store
* @see app/Http/Controllers/Api/Lms/SurveyController.php:29
* @route '/api/lms/courses/{course}/survey'
*/
export const store = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/lms/courses/{course}/survey',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\SurveyController::store
* @see app/Http/Controllers/Api/Lms/SurveyController.php:29
* @route '/api/lms/courses/{course}/survey'
*/
store.url = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { course: args }
    }

    if (Array.isArray(args)) {
        args = {
            course: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        course: args.course,
    }

    return store.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\SurveyController::store
* @see app/Http/Controllers/Api/Lms/SurveyController.php:29
* @route '/api/lms/courses/{course}/survey'
*/
store.post = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\SurveyController::store
* @see app/Http/Controllers/Api/Lms/SurveyController.php:29
* @route '/api/lms/courses/{course}/survey'
*/
const storeForm = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\SurveyController::store
* @see app/Http/Controllers/Api/Lms/SurveyController.php:29
* @route '/api/lms/courses/{course}/survey'
*/
storeForm.post = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\Lms\SurveyController::submit
* @see app/Http/Controllers/Api/Lms/SurveyController.php:53
* @route '/api/lms/surveys/{survey}/submit'
*/
export const submit = (args: { survey: string | number } | [survey: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(args, options),
    method: 'post',
})

submit.definition = {
    methods: ["post"],
    url: '/api/lms/surveys/{survey}/submit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\SurveyController::submit
* @see app/Http/Controllers/Api/Lms/SurveyController.php:53
* @route '/api/lms/surveys/{survey}/submit'
*/
submit.url = (args: { survey: string | number } | [survey: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { survey: args }
    }

    if (Array.isArray(args)) {
        args = {
            survey: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        survey: args.survey,
    }

    return submit.definition.url
            .replace('{survey}', parsedArgs.survey.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\SurveyController::submit
* @see app/Http/Controllers/Api/Lms/SurveyController.php:53
* @route '/api/lms/surveys/{survey}/submit'
*/
submit.post = (args: { survey: string | number } | [survey: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\SurveyController::submit
* @see app/Http/Controllers/Api/Lms/SurveyController.php:53
* @route '/api/lms/surveys/{survey}/submit'
*/
const submitForm = (args: { survey: string | number } | [survey: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: submit.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\SurveyController::submit
* @see app/Http/Controllers/Api/Lms/SurveyController.php:53
* @route '/api/lms/surveys/{survey}/submit'
*/
submitForm.post = (args: { survey: string | number } | [survey: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: submit.url(args, options),
    method: 'post',
})

submit.form = submitForm

/**
* @see \App\Http\Controllers\Api\Lms\SurveyController::results
* @see app/Http/Controllers/Api/Lms/SurveyController.php:78
* @route '/api/lms/surveys/{survey}/results'
*/
export const results = (args: { survey: string | number } | [survey: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: results.url(args, options),
    method: 'get',
})

results.definition = {
    methods: ["get","head"],
    url: '/api/lms/surveys/{survey}/results',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\SurveyController::results
* @see app/Http/Controllers/Api/Lms/SurveyController.php:78
* @route '/api/lms/surveys/{survey}/results'
*/
results.url = (args: { survey: string | number } | [survey: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { survey: args }
    }

    if (Array.isArray(args)) {
        args = {
            survey: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        survey: args.survey,
    }

    return results.definition.url
            .replace('{survey}', parsedArgs.survey.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\SurveyController::results
* @see app/Http/Controllers/Api/Lms/SurveyController.php:78
* @route '/api/lms/surveys/{survey}/results'
*/
results.get = (args: { survey: string | number } | [survey: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: results.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\SurveyController::results
* @see app/Http/Controllers/Api/Lms/SurveyController.php:78
* @route '/api/lms/surveys/{survey}/results'
*/
results.head = (args: { survey: string | number } | [survey: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: results.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\SurveyController::results
* @see app/Http/Controllers/Api/Lms/SurveyController.php:78
* @route '/api/lms/surveys/{survey}/results'
*/
const resultsForm = (args: { survey: string | number } | [survey: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: results.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\SurveyController::results
* @see app/Http/Controllers/Api/Lms/SurveyController.php:78
* @route '/api/lms/surveys/{survey}/results'
*/
resultsForm.get = (args: { survey: string | number } | [survey: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: results.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\SurveyController::results
* @see app/Http/Controllers/Api/Lms/SurveyController.php:78
* @route '/api/lms/surveys/{survey}/results'
*/
resultsForm.head = (args: { survey: string | number } | [survey: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: results.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

results.form = resultsForm

const SurveyController = { show, store, submit, results }

export default SurveyController