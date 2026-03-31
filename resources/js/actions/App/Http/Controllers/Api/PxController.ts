import {
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../../../../wayfinder';
/**
 * @see \App\Http\Controllers\Api\PxController::index
 * @see app/Http/Controllers/Api/PxController.php:22
 * @route '/api/px/campaigns'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

index.definition = {
    methods: ['get', 'head'],
    url: '/api/px/campaigns',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\PxController::index
 * @see app/Http/Controllers/Api/PxController.php:22
 * @route '/api/px/campaigns'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\PxController::index
 * @see app/Http/Controllers/Api/PxController.php:22
 * @route '/api/px/campaigns'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PxController::index
 * @see app/Http/Controllers/Api/PxController.php:22
 * @route '/api/px/campaigns'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\PxController::index
 * @see app/Http/Controllers/Api/PxController.php:22
 * @route '/api/px/campaigns'
 */
const indexForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PxController::index
 * @see app/Http/Controllers/Api/PxController.php:22
 * @route '/api/px/campaigns'
 */
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PxController::index
 * @see app/Http/Controllers/Api/PxController.php:22
 * @route '/api/px/campaigns'
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
 * @see \App\Http\Controllers\Api\PxController::trigger
 * @see app/Http/Controllers/Api/PxController.php:36
 * @route '/api/px/campaigns/trigger'
 */
export const trigger = (
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: trigger.url(options),
    method: 'post',
});

trigger.definition = {
    methods: ['post'],
    url: '/api/px/campaigns/trigger',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\PxController::trigger
 * @see app/Http/Controllers/Api/PxController.php:36
 * @route '/api/px/campaigns/trigger'
 */
trigger.url = (options?: RouteQueryOptions) => {
    return trigger.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\PxController::trigger
 * @see app/Http/Controllers/Api/PxController.php:36
 * @route '/api/px/campaigns/trigger'
 */
trigger.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: trigger.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\PxController::trigger
 * @see app/Http/Controllers/Api/PxController.php:36
 * @route '/api/px/campaigns/trigger'
 */
const triggerForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: trigger.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\PxController::trigger
 * @see app/Http/Controllers/Api/PxController.php:36
 * @route '/api/px/campaigns/trigger'
 */
triggerForm.post = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: trigger.url(options),
    method: 'post',
});

trigger.form = triggerForm;

const PxController = { index, trigger };

export default PxController;
