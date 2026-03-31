import {
    applyUrlDefaults,
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../../wayfinder';
/**
 * @see \App\Http\Controllers\Api\AlertController::index
 * @see app/Http/Controllers/Api/AlertController.php:23
 * @route '/api/alerts/thresholds'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

index.definition = {
    methods: ['get', 'head'],
    url: '/api/alerts/thresholds',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\AlertController::index
 * @see app/Http/Controllers/Api/AlertController.php:23
 * @route '/api/alerts/thresholds'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\AlertController::index
 * @see app/Http/Controllers/Api/AlertController.php:23
 * @route '/api/alerts/thresholds'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\AlertController::index
 * @see app/Http/Controllers/Api/AlertController.php:23
 * @route '/api/alerts/thresholds'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\AlertController::index
 * @see app/Http/Controllers/Api/AlertController.php:23
 * @route '/api/alerts/thresholds'
 */
const indexForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\AlertController::index
 * @see app/Http/Controllers/Api/AlertController.php:23
 * @route '/api/alerts/thresholds'
 */
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\AlertController::index
 * @see app/Http/Controllers/Api/AlertController.php:23
 * @route '/api/alerts/thresholds'
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
 * @see \App\Http\Controllers\Api\AlertController::store
 * @see app/Http/Controllers/Api/AlertController.php:40
 * @route '/api/alerts/thresholds'
 */
export const store = (
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
});

store.definition = {
    methods: ['post'],
    url: '/api/alerts/thresholds',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\AlertController::store
 * @see app/Http/Controllers/Api/AlertController.php:40
 * @route '/api/alerts/thresholds'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\AlertController::store
 * @see app/Http/Controllers/Api/AlertController.php:40
 * @route '/api/alerts/thresholds'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\AlertController::store
 * @see app/Http/Controllers/Api/AlertController.php:40
 * @route '/api/alerts/thresholds'
 */
const storeForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\AlertController::store
 * @see app/Http/Controllers/Api/AlertController.php:40
 * @route '/api/alerts/thresholds'
 */
storeForm.post = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
});

store.form = storeForm;

/**
 * @see \App\Http\Controllers\Api\AlertController::show
 * @see app/Http/Controllers/Api/AlertController.php:57
 * @route '/api/alerts/thresholds/{threshold}'
 */
export const show = (
    args:
        | { threshold: string | number | { id: string | number } }
        | [threshold: string | number | { id: string | number }]
        | string
        | number
        | { id: string | number },
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
});

show.definition = {
    methods: ['get', 'head'],
    url: '/api/alerts/thresholds/{threshold}',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\AlertController::show
 * @see app/Http/Controllers/Api/AlertController.php:57
 * @route '/api/alerts/thresholds/{threshold}'
 */
show.url = (
    args:
        | { threshold: string | number | { id: string | number } }
        | [threshold: string | number | { id: string | number }]
        | string
        | number
        | { id: string | number },
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { threshold: args };
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { threshold: args.id };
    }

    if (Array.isArray(args)) {
        args = {
            threshold: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        threshold:
            typeof args.threshold === 'object'
                ? args.threshold.id
                : args.threshold,
    };

    return (
        show.definition.url
            .replace('{threshold}', parsedArgs.threshold.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\AlertController::show
 * @see app/Http/Controllers/Api/AlertController.php:57
 * @route '/api/alerts/thresholds/{threshold}'
 */
show.get = (
    args:
        | { threshold: string | number | { id: string | number } }
        | [threshold: string | number | { id: string | number }]
        | string
        | number
        | { id: string | number },
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\AlertController::show
 * @see app/Http/Controllers/Api/AlertController.php:57
 * @route '/api/alerts/thresholds/{threshold}'
 */
show.head = (
    args:
        | { threshold: string | number | { id: string | number } }
        | [threshold: string | number | { id: string | number }]
        | string
        | number
        | { id: string | number },
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\AlertController::show
 * @see app/Http/Controllers/Api/AlertController.php:57
 * @route '/api/alerts/thresholds/{threshold}'
 */
const showForm = (
    args:
        | { threshold: string | number | { id: string | number } }
        | [threshold: string | number | { id: string | number }]
        | string
        | number
        | { id: string | number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\AlertController::show
 * @see app/Http/Controllers/Api/AlertController.php:57
 * @route '/api/alerts/thresholds/{threshold}'
 */
showForm.get = (
    args:
        | { threshold: string | number | { id: string | number } }
        | [threshold: string | number | { id: string | number }]
        | string
        | number
        | { id: string | number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\AlertController::show
 * @see app/Http/Controllers/Api/AlertController.php:57
 * @route '/api/alerts/thresholds/{threshold}'
 */
showForm.head = (
    args:
        | { threshold: string | number | { id: string | number } }
        | [threshold: string | number | { id: string | number }]
        | string
        | number
        | { id: string | number },
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
 * @see \App\Http\Controllers\Api\AlertController::update
 * @see app/Http/Controllers/Api/AlertController.php:73
 * @route '/api/alerts/thresholds/{threshold}'
 */
export const update = (
    args:
        | { threshold: string | number | { id: string | number } }
        | [threshold: string | number | { id: string | number }]
        | string
        | number
        | { id: string | number },
    options?: RouteQueryOptions,
): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
});

update.definition = {
    methods: ['patch'],
    url: '/api/alerts/thresholds/{threshold}',
} satisfies RouteDefinition<['patch']>;

/**
 * @see \App\Http\Controllers\Api\AlertController::update
 * @see app/Http/Controllers/Api/AlertController.php:73
 * @route '/api/alerts/thresholds/{threshold}'
 */
update.url = (
    args:
        | { threshold: string | number | { id: string | number } }
        | [threshold: string | number | { id: string | number }]
        | string
        | number
        | { id: string | number },
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { threshold: args };
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { threshold: args.id };
    }

    if (Array.isArray(args)) {
        args = {
            threshold: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        threshold:
            typeof args.threshold === 'object'
                ? args.threshold.id
                : args.threshold,
    };

    return (
        update.definition.url
            .replace('{threshold}', parsedArgs.threshold.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\AlertController::update
 * @see app/Http/Controllers/Api/AlertController.php:73
 * @route '/api/alerts/thresholds/{threshold}'
 */
update.patch = (
    args:
        | { threshold: string | number | { id: string | number } }
        | [threshold: string | number | { id: string | number }]
        | string
        | number
        | { id: string | number },
    options?: RouteQueryOptions,
): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
});

/**
 * @see \App\Http\Controllers\Api\AlertController::update
 * @see app/Http/Controllers/Api/AlertController.php:73
 * @route '/api/alerts/thresholds/{threshold}'
 */
const updateForm = (
    args:
        | { threshold: string | number | { id: string | number } }
        | [threshold: string | number | { id: string | number }]
        | string
        | number
        | { id: string | number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\AlertController::update
 * @see app/Http/Controllers/Api/AlertController.php:73
 * @route '/api/alerts/thresholds/{threshold}'
 */
updateForm.patch = (
    args:
        | { threshold: string | number | { id: string | number } }
        | [threshold: string | number | { id: string | number }]
        | string
        | number
        | { id: string | number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'post',
});

update.form = updateForm;

/**
 * @see \App\Http\Controllers\Api\AlertController::destroy
 * @see app/Http/Controllers/Api/AlertController.php:93
 * @route '/api/alerts/thresholds/{threshold}'
 */
export const destroy = (
    args:
        | { threshold: string | number | { id: string | number } }
        | [threshold: string | number | { id: string | number }]
        | string
        | number
        | { id: string | number },
    options?: RouteQueryOptions,
): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
});

destroy.definition = {
    methods: ['delete'],
    url: '/api/alerts/thresholds/{threshold}',
} satisfies RouteDefinition<['delete']>;

/**
 * @see \App\Http\Controllers\Api\AlertController::destroy
 * @see app/Http/Controllers/Api/AlertController.php:93
 * @route '/api/alerts/thresholds/{threshold}'
 */
destroy.url = (
    args:
        | { threshold: string | number | { id: string | number } }
        | [threshold: string | number | { id: string | number }]
        | string
        | number
        | { id: string | number },
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { threshold: args };
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { threshold: args.id };
    }

    if (Array.isArray(args)) {
        args = {
            threshold: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        threshold:
            typeof args.threshold === 'object'
                ? args.threshold.id
                : args.threshold,
    };

    return (
        destroy.definition.url
            .replace('{threshold}', parsedArgs.threshold.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\AlertController::destroy
 * @see app/Http/Controllers/Api/AlertController.php:93
 * @route '/api/alerts/thresholds/{threshold}'
 */
destroy.delete = (
    args:
        | { threshold: string | number | { id: string | number } }
        | [threshold: string | number | { id: string | number }]
        | string
        | number
        | { id: string | number },
    options?: RouteQueryOptions,
): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
});

/**
 * @see \App\Http\Controllers\Api\AlertController::destroy
 * @see app/Http/Controllers/Api/AlertController.php:93
 * @route '/api/alerts/thresholds/{threshold}'
 */
const destroyForm = (
    args:
        | { threshold: string | number | { id: string | number } }
        | [threshold: string | number | { id: string | number }]
        | string
        | number
        | { id: string | number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\AlertController::destroy
 * @see app/Http/Controllers/Api/AlertController.php:93
 * @route '/api/alerts/thresholds/{threshold}'
 */
destroyForm.delete = (
    args:
        | { threshold: string | number | { id: string | number } }
        | [threshold: string | number | { id: string | number }]
        | string
        | number
        | { id: string | number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'post',
});

destroy.form = destroyForm;

const thresholds = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    show: Object.assign(show, show),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
};

export default thresholds;
