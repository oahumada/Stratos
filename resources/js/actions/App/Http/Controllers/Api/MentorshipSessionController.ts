import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\MentorshipSessionController::index
* @see app/Http/Controllers/Api/MentorshipSessionController.php:15
* @route '/api/mentorship-sessions'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/mentorship-sessions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\MentorshipSessionController::index
* @see app/Http/Controllers/Api/MentorshipSessionController.php:15
* @route '/api/mentorship-sessions'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\MentorshipSessionController::index
* @see app/Http/Controllers/Api/MentorshipSessionController.php:15
* @route '/api/mentorship-sessions'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MentorshipSessionController::index
* @see app/Http/Controllers/Api/MentorshipSessionController.php:15
* @route '/api/mentorship-sessions'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\MentorshipSessionController::store
* @see app/Http/Controllers/Api/MentorshipSessionController.php:31
* @route '/api/mentorship-sessions'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/mentorship-sessions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\MentorshipSessionController::store
* @see app/Http/Controllers/Api/MentorshipSessionController.php:31
* @route '/api/mentorship-sessions'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\MentorshipSessionController::store
* @see app/Http/Controllers/Api/MentorshipSessionController.php:31
* @route '/api/mentorship-sessions'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\MentorshipSessionController::update
* @see app/Http/Controllers/Api/MentorshipSessionController.php:53
* @route '/api/mentorship-sessions/{id}'
*/
export const update = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/api/mentorship-sessions/{id}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\MentorshipSessionController::update
* @see app/Http/Controllers/Api/MentorshipSessionController.php:53
* @route '/api/mentorship-sessions/{id}'
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
* @see \App\Http\Controllers\Api\MentorshipSessionController::update
* @see app/Http/Controllers/Api/MentorshipSessionController.php:53
* @route '/api/mentorship-sessions/{id}'
*/
update.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\MentorshipSessionController::destroy
* @see app/Http/Controllers/Api/MentorshipSessionController.php:76
* @route '/api/mentorship-sessions/{id}'
*/
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/mentorship-sessions/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\MentorshipSessionController::destroy
* @see app/Http/Controllers/Api/MentorshipSessionController.php:76
* @route '/api/mentorship-sessions/{id}'
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
* @see \App\Http\Controllers\Api\MentorshipSessionController::destroy
* @see app/Http/Controllers/Api/MentorshipSessionController.php:76
* @route '/api/mentorship-sessions/{id}'
*/
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const MentorshipSessionController = { index, store, update, destroy }

export default MentorshipSessionController