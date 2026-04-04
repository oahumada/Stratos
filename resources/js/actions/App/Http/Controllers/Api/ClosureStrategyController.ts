import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see app/Http/Controllers/Api/ClosureStrategyController.php:19
* @route '/api/scenarios/{id}/recommendations'
*/
export const index = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{id}/recommendations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see app/Http/Controllers/Api/ClosureStrategyController.php:19
* @route '/api/scenarios/{id}/recommendations'
*/
index.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return index.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see app/Http/Controllers/Api/ClosureStrategyController.php:19
* @route '/api/scenarios/{id}/recommendations'
*/
index.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see app/Http/Controllers/Api/ClosureStrategyController.php:19
* @route '/api/scenarios/{id}/recommendations'
*/
index.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see app/Http/Controllers/Api/ClosureStrategyController.php:19
* @route '/api/scenarios/{id}/recommendations'
*/
const indexForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see app/Http/Controllers/Api/ClosureStrategyController.php:19
* @route '/api/scenarios/{id}/recommendations'
*/
indexForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see app/Http/Controllers/Api/ClosureStrategyController.php:19
* @route '/api/scenarios/{id}/recommendations'
*/
indexForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see app/Http/Controllers/Api/ClosureStrategyController.php:43
* @route '/api/scenarios/{id}/recommendations/generate'
*/
export const generate = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(args, options),
    method: 'post',
})

generate.definition = {
    methods: ["post"],
    url: '/api/scenarios/{id}/recommendations/generate',
} satisfies RouteDefinition<["post"]>

/**
* @see app/Http/Controllers/Api/ClosureStrategyController.php:43
* @route '/api/scenarios/{id}/recommendations/generate'
*/
generate.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return generate.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see app/Http/Controllers/Api/ClosureStrategyController.php:43
* @route '/api/scenarios/{id}/recommendations/generate'
*/
generate.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(args, options),
    method: 'post',
})

/**
* @see app/Http/Controllers/Api/ClosureStrategyController.php:43
* @route '/api/scenarios/{id}/recommendations/generate'
*/
const generateForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generate.url(args, options),
    method: 'post',
})

/**
* @see app/Http/Controllers/Api/ClosureStrategyController.php:43
* @route '/api/scenarios/{id}/recommendations/generate'
*/
generateForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generate.url(args, options),
    method: 'post',
})

generate.form = generateForm

const ClosureStrategyController = { index, generate }

export default ClosureStrategyController