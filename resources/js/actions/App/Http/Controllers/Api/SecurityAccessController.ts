import {
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../../../../wayfinder';
/**
 * @see \App\Http\Controllers\Api\SecurityAccessController::index
 * @see app/Http/Controllers/Api/SecurityAccessController.php:27
 * @route '/api/security/access-logs'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

index.definition = {
    methods: ['get', 'head'],
    url: '/api/security/access-logs',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\SecurityAccessController::index
 * @see app/Http/Controllers/Api/SecurityAccessController.php:27
 * @route '/api/security/access-logs'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\SecurityAccessController::index
 * @see app/Http/Controllers/Api/SecurityAccessController.php:27
 * @route '/api/security/access-logs'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\SecurityAccessController::index
 * @see app/Http/Controllers/Api/SecurityAccessController.php:27
 * @route '/api/security/access-logs'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\SecurityAccessController::index
 * @see app/Http/Controllers/Api/SecurityAccessController.php:27
 * @route '/api/security/access-logs'
 */
const indexForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\SecurityAccessController::index
 * @see app/Http/Controllers/Api/SecurityAccessController.php:27
 * @route '/api/security/access-logs'
 */
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\SecurityAccessController::index
 * @see app/Http/Controllers/Api/SecurityAccessController.php:27
 * @route '/api/security/access-logs'
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
 * @see \App\Http\Controllers\Api\SecurityAccessController::summary
 * @see app/Http/Controllers/Api/SecurityAccessController.php:55
 * @route '/api/security/access-logs/summary'
 */
export const summary = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: summary.url(options),
    method: 'get',
});

summary.definition = {
    methods: ['get', 'head'],
    url: '/api/security/access-logs/summary',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\SecurityAccessController::summary
 * @see app/Http/Controllers/Api/SecurityAccessController.php:55
 * @route '/api/security/access-logs/summary'
 */
summary.url = (options?: RouteQueryOptions) => {
    return summary.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\SecurityAccessController::summary
 * @see app/Http/Controllers/Api/SecurityAccessController.php:55
 * @route '/api/security/access-logs/summary'
 */
summary.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: summary.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\SecurityAccessController::summary
 * @see app/Http/Controllers/Api/SecurityAccessController.php:55
 * @route '/api/security/access-logs/summary'
 */
summary.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: summary.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\SecurityAccessController::summary
 * @see app/Http/Controllers/Api/SecurityAccessController.php:55
 * @route '/api/security/access-logs/summary'
 */
const summaryForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: summary.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\SecurityAccessController::summary
 * @see app/Http/Controllers/Api/SecurityAccessController.php:55
 * @route '/api/security/access-logs/summary'
 */
summaryForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: summary.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\SecurityAccessController::summary
 * @see app/Http/Controllers/Api/SecurityAccessController.php:55
 * @route '/api/security/access-logs/summary'
 */
summaryForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: summary.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

summary.form = summaryForm;

const SecurityAccessController = { index, summary };

export default SecurityAccessController;
