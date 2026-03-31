import {
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../wayfinder';
import verification from './verification';
import verificationConfigF9f1e9 from './verification-config';
import verificationMetricsAcc03a from './verification-metrics';
/**
 * @see \App\Http\Controllers\Deployment\VerificationConfigurationController::verificationConfig
 * @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:12
 * @route '/deployment/verification-config'
 */
export const verificationConfig = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: verificationConfig.url(options),
    method: 'get',
});

verificationConfig.definition = {
    methods: ['get', 'head'],
    url: '/deployment/verification-config',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Deployment\VerificationConfigurationController::verificationConfig
 * @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:12
 * @route '/deployment/verification-config'
 */
verificationConfig.url = (options?: RouteQueryOptions) => {
    return verificationConfig.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Deployment\VerificationConfigurationController::verificationConfig
 * @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:12
 * @route '/deployment/verification-config'
 */
verificationConfig.get = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: verificationConfig.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Deployment\VerificationConfigurationController::verificationConfig
 * @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:12
 * @route '/deployment/verification-config'
 */
verificationConfig.head = (
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: verificationConfig.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Deployment\VerificationConfigurationController::verificationConfig
 * @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:12
 * @route '/deployment/verification-config'
 */
const verificationConfigForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: verificationConfig.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Deployment\VerificationConfigurationController::verificationConfig
 * @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:12
 * @route '/deployment/verification-config'
 */
verificationConfigForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: verificationConfig.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Deployment\VerificationConfigurationController::verificationConfig
 * @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:12
 * @route '/deployment/verification-config'
 */
verificationConfigForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: verificationConfig.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

verificationConfig.form = verificationConfigForm;

/**
 * @see \App\Http\Controllers\Deployment\VerificationConfigurationController::verificationStatus
 * @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:52
 * @route '/api/deployment/verification-status'
 */
export const verificationStatus = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: verificationStatus.url(options),
    method: 'get',
});

verificationStatus.definition = {
    methods: ['get', 'head'],
    url: '/api/deployment/verification-status',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Deployment\VerificationConfigurationController::verificationStatus
 * @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:52
 * @route '/api/deployment/verification-status'
 */
verificationStatus.url = (options?: RouteQueryOptions) => {
    return verificationStatus.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Deployment\VerificationConfigurationController::verificationStatus
 * @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:52
 * @route '/api/deployment/verification-status'
 */
verificationStatus.get = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: verificationStatus.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Deployment\VerificationConfigurationController::verificationStatus
 * @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:52
 * @route '/api/deployment/verification-status'
 */
verificationStatus.head = (
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: verificationStatus.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Deployment\VerificationConfigurationController::verificationStatus
 * @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:52
 * @route '/api/deployment/verification-status'
 */
const verificationStatusForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: verificationStatus.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Deployment\VerificationConfigurationController::verificationStatus
 * @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:52
 * @route '/api/deployment/verification-status'
 */
verificationStatusForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: verificationStatus.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Deployment\VerificationConfigurationController::verificationStatus
 * @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:52
 * @route '/api/deployment/verification-status'
 */
verificationStatusForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: verificationStatus.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

verificationStatus.form = verificationStatusForm;

/**
 * @see \Inertia\Controller::__invoke
 * @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
 * @route '/deployment/verification-metrics'
 */
export const verificationMetrics = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: verificationMetrics.url(options),
    method: 'get',
});

verificationMetrics.definition = {
    methods: ['get', 'head'],
    url: '/deployment/verification-metrics',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \Inertia\Controller::__invoke
 * @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
 * @route '/deployment/verification-metrics'
 */
verificationMetrics.url = (options?: RouteQueryOptions) => {
    return verificationMetrics.definition.url + queryParams(options);
};

/**
 * @see \Inertia\Controller::__invoke
 * @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
 * @route '/deployment/verification-metrics'
 */
verificationMetrics.get = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: verificationMetrics.url(options),
    method: 'get',
});

/**
 * @see \Inertia\Controller::__invoke
 * @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
 * @route '/deployment/verification-metrics'
 */
verificationMetrics.head = (
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: verificationMetrics.url(options),
    method: 'head',
});

/**
 * @see \Inertia\Controller::__invoke
 * @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
 * @route '/deployment/verification-metrics'
 */
const verificationMetricsForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: verificationMetrics.url(options),
    method: 'get',
});

/**
 * @see \Inertia\Controller::__invoke
 * @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
 * @route '/deployment/verification-metrics'
 */
verificationMetricsForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: verificationMetrics.url(options),
    method: 'get',
});

/**
 * @see \Inertia\Controller::__invoke
 * @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
 * @route '/deployment/verification-metrics'
 */
verificationMetricsForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: verificationMetrics.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

verificationMetrics.form = verificationMetricsForm;

/**
 * @see \Inertia\Controller::__invoke
 * @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
 * @route '/deployment/verification-hub'
 */
export const verificationHub = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: verificationHub.url(options),
    method: 'get',
});

verificationHub.definition = {
    methods: ['get', 'head'],
    url: '/deployment/verification-hub',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \Inertia\Controller::__invoke
 * @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
 * @route '/deployment/verification-hub'
 */
verificationHub.url = (options?: RouteQueryOptions) => {
    return verificationHub.definition.url + queryParams(options);
};

/**
 * @see \Inertia\Controller::__invoke
 * @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
 * @route '/deployment/verification-hub'
 */
verificationHub.get = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: verificationHub.url(options),
    method: 'get',
});

/**
 * @see \Inertia\Controller::__invoke
 * @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
 * @route '/deployment/verification-hub'
 */
verificationHub.head = (
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: verificationHub.url(options),
    method: 'head',
});

/**
 * @see \Inertia\Controller::__invoke
 * @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
 * @route '/deployment/verification-hub'
 */
const verificationHubForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: verificationHub.url(options),
    method: 'get',
});

/**
 * @see \Inertia\Controller::__invoke
 * @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
 * @route '/deployment/verification-hub'
 */
verificationHubForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: verificationHub.url(options),
    method: 'get',
});

/**
 * @see \Inertia\Controller::__invoke
 * @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
 * @route '/deployment/verification-hub'
 */
verificationHubForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: verificationHub.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

verificationHub.form = verificationHubForm;

const deployment = {
    verificationConfig: Object.assign(
        verificationConfig,
        verificationConfigF9f1e9,
    ),
    verificationStatus: Object.assign(verificationStatus, verificationStatus),
    verificationMetrics: Object.assign(
        verificationMetrics,
        verificationMetricsAcc03a,
    ),
    verificationHub: Object.assign(verificationHub, verificationHub),
    verification: Object.assign(verification, verification),
};

export default deployment;
