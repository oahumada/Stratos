import {
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../wayfinder';
import auditLogs from './audit-logs';
/**
 * @see routes/web.php:45
 * @route '/admin/operations'
 */
export const operations = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: operations.url(options),
    method: 'get',
});

operations.definition = {
    methods: ['get', 'head'],
    url: '/admin/operations',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see routes/web.php:45
 * @route '/admin/operations'
 */
operations.url = (options?: RouteQueryOptions) => {
    return operations.definition.url + queryParams(options);
};

/**
 * @see routes/web.php:45
 * @route '/admin/operations'
 */
operations.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: operations.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:45
 * @route '/admin/operations'
 */
operations.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: operations.url(options),
    method: 'head',
});

/**
 * @see routes/web.php:45
 * @route '/admin/operations'
 */
const operationsForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: operations.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:45
 * @route '/admin/operations'
 */
operationsForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: operations.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:45
 * @route '/admin/operations'
 */
operationsForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: operations.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

operations.form = operationsForm;

/**
 * @see routes/web.php:50
 * @route '/admin/alert-configuration'
 */
export const alertConfiguration = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: alertConfiguration.url(options),
    method: 'get',
});

alertConfiguration.definition = {
    methods: ['get', 'head'],
    url: '/admin/alert-configuration',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see routes/web.php:50
 * @route '/admin/alert-configuration'
 */
alertConfiguration.url = (options?: RouteQueryOptions) => {
    return alertConfiguration.definition.url + queryParams(options);
};

/**
 * @see routes/web.php:50
 * @route '/admin/alert-configuration'
 */
alertConfiguration.get = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: alertConfiguration.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:50
 * @route '/admin/alert-configuration'
 */
alertConfiguration.head = (
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: alertConfiguration.url(options),
    method: 'head',
});

/**
 * @see routes/web.php:50
 * @route '/admin/alert-configuration'
 */
const alertConfigurationForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: alertConfiguration.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:50
 * @route '/admin/alert-configuration'
 */
alertConfigurationForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: alertConfiguration.url(options),
    method: 'get',
});

/**
 * @see routes/web.php:50
 * @route '/admin/alert-configuration'
 */
alertConfigurationForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: alertConfiguration.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

alertConfiguration.form = alertConfigurationForm;

const admin = {
    operations: Object.assign(operations, operations),
    auditLogs: Object.assign(auditLogs, auditLogs),
    alertConfiguration: Object.assign(alertConfiguration, alertConfiguration),
};

export default admin;
