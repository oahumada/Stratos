import {
    applyUrlDefaults,
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../../wayfinder';
/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::index
 * @see app/Http/Controllers/Api/AdminOperationsController.php:29
 * @route '/api/admin/operations'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

index.definition = {
    methods: ['get', 'head'],
    url: '/api/admin/operations',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::index
 * @see app/Http/Controllers/Api/AdminOperationsController.php:29
 * @route '/api/admin/operations'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::index
 * @see app/Http/Controllers/Api/AdminOperationsController.php:29
 * @route '/api/admin/operations'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::index
 * @see app/Http/Controllers/Api/AdminOperationsController.php:29
 * @route '/api/admin/operations'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::index
 * @see app/Http/Controllers/Api/AdminOperationsController.php:29
 * @route '/api/admin/operations'
 */
const indexForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::index
 * @see app/Http/Controllers/Api/AdminOperationsController.php:29
 * @route '/api/admin/operations'
 */
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::index
 * @see app/Http/Controllers/Api/AdminOperationsController.php:29
 * @route '/api/admin/operations'
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
 * @see \App\Http\Controllers\Api\AdminOperationsController::preview
 * @see app/Http/Controllers/Api/AdminOperationsController.php:52
 * @route '/api/admin/operations/{id}/preview'
 */
export const preview = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: preview.url(args, options),
    method: 'post',
});

preview.definition = {
    methods: ['post'],
    url: '/api/admin/operations/{id}/preview',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::preview
 * @see app/Http/Controllers/Api/AdminOperationsController.php:52
 * @route '/api/admin/operations/{id}/preview'
 */
preview.url = (
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
        preview.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::preview
 * @see app/Http/Controllers/Api/AdminOperationsController.php:52
 * @route '/api/admin/operations/{id}/preview'
 */
preview.post = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: preview.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::preview
 * @see app/Http/Controllers/Api/AdminOperationsController.php:52
 * @route '/api/admin/operations/{id}/preview'
 */
const previewForm = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: preview.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::preview
 * @see app/Http/Controllers/Api/AdminOperationsController.php:52
 * @route '/api/admin/operations/{id}/preview'
 */
previewForm.post = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: preview.url(args, options),
    method: 'post',
});

preview.form = previewForm;

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::execute
 * @see app/Http/Controllers/Api/AdminOperationsController.php:87
 * @route '/api/admin/operations/{id}/execute'
 */
export const execute = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: execute.url(args, options),
    method: 'post',
});

execute.definition = {
    methods: ['post'],
    url: '/api/admin/operations/{id}/execute',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::execute
 * @see app/Http/Controllers/Api/AdminOperationsController.php:87
 * @route '/api/admin/operations/{id}/execute'
 */
execute.url = (
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
        execute.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::execute
 * @see app/Http/Controllers/Api/AdminOperationsController.php:87
 * @route '/api/admin/operations/{id}/execute'
 */
execute.post = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: execute.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::execute
 * @see app/Http/Controllers/Api/AdminOperationsController.php:87
 * @route '/api/admin/operations/{id}/execute'
 */
const executeForm = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: execute.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::execute
 * @see app/Http/Controllers/Api/AdminOperationsController.php:87
 * @route '/api/admin/operations/{id}/execute'
 */
executeForm.post = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: execute.url(args, options),
    method: 'post',
});

execute.form = executeForm;

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::cancel
 * @see app/Http/Controllers/Api/AdminOperationsController.php:138
 * @route '/api/admin/operations/{id}/cancel'
 */
export const cancel = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: cancel.url(args, options),
    method: 'post',
});

cancel.definition = {
    methods: ['post'],
    url: '/api/admin/operations/{id}/cancel',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::cancel
 * @see app/Http/Controllers/Api/AdminOperationsController.php:138
 * @route '/api/admin/operations/{id}/cancel'
 */
cancel.url = (
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
        cancel.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::cancel
 * @see app/Http/Controllers/Api/AdminOperationsController.php:138
 * @route '/api/admin/operations/{id}/cancel'
 */
cancel.post = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: cancel.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::cancel
 * @see app/Http/Controllers/Api/AdminOperationsController.php:138
 * @route '/api/admin/operations/{id}/cancel'
 */
const cancelForm = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: cancel.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::cancel
 * @see app/Http/Controllers/Api/AdminOperationsController.php:138
 * @route '/api/admin/operations/{id}/cancel'
 */
cancelForm.post = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: cancel.url(args, options),
    method: 'post',
});

cancel.form = cancelForm;

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::monitorStream
 * @see app/Http/Controllers/Api/AdminOperationsController.php:436
 * @route '/api/admin/operations/monitor/stream'
 */
export const monitorStream = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: monitorStream.url(options),
    method: 'get',
});

monitorStream.definition = {
    methods: ['get', 'head'],
    url: '/api/admin/operations/monitor/stream',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::monitorStream
 * @see app/Http/Controllers/Api/AdminOperationsController.php:436
 * @route '/api/admin/operations/monitor/stream'
 */
monitorStream.url = (options?: RouteQueryOptions) => {
    return monitorStream.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::monitorStream
 * @see app/Http/Controllers/Api/AdminOperationsController.php:436
 * @route '/api/admin/operations/monitor/stream'
 */
monitorStream.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: monitorStream.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::monitorStream
 * @see app/Http/Controllers/Api/AdminOperationsController.php:436
 * @route '/api/admin/operations/monitor/stream'
 */
monitorStream.head = (
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: monitorStream.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::monitorStream
 * @see app/Http/Controllers/Api/AdminOperationsController.php:436
 * @route '/api/admin/operations/monitor/stream'
 */
const monitorStreamForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: monitorStream.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::monitorStream
 * @see app/Http/Controllers/Api/AdminOperationsController.php:436
 * @route '/api/admin/operations/monitor/stream'
 */
monitorStreamForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: monitorStream.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\AdminOperationsController::monitorStream
 * @see app/Http/Controllers/Api/AdminOperationsController.php:436
 * @route '/api/admin/operations/monitor/stream'
 */
monitorStreamForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: monitorStream.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

monitorStream.form = monitorStreamForm;
