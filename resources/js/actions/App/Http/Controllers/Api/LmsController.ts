import {
    applyUrlDefaults,
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../../../../wayfinder';
/**
 * @see \App\Http\Controllers\Api\LmsController::search
 * @see app/Http/Controllers/Api/LmsController.php:22
 * @route '/api/strategic-planning/lms/search'
 */
export const search = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: search.url(options),
    method: 'get',
});

search.definition = {
    methods: ['get', 'head'],
    url: '/api/strategic-planning/lms/search',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\LmsController::search
 * @see app/Http/Controllers/Api/LmsController.php:22
 * @route '/api/strategic-planning/lms/search'
 */
search.url = (options?: RouteQueryOptions) => {
    return search.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\LmsController::search
 * @see app/Http/Controllers/Api/LmsController.php:22
 * @route '/api/strategic-planning/lms/search'
 */
search.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: search.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\LmsController::search
 * @see app/Http/Controllers/Api/LmsController.php:22
 * @route '/api/strategic-planning/lms/search'
 */
search.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: search.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\LmsController::search
 * @see app/Http/Controllers/Api/LmsController.php:22
 * @route '/api/strategic-planning/lms/search'
 */
const searchForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: search.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\LmsController::search
 * @see app/Http/Controllers/Api/LmsController.php:22
 * @route '/api/strategic-planning/lms/search'
 */
searchForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: search.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\LmsController::search
 * @see app/Http/Controllers/Api/LmsController.php:22
 * @route '/api/strategic-planning/lms/search'
 */
searchForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: search.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

search.form = searchForm;

/**
 * @see \App\Http\Controllers\Api\LmsController::launch
 * @see app/Http/Controllers/Api/LmsController.php:36
 * @route '/api/strategic-planning/lms/actions/{action}/launch'
 */
export const launch = (
    args:
        | { action: number | { id: number } }
        | [action: number | { id: number }]
        | number
        | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: launch.url(args, options),
    method: 'post',
});

launch.definition = {
    methods: ['post'],
    url: '/api/strategic-planning/lms/actions/{action}/launch',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\LmsController::launch
 * @see app/Http/Controllers/Api/LmsController.php:36
 * @route '/api/strategic-planning/lms/actions/{action}/launch'
 */
launch.url = (
    args:
        | { action: number | { id: number } }
        | [action: number | { id: number }]
        | number
        | { id: number },
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { action: args };
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { action: args.id };
    }

    if (Array.isArray(args)) {
        args = {
            action: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        action: typeof args.action === 'object' ? args.action.id : args.action,
    };

    return (
        launch.definition.url
            .replace('{action}', parsedArgs.action.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\LmsController::launch
 * @see app/Http/Controllers/Api/LmsController.php:36
 * @route '/api/strategic-planning/lms/actions/{action}/launch'
 */
launch.post = (
    args:
        | { action: number | { id: number } }
        | [action: number | { id: number }]
        | number
        | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: launch.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\LmsController::launch
 * @see app/Http/Controllers/Api/LmsController.php:36
 * @route '/api/strategic-planning/lms/actions/{action}/launch'
 */
const launchForm = (
    args:
        | { action: number | { id: number } }
        | [action: number | { id: number }]
        | number
        | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: launch.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\LmsController::launch
 * @see app/Http/Controllers/Api/LmsController.php:36
 * @route '/api/strategic-planning/lms/actions/{action}/launch'
 */
launchForm.post = (
    args:
        | { action: number | { id: number } }
        | [action: number | { id: number }]
        | number
        | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: launch.url(args, options),
    method: 'post',
});

launch.form = launchForm;

/**
 * @see \App\Http\Controllers\Api\LmsController::sync
 * @see app/Http/Controllers/Api/LmsController.php:56
 * @route '/api/strategic-planning/lms/actions/{action}/sync'
 */
export const sync = (
    args:
        | { action: number | { id: number } }
        | [action: number | { id: number }]
        | number
        | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: sync.url(args, options),
    method: 'post',
});

sync.definition = {
    methods: ['post'],
    url: '/api/strategic-planning/lms/actions/{action}/sync',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\LmsController::sync
 * @see app/Http/Controllers/Api/LmsController.php:56
 * @route '/api/strategic-planning/lms/actions/{action}/sync'
 */
sync.url = (
    args:
        | { action: number | { id: number } }
        | [action: number | { id: number }]
        | number
        | { id: number },
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { action: args };
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { action: args.id };
    }

    if (Array.isArray(args)) {
        args = {
            action: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        action: typeof args.action === 'object' ? args.action.id : args.action,
    };

    return (
        sync.definition.url
            .replace('{action}', parsedArgs.action.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\LmsController::sync
 * @see app/Http/Controllers/Api/LmsController.php:56
 * @route '/api/strategic-planning/lms/actions/{action}/sync'
 */
sync.post = (
    args:
        | { action: number | { id: number } }
        | [action: number | { id: number }]
        | number
        | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: sync.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\LmsController::sync
 * @see app/Http/Controllers/Api/LmsController.php:56
 * @route '/api/strategic-planning/lms/actions/{action}/sync'
 */
const syncForm = (
    args:
        | { action: number | { id: number } }
        | [action: number | { id: number }]
        | number
        | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: sync.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\LmsController::sync
 * @see app/Http/Controllers/Api/LmsController.php:56
 * @route '/api/strategic-planning/lms/actions/{action}/sync'
 */
syncForm.post = (
    args:
        | { action: number | { id: number } }
        | [action: number | { id: number }]
        | number
        | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: sync.url(args, options),
    method: 'post',
});

sync.form = syncForm;

/**
 * @see \App\Http\Controllers\Api\LmsController::getGamificationStats
 * @see app/Http/Controllers/Api/LmsController.php:70
 * @route '/api/strategic-planning/lms/stats'
 */
export const getGamificationStats = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getGamificationStats.url(options),
    method: 'get',
});

getGamificationStats.definition = {
    methods: ['get', 'head'],
    url: '/api/strategic-planning/lms/stats',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\LmsController::getGamificationStats
 * @see app/Http/Controllers/Api/LmsController.php:70
 * @route '/api/strategic-planning/lms/stats'
 */
getGamificationStats.url = (options?: RouteQueryOptions) => {
    return getGamificationStats.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\LmsController::getGamificationStats
 * @see app/Http/Controllers/Api/LmsController.php:70
 * @route '/api/strategic-planning/lms/stats'
 */
getGamificationStats.get = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getGamificationStats.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\LmsController::getGamificationStats
 * @see app/Http/Controllers/Api/LmsController.php:70
 * @route '/api/strategic-planning/lms/stats'
 */
getGamificationStats.head = (
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: getGamificationStats.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\LmsController::getGamificationStats
 * @see app/Http/Controllers/Api/LmsController.php:70
 * @route '/api/strategic-planning/lms/stats'
 */
const getGamificationStatsForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getGamificationStats.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\LmsController::getGamificationStats
 * @see app/Http/Controllers/Api/LmsController.php:70
 * @route '/api/strategic-planning/lms/stats'
 */
getGamificationStatsForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getGamificationStats.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\LmsController::getGamificationStats
 * @see app/Http/Controllers/Api/LmsController.php:70
 * @route '/api/strategic-planning/lms/stats'
 */
getGamificationStatsForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getGamificationStats.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

getGamificationStats.form = getGamificationStatsForm;

/**
 * @see \App\Http\Controllers\Api\LmsController::getLeaderboard
 * @see app/Http/Controllers/Api/LmsController.php:100
 * @route '/api/strategic-planning/lms/leaderboard'
 */
export const getLeaderboard = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getLeaderboard.url(options),
    method: 'get',
});

getLeaderboard.definition = {
    methods: ['get', 'head'],
    url: '/api/strategic-planning/lms/leaderboard',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\LmsController::getLeaderboard
 * @see app/Http/Controllers/Api/LmsController.php:100
 * @route '/api/strategic-planning/lms/leaderboard'
 */
getLeaderboard.url = (options?: RouteQueryOptions) => {
    return getLeaderboard.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\LmsController::getLeaderboard
 * @see app/Http/Controllers/Api/LmsController.php:100
 * @route '/api/strategic-planning/lms/leaderboard'
 */
getLeaderboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getLeaderboard.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\LmsController::getLeaderboard
 * @see app/Http/Controllers/Api/LmsController.php:100
 * @route '/api/strategic-planning/lms/leaderboard'
 */
getLeaderboard.head = (
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: getLeaderboard.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\LmsController::getLeaderboard
 * @see app/Http/Controllers/Api/LmsController.php:100
 * @route '/api/strategic-planning/lms/leaderboard'
 */
const getLeaderboardForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getLeaderboard.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\LmsController::getLeaderboard
 * @see app/Http/Controllers/Api/LmsController.php:100
 * @route '/api/strategic-planning/lms/leaderboard'
 */
getLeaderboardForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getLeaderboard.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\LmsController::getLeaderboard
 * @see app/Http/Controllers/Api/LmsController.php:100
 * @route '/api/strategic-planning/lms/leaderboard'
 */
getLeaderboardForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getLeaderboard.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

getLeaderboard.form = getLeaderboardForm;

const LmsController = {
    search,
    launch,
    sync,
    getGamificationStats,
    getLeaderboard,
};

export default LmsController;
