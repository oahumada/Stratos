import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\EvidenceController::index
* @see app/Http/Controllers/Api/EvidenceController.php:16
* @route '/api/evidences'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/evidences',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\EvidenceController::index
* @see app/Http/Controllers/Api/EvidenceController.php:16
* @route '/api/evidences'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\EvidenceController::index
* @see app/Http/Controllers/Api/EvidenceController.php:16
* @route '/api/evidences'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\EvidenceController::index
* @see app/Http/Controllers/Api/EvidenceController.php:16
* @route '/api/evidences'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\EvidenceController::store
* @see app/Http/Controllers/Api/EvidenceController.php:32
* @route '/api/evidences'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/evidences',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\EvidenceController::store
* @see app/Http/Controllers/Api/EvidenceController.php:32
* @route '/api/evidences'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\EvidenceController::store
* @see app/Http/Controllers/Api/EvidenceController.php:32
* @route '/api/evidences'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\EvidenceController::destroy
* @see app/Http/Controllers/Api/EvidenceController.php:59
* @route '/api/evidences/{id}'
*/
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/evidences/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\EvidenceController::destroy
* @see app/Http/Controllers/Api/EvidenceController.php:59
* @route '/api/evidences/{id}'
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
* @see \App\Http\Controllers\Api\EvidenceController::destroy
* @see app/Http/Controllers/Api/EvidenceController.php:59
* @route '/api/evidences/{id}'
*/
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const EvidenceController = { index, store, destroy }

export default EvidenceController