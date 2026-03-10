import {
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../wayfinder';
/**
 * @see routes/web.php:209
 * @route '/quality-hub'
 */
export const hub = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: hub.url(options),
    method: 'get',
});

hub.definition = {
    methods: ['get', 'head'],
    url: '/quality-hub',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see routes/web.php:209
 * @route '/quality-hub'
 */
hub.url = (options?: RouteQueryOptions) => {
    return hub.definition.url + queryParams(options);
};

/**
 * @see routes/web.php:209
 * @route '/quality-hub'
 */
hub.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: hub.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:209
 * @route '/quality-hub'
 */
hub.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: hub.url(options),
    method: 'head',
});

/**
 * @see routes/web.php:209
 * @route '/quality-hub'
 */
const hubForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: hub.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:209
 * @route '/quality-hub'
 */
hubForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: hub.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:209
 * @route '/quality-hub'
 */
hubForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: hub.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

hub.form = hubForm;

/**
 * @see routes/web.php:213
 * @route '/quality/ragas-metrics'
 */
export const ragasMetrics = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: ragasMetrics.url(options),
    method: 'get',
});

ragasMetrics.definition = {
    methods: ['get', 'head'],
    url: '/quality/ragas-metrics',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see routes/web.php:213
 * @route '/quality/ragas-metrics'
 */
ragasMetrics.url = (options?: RouteQueryOptions) => {
    return ragasMetrics.definition.url + queryParams(options);
};

/**
 * @see routes/web.php:213
 * @route '/quality/ragas-metrics'
 */
ragasMetrics.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ragasMetrics.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:213
 * @route '/quality/ragas-metrics'
 */
ragasMetrics.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ragasMetrics.url(options),
    method: 'head',
});

/**
 * @see routes/web.php:213
 * @route '/quality/ragas-metrics'
 */
const ragasMetricsForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: ragasMetrics.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:213
 * @route '/quality/ragas-metrics'
 */
ragasMetricsForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: ragasMetrics.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:213
 * @route '/quality/ragas-metrics'
 */
ragasMetricsForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: ragasMetrics.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

ragasMetrics.form = ragasMetricsForm;

const quality = {
    hub: Object.assign(hub, hub),
    ragasMetrics: Object.assign(ragasMetrics, ragasMetrics),
};

export default quality;
