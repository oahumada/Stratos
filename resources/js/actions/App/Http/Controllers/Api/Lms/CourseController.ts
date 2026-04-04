import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\CourseController::show
* @see app/Http/Controllers/Api/Lms/CourseController.php:18
* @route '/api/lms/courses/{course}'
*/
export const show = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/lms/courses/{course}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\CourseController::show
* @see app/Http/Controllers/Api/Lms/CourseController.php:18
* @route '/api/lms/courses/{course}'
*/
show.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { course: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { course: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            course: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        course: typeof args.course === 'object'
        ? args.course.id
        : args.course,
    }

    return show.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CourseController::show
* @see app/Http/Controllers/Api/Lms/CourseController.php:18
* @route '/api/lms/courses/{course}'
*/
show.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CourseController::show
* @see app/Http/Controllers/Api/Lms/CourseController.php:18
* @route '/api/lms/courses/{course}'
*/
show.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\CourseController::show
* @see app/Http/Controllers/Api/Lms/CourseController.php:18
* @route '/api/lms/courses/{course}'
*/
const showForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CourseController::show
* @see app/Http/Controllers/Api/Lms/CourseController.php:18
* @route '/api/lms/courses/{course}'
*/
showForm.get = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CourseController::show
* @see app/Http/Controllers/Api/Lms/CourseController.php:18
* @route '/api/lms/courses/{course}'
*/
showForm.head = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Api\Lms\CourseController::update
* @see app/Http/Controllers/Api/Lms/CourseController.php:63
* @route '/api/lms/courses/{course}'
*/
export const update = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/api/lms/courses/{course}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\Lms\CourseController::update
* @see app/Http/Controllers/Api/Lms/CourseController.php:63
* @route '/api/lms/courses/{course}'
*/
update.url = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { course: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { course: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            course: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        course: typeof args.course === 'object'
        ? args.course.id
        : args.course,
    }

    return update.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CourseController::update
* @see app/Http/Controllers/Api/Lms/CourseController.php:63
* @route '/api/lms/courses/{course}'
*/
update.patch = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\Lms\CourseController::update
* @see app/Http/Controllers/Api/Lms/CourseController.php:63
* @route '/api/lms/courses/{course}'
*/
const updateForm = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CourseController::update
* @see app/Http/Controllers/Api/Lms/CourseController.php:63
* @route '/api/lms/courses/{course}'
*/
updateForm.patch = (args: { course: number | { id: number } } | [course: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Api\Lms\CourseController::templates
* @see app/Http/Controllers/Api/Lms/CourseController.php:32
* @route '/api/lms/certificate-templates'
*/
export const templates = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: templates.url(options),
    method: 'get',
})

templates.definition = {
    methods: ["get","head"],
    url: '/api/lms/certificate-templates',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\CourseController::templates
* @see app/Http/Controllers/Api/Lms/CourseController.php:32
* @route '/api/lms/certificate-templates'
*/
templates.url = (options?: RouteQueryOptions) => {
    return templates.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CourseController::templates
* @see app/Http/Controllers/Api/Lms/CourseController.php:32
* @route '/api/lms/certificate-templates'
*/
templates.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: templates.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CourseController::templates
* @see app/Http/Controllers/Api/Lms/CourseController.php:32
* @route '/api/lms/certificate-templates'
*/
templates.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: templates.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\CourseController::templates
* @see app/Http/Controllers/Api/Lms/CourseController.php:32
* @route '/api/lms/certificate-templates'
*/
const templatesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: templates.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CourseController::templates
* @see app/Http/Controllers/Api/Lms/CourseController.php:32
* @route '/api/lms/certificate-templates'
*/
templatesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: templates.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CourseController::templates
* @see app/Http/Controllers/Api/Lms/CourseController.php:32
* @route '/api/lms/certificate-templates'
*/
templatesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: templates.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

templates.form = templatesForm

const CourseController = { show, update, templates }

export default CourseController