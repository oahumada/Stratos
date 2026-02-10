import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\JobOpeningController::index
* @see app/Http/Controllers/Api/JobOpeningController.php:12
* @route '/api/job-openings'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/job-openings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\JobOpeningController::index
* @see app/Http/Controllers/Api/JobOpeningController.php:12
* @route '/api/job-openings'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\JobOpeningController::index
* @see app/Http/Controllers/Api/JobOpeningController.php:12
* @route '/api/job-openings'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\JobOpeningController::index
* @see app/Http/Controllers/Api/JobOpeningController.php:12
* @route '/api/job-openings'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\JobOpeningController::index
* @see app/Http/Controllers/Api/JobOpeningController.php:12
* @route '/api/job-openings'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\JobOpeningController::index
* @see app/Http/Controllers/Api/JobOpeningController.php:12
* @route '/api/job-openings'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\JobOpeningController::index
* @see app/Http/Controllers/Api/JobOpeningController.php:12
* @route '/api/job-openings'
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
* @see \App\Http\Controllers\Api\JobOpeningController::show
* @see app/Http/Controllers/Api/JobOpeningController.php:29
* @route '/api/job-openings/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/job-openings/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\JobOpeningController::show
* @see app/Http/Controllers/Api/JobOpeningController.php:29
* @route '/api/job-openings/{id}'
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
* @see \App\Http\Controllers\Api\JobOpeningController::show
* @see app/Http/Controllers/Api/JobOpeningController.php:29
* @route '/api/job-openings/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\JobOpeningController::show
* @see app/Http/Controllers/Api/JobOpeningController.php:29
* @route '/api/job-openings/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\JobOpeningController::show
* @see app/Http/Controllers/Api/JobOpeningController.php:29
* @route '/api/job-openings/{id}'
*/
const showForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\JobOpeningController::show
* @see app/Http/Controllers/Api/JobOpeningController.php:29
* @route '/api/job-openings/{id}'
*/
showForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\JobOpeningController::show
* @see app/Http/Controllers/Api/JobOpeningController.php:29
* @route '/api/job-openings/{id}'
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
* @see \App\Http\Controllers\Api\JobOpeningController::candidates
* @see app/Http/Controllers/Api/JobOpeningController.php:51
* @route '/api/job-openings/{id}/candidates'
*/
export const candidates = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: candidates.url(args, options),
    method: 'get',
})

candidates.definition = {
    methods: ["get","head"],
    url: '/api/job-openings/{id}/candidates',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\JobOpeningController::candidates
* @see app/Http/Controllers/Api/JobOpeningController.php:51
* @route '/api/job-openings/{id}/candidates'
*/
candidates.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return candidates.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\JobOpeningController::candidates
* @see app/Http/Controllers/Api/JobOpeningController.php:51
* @route '/api/job-openings/{id}/candidates'
*/
candidates.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: candidates.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\JobOpeningController::candidates
* @see app/Http/Controllers/Api/JobOpeningController.php:51
* @route '/api/job-openings/{id}/candidates'
*/
candidates.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: candidates.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\JobOpeningController::candidates
* @see app/Http/Controllers/Api/JobOpeningController.php:51
* @route '/api/job-openings/{id}/candidates'
*/
const candidatesForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: candidates.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\JobOpeningController::candidates
* @see app/Http/Controllers/Api/JobOpeningController.php:51
* @route '/api/job-openings/{id}/candidates'
*/
candidatesForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: candidates.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\JobOpeningController::candidates
* @see app/Http/Controllers/Api/JobOpeningController.php:51
* @route '/api/job-openings/{id}/candidates'
*/
candidatesForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: candidates.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

candidates.form = candidatesForm

const JobOpeningController = { index, show, candidates }

export default JobOpeningController