import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ApplicationController::index
* @see app/Http/Controllers/Api/ApplicationController.php:14
* @route '/api/applications'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/applications',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ApplicationController::index
* @see app/Http/Controllers/Api/ApplicationController.php:14
* @route '/api/applications'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ApplicationController::index
* @see app/Http/Controllers/Api/ApplicationController.php:14
* @route '/api/applications'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ApplicationController::index
* @see app/Http/Controllers/Api/ApplicationController.php:14
* @route '/api/applications'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ApplicationController::show
* @see app/Http/Controllers/Api/ApplicationController.php:30
* @route '/api/applications/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/applications/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ApplicationController::show
* @see app/Http/Controllers/Api/ApplicationController.php:30
* @route '/api/applications/{id}'
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
* @see \App\Http\Controllers\Api\ApplicationController::show
* @see app/Http/Controllers/Api/ApplicationController.php:30
* @route '/api/applications/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ApplicationController::show
* @see app/Http/Controllers/Api/ApplicationController.php:30
* @route '/api/applications/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ApplicationController::store
* @see app/Http/Controllers/Api/ApplicationController.php:53
* @route '/api/applications'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/applications',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ApplicationController::store
* @see app/Http/Controllers/Api/ApplicationController.php:53
* @route '/api/applications'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ApplicationController::store
* @see app/Http/Controllers/Api/ApplicationController.php:53
* @route '/api/applications'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ApplicationController::update
* @see app/Http/Controllers/Api/ApplicationController.php:95
* @route '/api/applications/{id}'
*/
export const update = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/api/applications/{id}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\ApplicationController::update
* @see app/Http/Controllers/Api/ApplicationController.php:95
* @route '/api/applications/{id}'
*/
update.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ApplicationController::update
* @see app/Http/Controllers/Api/ApplicationController.php:95
* @route '/api/applications/{id}'
*/
update.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

const ApplicationController = { index, show, store, update }

export default ApplicationController