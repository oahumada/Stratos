import {
    applyUrlDefaults,
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../../../../wayfinder';
/**
 * @see \App\Http\Controllers\Api\PeopleProfileController::show
 * @see app/Http/Controllers/Api/PeopleProfileController.php:18
 * @route '/api/people/profile/{id}'
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
    url: '/api/people/profile/{id}',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\PeopleProfileController::show
 * @see app/Http/Controllers/Api/PeopleProfileController.php:18
 * @route '/api/people/profile/{id}'
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
 * @see \App\Http\Controllers\Api\PeopleProfileController::show
 * @see app/Http/Controllers/Api/PeopleProfileController.php:18
 * @route '/api/people/profile/{id}'
 */
show.get = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PeopleProfileController::show
 * @see app/Http/Controllers/Api/PeopleProfileController.php:18
 * @route '/api/people/profile/{id}'
 */
show.head = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\PeopleProfileController::show
 * @see app/Http/Controllers/Api/PeopleProfileController.php:18
 * @route '/api/people/profile/{id}'
 */
const showForm = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PeopleProfileController::show
 * @see app/Http/Controllers/Api/PeopleProfileController.php:18
 * @route '/api/people/profile/{id}'
 */
showForm.get = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PeopleProfileController::show
 * @see app/Http/Controllers/Api/PeopleProfileController.php:18
 * @route '/api/people/profile/{id}'
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
 * @see \App\Http\Controllers\Api\PeopleProfileController::getTimeline
 * @see app/Http/Controllers/Api/PeopleProfileController.php:99
 * @route '/api/people/profile/{id}/timeline'
 */
export const getTimeline = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getTimeline.url(args, options),
    method: 'get',
});

getTimeline.definition = {
    methods: ['get', 'head'],
    url: '/api/people/profile/{id}/timeline',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\PeopleProfileController::getTimeline
 * @see app/Http/Controllers/Api/PeopleProfileController.php:99
 * @route '/api/people/profile/{id}/timeline'
 */
getTimeline.url = (
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
        getTimeline.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\PeopleProfileController::getTimeline
 * @see app/Http/Controllers/Api/PeopleProfileController.php:99
 * @route '/api/people/profile/{id}/timeline'
 */
getTimeline.get = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getTimeline.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PeopleProfileController::getTimeline
 * @see app/Http/Controllers/Api/PeopleProfileController.php:99
 * @route '/api/people/profile/{id}/timeline'
 */
getTimeline.head = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: getTimeline.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\PeopleProfileController::getTimeline
 * @see app/Http/Controllers/Api/PeopleProfileController.php:99
 * @route '/api/people/profile/{id}/timeline'
 */
const getTimelineForm = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getTimeline.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PeopleProfileController::getTimeline
 * @see app/Http/Controllers/Api/PeopleProfileController.php:99
 * @route '/api/people/profile/{id}/timeline'
 */
getTimelineForm.get = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getTimeline.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PeopleProfileController::getTimeline
 * @see app/Http/Controllers/Api/PeopleProfileController.php:99
 * @route '/api/people/profile/{id}/timeline'
 */
getTimelineForm.head = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getTimeline.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

getTimeline.form = getTimelineForm;

const PeopleProfileController = { show, getTimeline };

export default PeopleProfileController;
