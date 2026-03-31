import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ExecutiveSummaryController::__invoke
* @see app/Http/Controllers/Api/ExecutiveSummaryController.php:31
* @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary'
*/
export const __invoke = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: __invoke.url(args, options),
    method: 'get',
})

__invoke.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenarios/{scenarioId}/executive-summary',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ExecutiveSummaryController::__invoke
* @see app/Http/Controllers/Api/ExecutiveSummaryController.php:31
* @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary'
*/
__invoke.url = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenarioId: args }
    }

    if (Array.isArray(args)) {
        args = {
            scenarioId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenarioId: args.scenarioId,
    }

    return __invoke.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ExecutiveSummaryController::__invoke
* @see app/Http/Controllers/Api/ExecutiveSummaryController.php:31
* @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary'
*/
__invoke.get = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: __invoke.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ExecutiveSummaryController::__invoke
* @see app/Http/Controllers/Api/ExecutiveSummaryController.php:31
* @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary'
*/
__invoke.head = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: __invoke.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ExecutiveSummaryController::__invoke
* @see app/Http/Controllers/Api/ExecutiveSummaryController.php:31
* @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary'
*/
const __invokeForm = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: __invoke.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ExecutiveSummaryController::__invoke
* @see app/Http/Controllers/Api/ExecutiveSummaryController.php:31
* @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary'
*/
__invokeForm.get = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: __invoke.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ExecutiveSummaryController::__invoke
* @see app/Http/Controllers/Api/ExecutiveSummaryController.php:31
* @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary'
*/
__invokeForm.head = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: __invoke.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

__invoke.form = __invokeForm

/**
* @see \App\Http\Controllers\Api\ExecutiveSummaryController::generate
* @see app/Http/Controllers/Api/ExecutiveSummaryController.php:49
* @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary'
*/
export const generate = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(args, options),
    method: 'post',
})

generate.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/{scenarioId}/executive-summary',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ExecutiveSummaryController::generate
* @see app/Http/Controllers/Api/ExecutiveSummaryController.php:49
* @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary'
*/
generate.url = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenarioId: args }
    }

    if (Array.isArray(args)) {
        args = {
            scenarioId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenarioId: args.scenarioId,
    }

    return generate.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ExecutiveSummaryController::generate
* @see app/Http/Controllers/Api/ExecutiveSummaryController.php:49
* @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary'
*/
generate.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ExecutiveSummaryController::generate
* @see app/Http/Controllers/Api/ExecutiveSummaryController.php:49
* @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary'
*/
const generateForm = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ExecutiveSummaryController::generate
* @see app/Http/Controllers/Api/ExecutiveSummaryController.php:49
* @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary'
*/
generateForm.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generate.url(args, options),
    method: 'post',
})

generate.form = generateForm

/**
* @see \App\Http\Controllers\Api\ExecutiveSummaryController::exportMethod
* @see app/Http/Controllers/Api/ExecutiveSummaryController.php:70
* @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/export'
*/
export const exportMethod = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: exportMethod.url(args, options),
    method: 'post',
})

exportMethod.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/export',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ExecutiveSummaryController::exportMethod
* @see app/Http/Controllers/Api/ExecutiveSummaryController.php:70
* @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/export'
*/
exportMethod.url = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenarioId: args }
    }

    if (Array.isArray(args)) {
        args = {
            scenarioId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenarioId: args.scenarioId,
    }

    return exportMethod.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ExecutiveSummaryController::exportMethod
* @see app/Http/Controllers/Api/ExecutiveSummaryController.php:70
* @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/export'
*/
exportMethod.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: exportMethod.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ExecutiveSummaryController::exportMethod
* @see app/Http/Controllers/Api/ExecutiveSummaryController.php:70
* @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/export'
*/
const exportMethodForm = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: exportMethod.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ExecutiveSummaryController::exportMethod
* @see app/Http/Controllers/Api/ExecutiveSummaryController.php:70
* @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/export'
*/
exportMethodForm.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: exportMethod.url(args, options),
    method: 'post',
})

exportMethod.form = exportMethodForm

const ExecutiveSummaryController = { __invoke, generate, exportMethod, export: exportMethod }

export default ExecutiveSummaryController