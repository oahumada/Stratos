import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\MicrolearningController::show
* @see app/Http/Controllers/Api/Lms/MicrolearningController.php:17
* @route '/api/lms/lessons/{lesson}/micro'
*/
export const show = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/lms/lessons/{lesson}/micro',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\MicrolearningController::show
* @see app/Http/Controllers/Api/Lms/MicrolearningController.php:17
* @route '/api/lms/lessons/{lesson}/micro'
*/
show.url = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { lesson: args }
    }

    if (Array.isArray(args)) {
        args = {
            lesson: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        lesson: args.lesson,
    }

    return show.definition.url
            .replace('{lesson}', parsedArgs.lesson.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\MicrolearningController::show
* @see app/Http/Controllers/Api/Lms/MicrolearningController.php:17
* @route '/api/lms/lessons/{lesson}/micro'
*/
show.get = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\MicrolearningController::show
* @see app/Http/Controllers/Api/Lms/MicrolearningController.php:17
* @route '/api/lms/lessons/{lesson}/micro'
*/
show.head = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\MicrolearningController::show
* @see app/Http/Controllers/Api/Lms/MicrolearningController.php:17
* @route '/api/lms/lessons/{lesson}/micro'
*/
const showForm = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\MicrolearningController::show
* @see app/Http/Controllers/Api/Lms/MicrolearningController.php:17
* @route '/api/lms/lessons/{lesson}/micro'
*/
showForm.get = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\MicrolearningController::show
* @see app/Http/Controllers/Api/Lms/MicrolearningController.php:17
* @route '/api/lms/lessons/{lesson}/micro'
*/
showForm.head = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Api\Lms\MicrolearningController::store
* @see app/Http/Controllers/Api/Lms/MicrolearningController.php:33
* @route '/api/lms/lessons/{lesson}/micro'
*/
export const store = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/lms/lessons/{lesson}/micro',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\MicrolearningController::store
* @see app/Http/Controllers/Api/Lms/MicrolearningController.php:33
* @route '/api/lms/lessons/{lesson}/micro'
*/
store.url = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { lesson: args }
    }

    if (Array.isArray(args)) {
        args = {
            lesson: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        lesson: args.lesson,
    }

    return store.definition.url
            .replace('{lesson}', parsedArgs.lesson.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\MicrolearningController::store
* @see app/Http/Controllers/Api/Lms/MicrolearningController.php:33
* @route '/api/lms/lessons/{lesson}/micro'
*/
store.post = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\MicrolearningController::store
* @see app/Http/Controllers/Api/Lms/MicrolearningController.php:33
* @route '/api/lms/lessons/{lesson}/micro'
*/
const storeForm = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\MicrolearningController::store
* @see app/Http/Controllers/Api/Lms/MicrolearningController.php:33
* @route '/api/lms/lessons/{lesson}/micro'
*/
storeForm.post = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\Lms\MicrolearningController::generate
* @see app/Http/Controllers/Api/Lms/MicrolearningController.php:67
* @route '/api/lms/lessons/{lesson}/micro/generate'
*/
export const generate = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(args, options),
    method: 'post',
})

generate.definition = {
    methods: ["post"],
    url: '/api/lms/lessons/{lesson}/micro/generate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\MicrolearningController::generate
* @see app/Http/Controllers/Api/Lms/MicrolearningController.php:67
* @route '/api/lms/lessons/{lesson}/micro/generate'
*/
generate.url = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { lesson: args }
    }

    if (Array.isArray(args)) {
        args = {
            lesson: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        lesson: args.lesson,
    }

    return generate.definition.url
            .replace('{lesson}', parsedArgs.lesson.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\MicrolearningController::generate
* @see app/Http/Controllers/Api/Lms/MicrolearningController.php:67
* @route '/api/lms/lessons/{lesson}/micro/generate'
*/
generate.post = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\MicrolearningController::generate
* @see app/Http/Controllers/Api/Lms/MicrolearningController.php:67
* @route '/api/lms/lessons/{lesson}/micro/generate'
*/
const generateForm = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\MicrolearningController::generate
* @see app/Http/Controllers/Api/Lms/MicrolearningController.php:67
* @route '/api/lms/lessons/{lesson}/micro/generate'
*/
generateForm.post = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generate.url(args, options),
    method: 'post',
})

generate.form = generateForm

const MicrolearningController = { show, store, generate }

export default MicrolearningController