import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\GenerationChunkController::index
* @see app/Http/Controllers/Api/GenerationChunkController.php:12
* @route '/api/strategic-planning/scenarios/generate/{id}/chunks'
*/
export const index = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenarios/generate/{id}/chunks',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\GenerationChunkController::index
* @see app/Http/Controllers/Api/GenerationChunkController.php:12
* @route '/api/strategic-planning/scenarios/generate/{id}/chunks'
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
* @see \App\Http\Controllers\Api\GenerationChunkController::index
* @see app/Http/Controllers/Api/GenerationChunkController.php:12
* @route '/api/strategic-planning/scenarios/generate/{id}/chunks'
*/
index.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\GenerationChunkController::index
* @see app/Http/Controllers/Api/GenerationChunkController.php:12
* @route '/api/strategic-planning/scenarios/generate/{id}/chunks'
*/
index.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\GenerationChunkController::compacted
* @see app/Http/Controllers/Api/GenerationChunkController.php:32
* @route '/api/strategic-planning/scenarios/generate/{id}/compacted'
*/
export const compacted = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: compacted.url(args, options),
    method: 'get',
})

compacted.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenarios/generate/{id}/compacted',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\GenerationChunkController::compacted
* @see app/Http/Controllers/Api/GenerationChunkController.php:32
* @route '/api/strategic-planning/scenarios/generate/{id}/compacted'
*/
compacted.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return compacted.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\GenerationChunkController::compacted
* @see app/Http/Controllers/Api/GenerationChunkController.php:32
* @route '/api/strategic-planning/scenarios/generate/{id}/compacted'
*/
compacted.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: compacted.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\GenerationChunkController::compacted
* @see app/Http/Controllers/Api/GenerationChunkController.php:32
* @route '/api/strategic-planning/scenarios/generate/{id}/compacted'
*/
compacted.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: compacted.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\GenerationChunkController::progress
* @see app/Http/Controllers/Api/GenerationChunkController.php:84
* @route '/api/strategic-planning/scenarios/generate/{id}/progress'
*/
export const progress = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: progress.url(args, options),
    method: 'get',
})

progress.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenarios/generate/{id}/progress',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\GenerationChunkController::progress
* @see app/Http/Controllers/Api/GenerationChunkController.php:84
* @route '/api/strategic-planning/scenarios/generate/{id}/progress'
*/
progress.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return progress.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\GenerationChunkController::progress
* @see app/Http/Controllers/Api/GenerationChunkController.php:84
* @route '/api/strategic-planning/scenarios/generate/{id}/progress'
*/
progress.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: progress.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\GenerationChunkController::progress
* @see app/Http/Controllers/Api/GenerationChunkController.php:84
* @route '/api/strategic-planning/scenarios/generate/{id}/progress'
*/
progress.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: progress.url(args, options),
    method: 'head',
})

const GenerationChunkController = { index, compacted, progress }

export default GenerationChunkController