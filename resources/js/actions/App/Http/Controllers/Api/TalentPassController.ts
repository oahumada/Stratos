import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\TalentPassController::show
* @see app/Http/Controllers/Api/TalentPassController.php:21
* @route '/api/people/{people_id}/talent-pass'
*/
export const show = (args: { people_id: string | number } | [people_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/people/{people_id}/talent-pass',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\TalentPassController::show
* @see app/Http/Controllers/Api/TalentPassController.php:21
* @route '/api/people/{people_id}/talent-pass'
*/
show.url = (args: { people_id: string | number } | [people_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { people_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            people_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        people_id: args.people_id,
    }

    return show.definition.url
            .replace('{people_id}', parsedArgs.people_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TalentPassController::show
* @see app/Http/Controllers/Api/TalentPassController.php:21
* @route '/api/people/{people_id}/talent-pass'
*/
show.get = (args: { people_id: string | number } | [people_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentPassController::show
* @see app/Http/Controllers/Api/TalentPassController.php:21
* @route '/api/people/{people_id}/talent-pass'
*/
show.head = (args: { people_id: string | number } | [people_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\TalentPassController::show
* @see app/Http/Controllers/Api/TalentPassController.php:21
* @route '/api/people/{people_id}/talent-pass'
*/
const showForm = (args: { people_id: string | number } | [people_id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentPassController::show
* @see app/Http/Controllers/Api/TalentPassController.php:21
* @route '/api/people/{people_id}/talent-pass'
*/
showForm.get = (args: { people_id: string | number } | [people_id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentPassController::show
* @see app/Http/Controllers/Api/TalentPassController.php:21
* @route '/api/people/{people_id}/talent-pass'
*/
showForm.head = (args: { people_id: string | number } | [people_id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Api\TalentPassController::generateCredential
* @see app/Http/Controllers/Api/TalentPassController.php:74
* @route '/api/people/{people_id}/talent-pass/issue'
*/
export const generateCredential = (args: { people_id: string | number } | [people_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateCredential.url(args, options),
    method: 'post',
})

generateCredential.definition = {
    methods: ["post"],
    url: '/api/people/{people_id}/talent-pass/issue',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\TalentPassController::generateCredential
* @see app/Http/Controllers/Api/TalentPassController.php:74
* @route '/api/people/{people_id}/talent-pass/issue'
*/
generateCredential.url = (args: { people_id: string | number } | [people_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { people_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            people_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        people_id: args.people_id,
    }

    return generateCredential.definition.url
            .replace('{people_id}', parsedArgs.people_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TalentPassController::generateCredential
* @see app/Http/Controllers/Api/TalentPassController.php:74
* @route '/api/people/{people_id}/talent-pass/issue'
*/
generateCredential.post = (args: { people_id: string | number } | [people_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateCredential.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\TalentPassController::generateCredential
* @see app/Http/Controllers/Api/TalentPassController.php:74
* @route '/api/people/{people_id}/talent-pass/issue'
*/
const generateCredentialForm = (args: { people_id: string | number } | [people_id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generateCredential.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\TalentPassController::generateCredential
* @see app/Http/Controllers/Api/TalentPassController.php:74
* @route '/api/people/{people_id}/talent-pass/issue'
*/
generateCredentialForm.post = (args: { people_id: string | number } | [people_id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generateCredential.url(args, options),
    method: 'post',
})

generateCredential.form = generateCredentialForm

const TalentPassController = { show, generateCredential }

export default TalentPassController