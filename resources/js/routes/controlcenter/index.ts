import {
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../wayfinder';
/**
 * @see routes/web.php:32
 * @route '/controlcenter'
 */
export const landing = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: landing.url(options),
    method: 'get',
});

landing.definition = {
    methods: ['get', 'head'],
    url: '/controlcenter',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see routes/web.php:32
 * @route '/controlcenter'
 */
landing.url = (options?: RouteQueryOptions) => {
    return landing.definition.url + queryParams(options);
};

/**
 * @see routes/web.php:32
 * @route '/controlcenter'
 */
landing.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: landing.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:32
 * @route '/controlcenter'
 */
landing.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: landing.url(options),
    method: 'head',
});

/**
 * @see routes/web.php:32
 * @route '/controlcenter'
 */
const landingForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: landing.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:32
 * @route '/controlcenter'
 */
landingForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: landing.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:32
 * @route '/controlcenter'
 */
landingForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: landing.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

landing.form = landingForm;

/**
 * @see routes/web.php:36
 * @route '/controlcenter/culture'
 */
export const culture = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: culture.url(options),
    method: 'get',
});

culture.definition = {
    methods: ['get', 'head'],
    url: '/controlcenter/culture',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see routes/web.php:36
 * @route '/controlcenter/culture'
 */
culture.url = (options?: RouteQueryOptions) => {
    return culture.definition.url + queryParams(options);
};

/**
 * @see routes/web.php:36
 * @route '/controlcenter/culture'
 */
culture.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: culture.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:36
 * @route '/controlcenter/culture'
 */
culture.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: culture.url(options),
    method: 'head',
});

/**
 * @see routes/web.php:36
 * @route '/controlcenter/culture'
 */
const cultureForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: culture.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:36
 * @route '/controlcenter/culture'
 */
cultureForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: culture.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:36
 * @route '/controlcenter/culture'
 */
cultureForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: culture.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

culture.form = cultureForm;

/**
 * @see routes/web.php:40
 * @route '/controlcenter/culture-analytics'
 */
export const cultureAnalytics = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: cultureAnalytics.url(options),
    method: 'get',
});

cultureAnalytics.definition = {
    methods: ['get', 'head'],
    url: '/controlcenter/culture-analytics',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see routes/web.php:40
 * @route '/controlcenter/culture-analytics'
 */
cultureAnalytics.url = (options?: RouteQueryOptions) => {
    return cultureAnalytics.definition.url + queryParams(options);
};

/**
 * @see routes/web.php:40
 * @route '/controlcenter/culture-analytics'
 */
cultureAnalytics.get = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: cultureAnalytics.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:40
 * @route '/controlcenter/culture-analytics'
 */
cultureAnalytics.head = (
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: cultureAnalytics.url(options),
    method: 'head',
});

/**
 * @see routes/web.php:40
 * @route '/controlcenter/culture-analytics'
 */
const cultureAnalyticsForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: cultureAnalytics.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:40
 * @route '/controlcenter/culture-analytics'
 */
cultureAnalyticsForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: cultureAnalytics.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:40
 * @route '/controlcenter/culture-analytics'
 */
cultureAnalyticsForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: cultureAnalytics.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

cultureAnalytics.form = cultureAnalyticsForm;

const controlcenter = {
    landing: Object.assign(landing, landing),
    culture: Object.assign(culture, culture),
    cultureAnalytics: Object.assign(cultureAnalytics, cultureAnalytics),
};

export default controlcenter;
