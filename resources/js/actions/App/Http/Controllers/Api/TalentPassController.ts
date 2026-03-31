import {
    applyUrlDefaults,
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../../../../wayfinder';
/**
 * @see \App\Http\Controllers\Api\TalentPassController::showPublic
 * @see app/Http/Controllers/Api/TalentPassController.php:35
 * @route '/api/talent-pass/{publicId}'
 */
export const showPublic = (
    args:
        | { publicId: string | number }
        | [publicId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: showPublic.url(args, options),
    method: 'get',
});

showPublic.definition = {
    methods: ['get', 'head'],
    url: '/api/talent-pass/{publicId}',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\TalentPassController::showPublic
 * @see app/Http/Controllers/Api/TalentPassController.php:35
 * @route '/api/talent-pass/{publicId}'
 */
showPublic.url = (
    args:
        | { publicId: string | number }
        | [publicId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { publicId: args };
    }

    if (Array.isArray(args)) {
        args = {
            publicId: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        publicId: args.publicId,
    };

    return (
        showPublic.definition.url
            .replace('{publicId}', parsedArgs.publicId.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\TalentPassController::showPublic
 * @see app/Http/Controllers/Api/TalentPassController.php:35
 * @route '/api/talent-pass/{publicId}'
 */
showPublic.get = (
    args:
        | { publicId: string | number }
        | [publicId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: showPublic.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::showPublic
 * @see app/Http/Controllers/Api/TalentPassController.php:35
 * @route '/api/talent-pass/{publicId}'
 */
showPublic.head = (
    args:
        | { publicId: string | number }
        | [publicId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: showPublic.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::showPublic
 * @see app/Http/Controllers/Api/TalentPassController.php:35
 * @route '/api/talent-pass/{publicId}'
 */
const showPublicForm = (
    args:
        | { publicId: string | number }
        | [publicId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: showPublic.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::showPublic
 * @see app/Http/Controllers/Api/TalentPassController.php:35
 * @route '/api/talent-pass/{publicId}'
 */
showPublicForm.get = (
    args:
        | { publicId: string | number }
        | [publicId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: showPublic.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::showPublic
 * @see app/Http/Controllers/Api/TalentPassController.php:35
 * @route '/api/talent-pass/{publicId}'
 */
showPublicForm.head = (
    args:
        | { publicId: string | number }
        | [publicId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: showPublic.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

showPublic.form = showPublicForm;

/**
 * @see \App\Http\Controllers\Api\TalentPassController::index
 * @see app/Http/Controllers/Api/TalentPassController.php:21
 * @route '/api/talent-passes'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

index.definition = {
    methods: ['get', 'head'],
    url: '/api/talent-passes',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\TalentPassController::index
 * @see app/Http/Controllers/Api/TalentPassController.php:21
 * @route '/api/talent-passes'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\TalentPassController::index
 * @see app/Http/Controllers/Api/TalentPassController.php:21
 * @route '/api/talent-passes'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::index
 * @see app/Http/Controllers/Api/TalentPassController.php:21
 * @route '/api/talent-passes'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::index
 * @see app/Http/Controllers/Api/TalentPassController.php:21
 * @route '/api/talent-passes'
 */
const indexForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::index
 * @see app/Http/Controllers/Api/TalentPassController.php:21
 * @route '/api/talent-passes'
 */
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::index
 * @see app/Http/Controllers/Api/TalentPassController.php:21
 * @route '/api/talent-passes'
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
 * @see \App\Http\Controllers\Api\TalentPassController::store
 * @see app/Http/Controllers/Api/TalentPassController.php:69
 * @route '/api/talent-passes'
 */
export const store = (
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
});

store.definition = {
    methods: ['post'],
    url: '/api/talent-passes',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\TalentPassController::store
 * @see app/Http/Controllers/Api/TalentPassController.php:69
 * @route '/api/talent-passes'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\TalentPassController::store
 * @see app/Http/Controllers/Api/TalentPassController.php:69
 * @route '/api/talent-passes'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::store
 * @see app/Http/Controllers/Api/TalentPassController.php:69
 * @route '/api/talent-passes'
 */
const storeForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::store
 * @see app/Http/Controllers/Api/TalentPassController.php:69
 * @route '/api/talent-passes'
 */
storeForm.post = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
});

store.form = storeForm;

/**
 * @see \App\Http\Controllers\Api\TalentPassController::show
 * @see app/Http/Controllers/Api/TalentPassController.php:55
 * @route '/api/talent-passes/{id}'
 */
const showa8ea459a676cdb42753a6a0543b531ab = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: showa8ea459a676cdb42753a6a0543b531ab.url(args, options),
    method: 'get',
});

showa8ea459a676cdb42753a6a0543b531ab.definition = {
    methods: ['get', 'head'],
    url: '/api/talent-passes/{id}',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\TalentPassController::show
 * @see app/Http/Controllers/Api/TalentPassController.php:55
 * @route '/api/talent-passes/{id}'
 */
showa8ea459a676cdb42753a6a0543b531ab.url = (
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
        showa8ea459a676cdb42753a6a0543b531ab.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\TalentPassController::show
 * @see app/Http/Controllers/Api/TalentPassController.php:55
 * @route '/api/talent-passes/{id}'
 */
showa8ea459a676cdb42753a6a0543b531ab.get = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: showa8ea459a676cdb42753a6a0543b531ab.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::show
 * @see app/Http/Controllers/Api/TalentPassController.php:55
 * @route '/api/talent-passes/{id}'
 */
showa8ea459a676cdb42753a6a0543b531ab.head = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: showa8ea459a676cdb42753a6a0543b531ab.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::show
 * @see app/Http/Controllers/Api/TalentPassController.php:55
 * @route '/api/talent-passes/{id}'
 */
const showa8ea459a676cdb42753a6a0543b531abForm = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: showa8ea459a676cdb42753a6a0543b531ab.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::show
 * @see app/Http/Controllers/Api/TalentPassController.php:55
 * @route '/api/talent-passes/{id}'
 */
showa8ea459a676cdb42753a6a0543b531abForm.get = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: showa8ea459a676cdb42753a6a0543b531ab.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::show
 * @see app/Http/Controllers/Api/TalentPassController.php:55
 * @route '/api/talent-passes/{id}'
 */
showa8ea459a676cdb42753a6a0543b531abForm.head = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: showa8ea459a676cdb42753a6a0543b531ab.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

showa8ea459a676cdb42753a6a0543b531ab.form =
    showa8ea459a676cdb42753a6a0543b531abForm;
/**
 * @see \App\Http\Controllers\Api\TalentPassController::show
 * @see app/Http/Controllers/Api/TalentPassController.php:55
 * @route '/api/people/{people_id}/talent-pass'
 */
const showc401fcb351b8beb578562905cc6306ac = (
    args:
        | { people_id: string | number }
        | [people_id: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: showc401fcb351b8beb578562905cc6306ac.url(args, options),
    method: 'get',
});

showc401fcb351b8beb578562905cc6306ac.definition = {
    methods: ['get', 'head'],
    url: '/api/people/{people_id}/talent-pass',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\TalentPassController::show
 * @see app/Http/Controllers/Api/TalentPassController.php:55
 * @route '/api/people/{people_id}/talent-pass'
 */
showc401fcb351b8beb578562905cc6306ac.url = (
    args:
        | { people_id: string | number }
        | [people_id: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { people_id: args };
    }

    if (Array.isArray(args)) {
        args = {
            people_id: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        people_id: args.people_id,
    };

    return (
        showc401fcb351b8beb578562905cc6306ac.definition.url
            .replace('{people_id}', parsedArgs.people_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\TalentPassController::show
 * @see app/Http/Controllers/Api/TalentPassController.php:55
 * @route '/api/people/{people_id}/talent-pass'
 */
showc401fcb351b8beb578562905cc6306ac.get = (
    args:
        | { people_id: string | number }
        | [people_id: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: showc401fcb351b8beb578562905cc6306ac.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::show
 * @see app/Http/Controllers/Api/TalentPassController.php:55
 * @route '/api/people/{people_id}/talent-pass'
 */
showc401fcb351b8beb578562905cc6306ac.head = (
    args:
        | { people_id: string | number }
        | [people_id: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: showc401fcb351b8beb578562905cc6306ac.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::show
 * @see app/Http/Controllers/Api/TalentPassController.php:55
 * @route '/api/people/{people_id}/talent-pass'
 */
const showc401fcb351b8beb578562905cc6306acForm = (
    args:
        | { people_id: string | number }
        | [people_id: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: showc401fcb351b8beb578562905cc6306ac.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::show
 * @see app/Http/Controllers/Api/TalentPassController.php:55
 * @route '/api/people/{people_id}/talent-pass'
 */
showc401fcb351b8beb578562905cc6306acForm.get = (
    args:
        | { people_id: string | number }
        | [people_id: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: showc401fcb351b8beb578562905cc6306ac.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::show
 * @see app/Http/Controllers/Api/TalentPassController.php:55
 * @route '/api/people/{people_id}/talent-pass'
 */
showc401fcb351b8beb578562905cc6306acForm.head = (
    args:
        | { people_id: string | number }
        | [people_id: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: showc401fcb351b8beb578562905cc6306ac.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

showc401fcb351b8beb578562905cc6306ac.form =
    showc401fcb351b8beb578562905cc6306acForm;

export const show = {
    '/api/talent-passes/{id}': showa8ea459a676cdb42753a6a0543b531ab,
    '/api/people/{people_id}/talent-pass': showc401fcb351b8beb578562905cc6306ac,
};

/**
 * @see \App\Http\Controllers\Api\TalentPassController::update
 * @see app/Http/Controllers/Api/TalentPassController.php:92
 * @route '/api/talent-passes/{id}'
 */
export const update = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
});

update.definition = {
    methods: ['put'],
    url: '/api/talent-passes/{id}',
} satisfies RouteDefinition<['put']>;

/**
 * @see \App\Http\Controllers\Api\TalentPassController::update
 * @see app/Http/Controllers/Api/TalentPassController.php:92
 * @route '/api/talent-passes/{id}'
 */
update.url = (
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
        update.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\TalentPassController::update
 * @see app/Http/Controllers/Api/TalentPassController.php:92
 * @route '/api/talent-passes/{id}'
 */
update.put = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::update
 * @see app/Http/Controllers/Api/TalentPassController.php:92
 * @route '/api/talent-passes/{id}'
 */
const updateForm = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::update
 * @see app/Http/Controllers/Api/TalentPassController.php:92
 * @route '/api/talent-passes/{id}'
 */
updateForm.put = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'post',
});

update.form = updateForm;

/**
 * @see \App\Http\Controllers\Api\TalentPassController::destroy
 * @see app/Http/Controllers/Api/TalentPassController.php:111
 * @route '/api/talent-passes/{id}'
 */
export const destroy = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
});

destroy.definition = {
    methods: ['delete'],
    url: '/api/talent-passes/{id}',
} satisfies RouteDefinition<['delete']>;

/**
 * @see \App\Http\Controllers\Api\TalentPassController::destroy
 * @see app/Http/Controllers/Api/TalentPassController.php:111
 * @route '/api/talent-passes/{id}'
 */
destroy.url = (
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
        destroy.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\TalentPassController::destroy
 * @see app/Http/Controllers/Api/TalentPassController.php:111
 * @route '/api/talent-passes/{id}'
 */
destroy.delete = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::destroy
 * @see app/Http/Controllers/Api/TalentPassController.php:111
 * @route '/api/talent-passes/{id}'
 */
const destroyForm = (
    args: { id: string | number } | [id: string | number] | string | number,
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
 * @see \App\Http\Controllers\Api\TalentPassController::destroy
 * @see app/Http/Controllers/Api/TalentPassController.php:111
 * @route '/api/talent-passes/{id}'
 */
destroyForm.delete = (
    args: { id: string | number } | [id: string | number] | string | number,
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

/**
 * @see \App\Http\Controllers\Api\TalentPassController::publish
 * @see app/Http/Controllers/Api/TalentPassController.php:124
 * @route '/api/talent-passes/{id}/publish'
 */
export const publish = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: publish.url(args, options),
    method: 'post',
});

publish.definition = {
    methods: ['post'],
    url: '/api/talent-passes/{id}/publish',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\TalentPassController::publish
 * @see app/Http/Controllers/Api/TalentPassController.php:124
 * @route '/api/talent-passes/{id}/publish'
 */
publish.url = (
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
        publish.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\TalentPassController::publish
 * @see app/Http/Controllers/Api/TalentPassController.php:124
 * @route '/api/talent-passes/{id}/publish'
 */
publish.post = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: publish.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::publish
 * @see app/Http/Controllers/Api/TalentPassController.php:124
 * @route '/api/talent-passes/{id}/publish'
 */
const publishForm = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: publish.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::publish
 * @see app/Http/Controllers/Api/TalentPassController.php:124
 * @route '/api/talent-passes/{id}/publish'
 */
publishForm.post = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: publish.url(args, options),
    method: 'post',
});

publish.form = publishForm;

/**
 * @see \App\Http\Controllers\Api\TalentPassController::archive
 * @see app/Http/Controllers/Api/TalentPassController.php:137
 * @route '/api/talent-passes/{id}/archive'
 */
export const archive = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: archive.url(args, options),
    method: 'post',
});

archive.definition = {
    methods: ['post'],
    url: '/api/talent-passes/{id}/archive',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\TalentPassController::archive
 * @see app/Http/Controllers/Api/TalentPassController.php:137
 * @route '/api/talent-passes/{id}/archive'
 */
archive.url = (
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
        archive.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\TalentPassController::archive
 * @see app/Http/Controllers/Api/TalentPassController.php:137
 * @route '/api/talent-passes/{id}/archive'
 */
archive.post = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: archive.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::archive
 * @see app/Http/Controllers/Api/TalentPassController.php:137
 * @route '/api/talent-passes/{id}/archive'
 */
const archiveForm = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: archive.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::archive
 * @see app/Http/Controllers/Api/TalentPassController.php:137
 * @route '/api/talent-passes/{id}/archive'
 */
archiveForm.post = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: archive.url(args, options),
    method: 'post',
});

archive.form = archiveForm;

/**
 * @see \App\Http\Controllers\Api\TalentPassController::clone
 * @see app/Http/Controllers/Api/TalentPassController.php:150
 * @route '/api/talent-passes/{id}/clone'
 */
export const clone = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: clone.url(args, options),
    method: 'post',
});

clone.definition = {
    methods: ['post'],
    url: '/api/talent-passes/{id}/clone',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\TalentPassController::clone
 * @see app/Http/Controllers/Api/TalentPassController.php:150
 * @route '/api/talent-passes/{id}/clone'
 */
clone.url = (
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
        clone.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\TalentPassController::clone
 * @see app/Http/Controllers/Api/TalentPassController.php:150
 * @route '/api/talent-passes/{id}/clone'
 */
clone.post = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: clone.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::clone
 * @see app/Http/Controllers/Api/TalentPassController.php:150
 * @route '/api/talent-passes/{id}/clone'
 */
const cloneForm = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: clone.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::clone
 * @see app/Http/Controllers/Api/TalentPassController.php:150
 * @route '/api/talent-passes/{id}/clone'
 */
cloneForm.post = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: clone.url(args, options),
    method: 'post',
});

clone.form = cloneForm;

/**
 * @see \App\Http\Controllers\Api\TalentPassController::exportMethod
 * @see app/Http/Controllers/Api/TalentPassController.php:163
 * @route '/api/talent-passes/{id}/export'
 */
export const exportMethod = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: exportMethod.url(args, options),
    method: 'get',
});

exportMethod.definition = {
    methods: ['get', 'head'],
    url: '/api/talent-passes/{id}/export',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\TalentPassController::exportMethod
 * @see app/Http/Controllers/Api/TalentPassController.php:163
 * @route '/api/talent-passes/{id}/export'
 */
exportMethod.url = (
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
        exportMethod.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\TalentPassController::exportMethod
 * @see app/Http/Controllers/Api/TalentPassController.php:163
 * @route '/api/talent-passes/{id}/export'
 */
exportMethod.get = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: exportMethod.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::exportMethod
 * @see app/Http/Controllers/Api/TalentPassController.php:163
 * @route '/api/talent-passes/{id}/export'
 */
exportMethod.head = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: exportMethod.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::exportMethod
 * @see app/Http/Controllers/Api/TalentPassController.php:163
 * @route '/api/talent-passes/{id}/export'
 */
const exportMethodForm = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::exportMethod
 * @see app/Http/Controllers/Api/TalentPassController.php:163
 * @route '/api/talent-passes/{id}/export'
 */
exportMethodForm.get = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::exportMethod
 * @see app/Http/Controllers/Api/TalentPassController.php:163
 * @route '/api/talent-passes/{id}/export'
 */
exportMethodForm.head = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

exportMethod.form = exportMethodForm;

/**
 * @see \App\Http\Controllers\Api\TalentPassController::share
 * @see app/Http/Controllers/Api/TalentPassController.php:184
 * @route '/api/talent-passes/{id}/share'
 */
export const share = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: share.url(args, options),
    method: 'post',
});

share.definition = {
    methods: ['post'],
    url: '/api/talent-passes/{id}/share',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\TalentPassController::share
 * @see app/Http/Controllers/Api/TalentPassController.php:184
 * @route '/api/talent-passes/{id}/share'
 */
share.url = (
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
        share.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\TalentPassController::share
 * @see app/Http/Controllers/Api/TalentPassController.php:184
 * @route '/api/talent-passes/{id}/share'
 */
share.post = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: share.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::share
 * @see app/Http/Controllers/Api/TalentPassController.php:184
 * @route '/api/talent-passes/{id}/share'
 */
const shareForm = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: share.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::share
 * @see app/Http/Controllers/Api/TalentPassController.php:184
 * @route '/api/talent-passes/{id}/share'
 */
shareForm.post = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: share.url(args, options),
    method: 'post',
});

share.form = shareForm;

/**
 * @see \App\Http\Controllers\Api\TalentPassController::generateCredential
 * @see app/Http/Controllers/Api/TalentPassController.php:0
 * @route '/api/people/{people_id}/talent-pass/issue'
 */
export const generateCredential = (
    args:
        | { people_id: string | number }
        | [people_id: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: generateCredential.url(args, options),
    method: 'post',
});

generateCredential.definition = {
    methods: ['post'],
    url: '/api/people/{people_id}/talent-pass/issue',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\TalentPassController::generateCredential
 * @see app/Http/Controllers/Api/TalentPassController.php:0
 * @route '/api/people/{people_id}/talent-pass/issue'
 */
generateCredential.url = (
    args:
        | { people_id: string | number }
        | [people_id: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { people_id: args };
    }

    if (Array.isArray(args)) {
        args = {
            people_id: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        people_id: args.people_id,
    };

    return (
        generateCredential.definition.url
            .replace('{people_id}', parsedArgs.people_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\TalentPassController::generateCredential
 * @see app/Http/Controllers/Api/TalentPassController.php:0
 * @route '/api/people/{people_id}/talent-pass/issue'
 */
generateCredential.post = (
    args:
        | { people_id: string | number }
        | [people_id: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: generateCredential.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::generateCredential
 * @see app/Http/Controllers/Api/TalentPassController.php:0
 * @route '/api/people/{people_id}/talent-pass/issue'
 */
const generateCredentialForm = (
    args:
        | { people_id: string | number }
        | [people_id: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: generateCredential.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\TalentPassController::generateCredential
 * @see app/Http/Controllers/Api/TalentPassController.php:0
 * @route '/api/people/{people_id}/talent-pass/issue'
 */
generateCredentialForm.post = (
    args:
        | { people_id: string | number }
        | [people_id: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: generateCredential.url(args, options),
    method: 'post',
});

generateCredential.form = generateCredentialForm;

const TalentPassController = {
    showPublic,
    index,
    store,
    show,
    update,
    destroy,
    publish,
    archive,
    clone,
    exportMethod,
    share,
    generateCredential,
    export: exportMethod,
};

export default TalentPassController;
