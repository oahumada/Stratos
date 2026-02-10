import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\DevelopmentPathController::index
* @see app/Http/Controllers/Api/DevelopmentPathController.php:18
* @route '/api/development-paths'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/development-paths',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\DevelopmentPathController::index
* @see app/Http/Controllers/Api/DevelopmentPathController.php:18
* @route '/api/development-paths'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\DevelopmentPathController::index
* @see app/Http/Controllers/Api/DevelopmentPathController.php:18
* @route '/api/development-paths'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\DevelopmentPathController::index
* @see app/Http/Controllers/Api/DevelopmentPathController.php:18
* @route '/api/development-paths'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\DevelopmentPathController::index
* @see app/Http/Controllers/Api/DevelopmentPathController.php:18
* @route '/api/development-paths'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\DevelopmentPathController::index
* @see app/Http/Controllers/Api/DevelopmentPathController.php:18
* @route '/api/development-paths'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\DevelopmentPathController::index
* @see app/Http/Controllers/Api/DevelopmentPathController.php:18
* @route '/api/development-paths'
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
* @see \App\Http\Controllers\Api\DevelopmentPathController::generate
* @see app/Http/Controllers/Api/DevelopmentPathController.php:46
* @route '/api/development-paths/generate'
*/
export const generate = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(options),
    method: 'post',
})

generate.definition = {
    methods: ["post"],
    url: '/api/development-paths/generate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\DevelopmentPathController::generate
* @see app/Http/Controllers/Api/DevelopmentPathController.php:46
* @route '/api/development-paths/generate'
*/
generate.url = (options?: RouteQueryOptions) => {
    return generate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\DevelopmentPathController::generate
* @see app/Http/Controllers/Api/DevelopmentPathController.php:46
* @route '/api/development-paths/generate'
*/
generate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\DevelopmentPathController::generate
* @see app/Http/Controllers/Api/DevelopmentPathController.php:46
* @route '/api/development-paths/generate'
*/
const generateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\DevelopmentPathController::generate
* @see app/Http/Controllers/Api/DevelopmentPathController.php:46
* @route '/api/development-paths/generate'
*/
generateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generate.url(options),
    method: 'post',
})

generate.form = generateForm

/**
* @see \App\Http\Controllers\Api\DevelopmentPathController::destroy
* @see app/Http/Controllers/Api/DevelopmentPathController.php:106
* @route '/api/development-paths/{id}'
*/
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/development-paths/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\DevelopmentPathController::destroy
* @see app/Http/Controllers/Api/DevelopmentPathController.php:106
* @route '/api/development-paths/{id}'
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
* @see \App\Http\Controllers\Api\DevelopmentPathController::destroy
* @see app/Http/Controllers/Api/DevelopmentPathController.php:106
* @route '/api/development-paths/{id}'
*/
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\DevelopmentPathController::destroy
* @see app/Http/Controllers/Api/DevelopmentPathController.php:106
* @route '/api/development-paths/{id}'
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
* @see \App\Http\Controllers\Api\DevelopmentPathController::destroy
* @see app/Http/Controllers/Api/DevelopmentPathController.php:106
* @route '/api/development-paths/{id}'
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

const DevelopmentPathController = { index, generate, destroy }

export default DevelopmentPathController