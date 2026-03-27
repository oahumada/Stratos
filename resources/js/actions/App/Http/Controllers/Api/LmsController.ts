import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\LmsController::search
* @see app/Http/Controllers/Api/LmsController.php:22
* @route '/api/strategic-planning/lms/search'
*/
export const search = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: search.url(options),
    method: 'get',
})

search.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/lms/search',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\LmsController::search
* @see app/Http/Controllers/Api/LmsController.php:22
* @route '/api/strategic-planning/lms/search'
*/
search.url = (options?: RouteQueryOptions) => {
    return search.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\LmsController::search
* @see app/Http/Controllers/Api/LmsController.php:22
* @route '/api/strategic-planning/lms/search'
*/
search.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: search.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\LmsController::search
* @see app/Http/Controllers/Api/LmsController.php:22
* @route '/api/strategic-planning/lms/search'
*/
search.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: search.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\LmsController::launch
* @see app/Http/Controllers/Api/LmsController.php:36
* @route '/api/strategic-planning/lms/actions/{action}/launch'
*/
export const launch = (args: { action: number | { id: number } } | [action: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: launch.url(args, options),
    method: 'post',
})

launch.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/lms/actions/{action}/launch',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\LmsController::launch
* @see app/Http/Controllers/Api/LmsController.php:36
* @route '/api/strategic-planning/lms/actions/{action}/launch'
*/
launch.url = (args: { action: number | { id: number } } | [action: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { action: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { action: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            action: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        action: typeof args.action === 'object'
        ? args.action.id
        : args.action,
    }

    return launch.definition.url
            .replace('{action}', parsedArgs.action.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\LmsController::launch
* @see app/Http/Controllers/Api/LmsController.php:36
* @route '/api/strategic-planning/lms/actions/{action}/launch'
*/
launch.post = (args: { action: number | { id: number } } | [action: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: launch.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\LmsController::sync
* @see app/Http/Controllers/Api/LmsController.php:56
* @route '/api/strategic-planning/lms/actions/{action}/sync'
*/
export const sync = (args: { action: number | { id: number } } | [action: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sync.url(args, options),
    method: 'post',
})

sync.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/lms/actions/{action}/sync',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\LmsController::sync
* @see app/Http/Controllers/Api/LmsController.php:56
* @route '/api/strategic-planning/lms/actions/{action}/sync'
*/
sync.url = (args: { action: number | { id: number } } | [action: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { action: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { action: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            action: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        action: typeof args.action === 'object'
        ? args.action.id
        : args.action,
    }

    return sync.definition.url
            .replace('{action}', parsedArgs.action.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\LmsController::sync
* @see app/Http/Controllers/Api/LmsController.php:56
* @route '/api/strategic-planning/lms/actions/{action}/sync'
*/
sync.post = (args: { action: number | { id: number } } | [action: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sync.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\LmsController::getGamificationStats
* @see app/Http/Controllers/Api/LmsController.php:70
* @route '/api/strategic-planning/lms/stats'
*/
export const getGamificationStats = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getGamificationStats.url(options),
    method: 'get',
})

getGamificationStats.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/lms/stats',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\LmsController::getGamificationStats
* @see app/Http/Controllers/Api/LmsController.php:70
* @route '/api/strategic-planning/lms/stats'
*/
getGamificationStats.url = (options?: RouteQueryOptions) => {
    return getGamificationStats.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\LmsController::getGamificationStats
* @see app/Http/Controllers/Api/LmsController.php:70
* @route '/api/strategic-planning/lms/stats'
*/
getGamificationStats.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getGamificationStats.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\LmsController::getGamificationStats
* @see app/Http/Controllers/Api/LmsController.php:70
* @route '/api/strategic-planning/lms/stats'
*/
getGamificationStats.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getGamificationStats.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\LmsController::getLeaderboard
* @see app/Http/Controllers/Api/LmsController.php:100
* @route '/api/strategic-planning/lms/leaderboard'
*/
export const getLeaderboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getLeaderboard.url(options),
    method: 'get',
})

getLeaderboard.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/lms/leaderboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\LmsController::getLeaderboard
* @see app/Http/Controllers/Api/LmsController.php:100
* @route '/api/strategic-planning/lms/leaderboard'
*/
getLeaderboard.url = (options?: RouteQueryOptions) => {
    return getLeaderboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\LmsController::getLeaderboard
* @see app/Http/Controllers/Api/LmsController.php:100
* @route '/api/strategic-planning/lms/leaderboard'
*/
getLeaderboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getLeaderboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\LmsController::getLeaderboard
* @see app/Http/Controllers/Api/LmsController.php:100
* @route '/api/strategic-planning/lms/leaderboard'
*/
getLeaderboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getLeaderboard.url(options),
    method: 'head',
})

const LmsController = { search, launch, sync, getGamificationStats, getLeaderboard }

export default LmsController