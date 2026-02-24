import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\PulseController::index
* @see app/Http/Controllers/Api/PulseController.php:16
* @route '/api/pulse-surveys'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/pulse-surveys',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PulseController::index
* @see app/Http/Controllers/Api/PulseController.php:16
* @route '/api/pulse-surveys'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PulseController::index
* @see app/Http/Controllers/Api/PulseController.php:16
* @route '/api/pulse-surveys'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PulseController::index
* @see app/Http/Controllers/Api/PulseController.php:16
* @route '/api/pulse-surveys'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PulseController::index
* @see app/Http/Controllers/Api/PulseController.php:16
* @route '/api/pulse-surveys'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PulseController::index
* @see app/Http/Controllers/Api/PulseController.php:16
* @route '/api/pulse-surveys'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PulseController::index
* @see app/Http/Controllers/Api/PulseController.php:16
* @route '/api/pulse-surveys'
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
* @see \App\Http\Controllers\Api\PulseController::show
* @see app/Http/Controllers/Api/PulseController.php:29
* @route '/api/pulse-surveys/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/pulse-surveys/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PulseController::show
* @see app/Http/Controllers/Api/PulseController.php:29
* @route '/api/pulse-surveys/{id}'
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
* @see \App\Http\Controllers\Api\PulseController::show
* @see app/Http/Controllers/Api/PulseController.php:29
* @route '/api/pulse-surveys/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PulseController::show
* @see app/Http/Controllers/Api/PulseController.php:29
* @route '/api/pulse-surveys/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PulseController::show
* @see app/Http/Controllers/Api/PulseController.php:29
* @route '/api/pulse-surveys/{id}'
*/
const showForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PulseController::show
* @see app/Http/Controllers/Api/PulseController.php:29
* @route '/api/pulse-surveys/{id}'
*/
showForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PulseController::show
* @see app/Http/Controllers/Api/PulseController.php:29
* @route '/api/pulse-surveys/{id}'
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
* @see \App\Http\Controllers\Api\PulseController::storeResponse
* @see app/Http/Controllers/Api/PulseController.php:38
* @route '/api/pulse-responses'
*/
export const storeResponse = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeResponse.url(options),
    method: 'post',
})

storeResponse.definition = {
    methods: ["post"],
    url: '/api/pulse-responses',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\PulseController::storeResponse
* @see app/Http/Controllers/Api/PulseController.php:38
* @route '/api/pulse-responses'
*/
storeResponse.url = (options?: RouteQueryOptions) => {
    return storeResponse.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PulseController::storeResponse
* @see app/Http/Controllers/Api/PulseController.php:38
* @route '/api/pulse-responses'
*/
storeResponse.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeResponse.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PulseController::storeResponse
* @see app/Http/Controllers/Api/PulseController.php:38
* @route '/api/pulse-responses'
*/
const storeResponseForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeResponse.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PulseController::storeResponse
* @see app/Http/Controllers/Api/PulseController.php:38
* @route '/api/pulse-responses'
*/
storeResponseForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeResponse.url(options),
    method: 'post',
})

storeResponse.form = storeResponseForm

const PulseController = { index, show, storeResponse }

export default PulseController