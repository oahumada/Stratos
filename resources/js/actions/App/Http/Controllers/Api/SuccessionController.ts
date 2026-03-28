import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\SuccessionController::index
* @see app/Http/Controllers/Api/SuccessionController.php:70
* @route '/api/talent/succession/plans'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/talent/succession/plans',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\SuccessionController::index
* @see app/Http/Controllers/Api/SuccessionController.php:70
* @route '/api/talent/succession/plans'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SuccessionController::index
* @see app/Http/Controllers/Api/SuccessionController.php:70
* @route '/api/talent/succession/plans'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SuccessionController::index
* @see app/Http/Controllers/Api/SuccessionController.php:70
* @route '/api/talent/succession/plans'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\SuccessionController::index
* @see app/Http/Controllers/Api/SuccessionController.php:70
* @route '/api/talent/succession/plans'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SuccessionController::index
* @see app/Http/Controllers/Api/SuccessionController.php:70
* @route '/api/talent/succession/plans'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SuccessionController::index
* @see app/Http/Controllers/Api/SuccessionController.php:70
* @route '/api/talent/succession/plans'
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
* @see \App\Http\Controllers\Api\SuccessionController::getSuccessors
* @see app/Http/Controllers/Api/SuccessionController.php:21
* @route '/api/talent/succession/role/{roleId}'
*/
export const getSuccessors = (args: { roleId: string | number } | [roleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSuccessors.url(args, options),
    method: 'get',
})

getSuccessors.definition = {
    methods: ["get","head"],
    url: '/api/talent/succession/role/{roleId}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\SuccessionController::getSuccessors
* @see app/Http/Controllers/Api/SuccessionController.php:21
* @route '/api/talent/succession/role/{roleId}'
*/
getSuccessors.url = (args: { roleId: string | number } | [roleId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { roleId: args }
    }

    if (Array.isArray(args)) {
        args = {
            roleId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        roleId: args.roleId,
    }

    return getSuccessors.definition.url
            .replace('{roleId}', parsedArgs.roleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SuccessionController::getSuccessors
* @see app/Http/Controllers/Api/SuccessionController.php:21
* @route '/api/talent/succession/role/{roleId}'
*/
getSuccessors.get = (args: { roleId: string | number } | [roleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSuccessors.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SuccessionController::getSuccessors
* @see app/Http/Controllers/Api/SuccessionController.php:21
* @route '/api/talent/succession/role/{roleId}'
*/
getSuccessors.head = (args: { roleId: string | number } | [roleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getSuccessors.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\SuccessionController::getSuccessors
* @see app/Http/Controllers/Api/SuccessionController.php:21
* @route '/api/talent/succession/role/{roleId}'
*/
const getSuccessorsForm = (args: { roleId: string | number } | [roleId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getSuccessors.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SuccessionController::getSuccessors
* @see app/Http/Controllers/Api/SuccessionController.php:21
* @route '/api/talent/succession/role/{roleId}'
*/
getSuccessorsForm.get = (args: { roleId: string | number } | [roleId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getSuccessors.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SuccessionController::getSuccessors
* @see app/Http/Controllers/Api/SuccessionController.php:21
* @route '/api/talent/succession/role/{roleId}'
*/
getSuccessorsForm.head = (args: { roleId: string | number } | [roleId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getSuccessors.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getSuccessors.form = getSuccessorsForm

/**
* @see \App\Http\Controllers\Api\SuccessionController::analyzeCandidate
* @see app/Http/Controllers/Api/SuccessionController.php:42
* @route '/api/talent/succession/analyze-candidate'
*/
export const analyzeCandidate = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyzeCandidate.url(options),
    method: 'post',
})

analyzeCandidate.definition = {
    methods: ["post"],
    url: '/api/talent/succession/analyze-candidate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\SuccessionController::analyzeCandidate
* @see app/Http/Controllers/Api/SuccessionController.php:42
* @route '/api/talent/succession/analyze-candidate'
*/
analyzeCandidate.url = (options?: RouteQueryOptions) => {
    return analyzeCandidate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SuccessionController::analyzeCandidate
* @see app/Http/Controllers/Api/SuccessionController.php:42
* @route '/api/talent/succession/analyze-candidate'
*/
analyzeCandidate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyzeCandidate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\SuccessionController::analyzeCandidate
* @see app/Http/Controllers/Api/SuccessionController.php:42
* @route '/api/talent/succession/analyze-candidate'
*/
const analyzeCandidateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: analyzeCandidate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\SuccessionController::analyzeCandidate
* @see app/Http/Controllers/Api/SuccessionController.php:42
* @route '/api/talent/succession/analyze-candidate'
*/
analyzeCandidateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: analyzeCandidate.url(options),
    method: 'post',
})

analyzeCandidate.form = analyzeCandidateForm

/**
* @see \App\Http\Controllers\Api\SuccessionController::storePlan
* @see app/Http/Controllers/Api/SuccessionController.php:86
* @route '/api/talent/succession/store-plan'
*/
export const storePlan = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storePlan.url(options),
    method: 'post',
})

storePlan.definition = {
    methods: ["post"],
    url: '/api/talent/succession/store-plan',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\SuccessionController::storePlan
* @see app/Http/Controllers/Api/SuccessionController.php:86
* @route '/api/talent/succession/store-plan'
*/
storePlan.url = (options?: RouteQueryOptions) => {
    return storePlan.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SuccessionController::storePlan
* @see app/Http/Controllers/Api/SuccessionController.php:86
* @route '/api/talent/succession/store-plan'
*/
storePlan.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storePlan.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\SuccessionController::storePlan
* @see app/Http/Controllers/Api/SuccessionController.php:86
* @route '/api/talent/succession/store-plan'
*/
const storePlanForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storePlan.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\SuccessionController::storePlan
* @see app/Http/Controllers/Api/SuccessionController.php:86
* @route '/api/talent/succession/store-plan'
*/
storePlanForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storePlan.url(options),
    method: 'post',
})

storePlan.form = storePlanForm

const SuccessionController = { index, getSuccessors, analyzeCandidate, storePlan }

export default SuccessionController