import {
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../wayfinder';
/**
 * @see routes/web.php:79
 * @route '/dashboard/analytics'
 */
export const analytics = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: analytics.url(options),
    method: 'get',
});

analytics.definition = {
    methods: ['get', 'head'],
    url: '/dashboard/analytics',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see routes/web.php:79
 * @route '/dashboard/analytics'
 */
analytics.url = (options?: RouteQueryOptions) => {
    return analytics.definition.url + queryParams(options);
};

/**
 * @see routes/web.php:79
 * @route '/dashboard/analytics'
 */
analytics.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: analytics.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:79
 * @route '/dashboard/analytics'
 */
analytics.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: analytics.url(options),
    method: 'head',
});

/**
 * @see routes/web.php:79
 * @route '/dashboard/analytics'
 */
const analyticsForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: analytics.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:79
 * @route '/dashboard/analytics'
 */
analyticsForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: analytics.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:79
 * @route '/dashboard/analytics'
 */
analyticsForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: analytics.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

analytics.form = analyticsForm;

/**
 * @see routes/web.php:83
 * @route '/dashboard/investor'
 */
export const investor = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: investor.url(options),
    method: 'get',
});

investor.definition = {
    methods: ['get', 'head'],
    url: '/dashboard/investor',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see routes/web.php:83
 * @route '/dashboard/investor'
 */
investor.url = (options?: RouteQueryOptions) => {
    return investor.definition.url + queryParams(options);
};

/**
 * @see routes/web.php:83
 * @route '/dashboard/investor'
 */
investor.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: investor.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:83
 * @route '/dashboard/investor'
 */
investor.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: investor.url(options),
    method: 'head',
});

/**
 * @see routes/web.php:83
 * @route '/dashboard/investor'
 */
const investorForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: investor.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:83
 * @route '/dashboard/investor'
 */
investorForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: investor.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:83
 * @route '/dashboard/investor'
 */
investorForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: investor.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

investor.form = investorForm;

const dashboard = {
    analytics: Object.assign(analytics, analytics),
    investor: Object.assign(investor, investor),
};

export default dashboard;
