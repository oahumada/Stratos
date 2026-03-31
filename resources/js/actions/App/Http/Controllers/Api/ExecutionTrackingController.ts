import {
    applyUrlDefaults,
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../../../../wayfinder';
/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::index
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:13
 * @route '/api/strategic-planning/mobility/execution-status'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

index.definition = {
    methods: ['get', 'head'],
    url: '/api/strategic-planning/mobility/execution-status',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::index
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:13
 * @route '/api/strategic-planning/mobility/execution-status'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::index
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:13
 * @route '/api/strategic-planning/mobility/execution-status'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::index
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:13
 * @route '/api/strategic-planning/mobility/execution-status'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::index
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:13
 * @route '/api/strategic-planning/mobility/execution-status'
 */
const indexForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::index
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:13
 * @route '/api/strategic-planning/mobility/execution-status'
 */
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::index
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:13
 * @route '/api/strategic-planning/mobility/execution-status'
 */
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

index.form = indexForm;

/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::show
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:59
 * @route '/api/strategic-planning/mobility/execution/{id}'
 */
export const show = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
});

show.definition = {
    methods: ['get', 'head'],
    url: '/api/strategic-planning/mobility/execution/{id}',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::show
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:59
 * @route '/api/strategic-planning/mobility/execution/{id}'
 */
show.url = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args };
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        id: args.id,
    };

    return (
        show.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::show
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:59
 * @route '/api/strategic-planning/mobility/execution/{id}'
 */
show.get = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::show
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:59
 * @route '/api/strategic-planning/mobility/execution/{id}'
 */
show.head = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::show
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:59
 * @route '/api/strategic-planning/mobility/execution/{id}'
 */
const showForm = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::show
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:59
 * @route '/api/strategic-planning/mobility/execution/{id}'
 */
showForm.get = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::show
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:59
 * @route '/api/strategic-planning/mobility/execution/{id}'
 */
showForm.head = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

show.form = showForm;

/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::launchLms
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:78
 * @route '/api/strategic-planning/mobility/execution/launch/{actionId}'
 */
export const launchLms = (
    args:
        | { actionId: string | number }
        | [actionId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: launchLms.url(args, options),
    method: 'post',
});

launchLms.definition = {
    methods: ['post'],
    url: '/api/strategic-planning/mobility/execution/launch/{actionId}',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::launchLms
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:78
 * @route '/api/strategic-planning/mobility/execution/launch/{actionId}'
 */
launchLms.url = (
    args:
        | { actionId: string | number }
        | [actionId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { actionId: args };
    }

    if (Array.isArray(args)) {
        args = {
            actionId: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        actionId: args.actionId,
    };

    return (
        launchLms.definition.url
            .replace('{actionId}', parsedArgs.actionId.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::launchLms
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:78
 * @route '/api/strategic-planning/mobility/execution/launch/{actionId}'
 */
launchLms.post = (
    args:
        | { actionId: string | number }
        | [actionId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: launchLms.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::launchLms
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:78
 * @route '/api/strategic-planning/mobility/execution/launch/{actionId}'
 */
const launchLmsForm = (
    args:
        | { actionId: string | number }
        | [actionId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: launchLms.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::launchLms
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:78
 * @route '/api/strategic-planning/mobility/execution/launch/{actionId}'
 */
launchLmsForm.post = (
    args:
        | { actionId: string | number }
        | [actionId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: launchLms.url(args, options),
    method: 'post',
});

launchLms.form = launchLmsForm;

/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::syncProgress
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:105
 * @route '/api/strategic-planning/mobility/execution/sync/{actionId}'
 */
export const syncProgress = (
    args:
        | { actionId: string | number }
        | [actionId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: syncProgress.url(args, options),
    method: 'post',
});

syncProgress.definition = {
    methods: ['post'],
    url: '/api/strategic-planning/mobility/execution/sync/{actionId}',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::syncProgress
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:105
 * @route '/api/strategic-planning/mobility/execution/sync/{actionId}'
 */
syncProgress.url = (
    args:
        | { actionId: string | number }
        | [actionId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { actionId: args };
    }

    if (Array.isArray(args)) {
        args = {
            actionId: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        actionId: args.actionId,
    };

    return (
        syncProgress.definition.url
            .replace('{actionId}', parsedArgs.actionId.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::syncProgress
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:105
 * @route '/api/strategic-planning/mobility/execution/sync/{actionId}'
 */
syncProgress.post = (
    args:
        | { actionId: string | number }
        | [actionId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: syncProgress.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::syncProgress
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:105
 * @route '/api/strategic-planning/mobility/execution/sync/{actionId}'
 */
const syncProgressForm = (
    args:
        | { actionId: string | number }
        | [actionId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: syncProgress.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\ExecutionTrackingController::syncProgress
 * @see app/Http/Controllers/Api/ExecutionTrackingController.php:105
 * @route '/api/strategic-planning/mobility/execution/sync/{actionId}'
 */
syncProgressForm.post = (
    args:
        | { actionId: string | number }
        | [actionId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: syncProgress.url(args, options),
    method: 'post',
});

syncProgress.form = syncProgressForm;

const ExecutionTrackingController = { index, show, launchLms, syncProgress };

export default ExecutionTrackingController;
