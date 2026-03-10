import {
    applyUrlDefaults,
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../../../../wayfinder';
/**
 * @see \App\Http\Controllers\Api\PulseController::index
 * @see app/Http/Controllers/Api/PulseController.php:17
 * @route '/api/pulse-surveys'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

index.definition = {
    methods: ['get', 'head'],
    url: '/api/pulse-surveys',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\PulseController::index
 * @see app/Http/Controllers/Api/PulseController.php:17
 * @route '/api/pulse-surveys'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\PulseController::index
 * @see app/Http/Controllers/Api/PulseController.php:17
 * @route '/api/pulse-surveys'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::index
 * @see app/Http/Controllers/Api/PulseController.php:17
 * @route '/api/pulse-surveys'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::index
 * @see app/Http/Controllers/Api/PulseController.php:17
 * @route '/api/pulse-surveys'
 */
const indexForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::index
 * @see app/Http/Controllers/Api/PulseController.php:17
 * @route '/api/pulse-surveys'
 */
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::index
 * @see app/Http/Controllers/Api/PulseController.php:17
 * @route '/api/pulse-surveys'
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
 * @see \App\Http\Controllers\Api\PulseController::show
 * @see app/Http/Controllers/Api/PulseController.php:30
 * @route '/api/pulse-surveys/{id}'
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
    url: '/api/pulse-surveys/{id}',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\PulseController::show
 * @see app/Http/Controllers/Api/PulseController.php:30
 * @route '/api/pulse-surveys/{id}'
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
 * @see \App\Http\Controllers\Api\PulseController::show
 * @see app/Http/Controllers/Api/PulseController.php:30
 * @route '/api/pulse-surveys/{id}'
 */
show.get = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::show
 * @see app/Http/Controllers/Api/PulseController.php:30
 * @route '/api/pulse-surveys/{id}'
 */
show.head = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::show
 * @see app/Http/Controllers/Api/PulseController.php:30
 * @route '/api/pulse-surveys/{id}'
 */
const showForm = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::show
 * @see app/Http/Controllers/Api/PulseController.php:30
 * @route '/api/pulse-surveys/{id}'
 */
showForm.get = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::show
 * @see app/Http/Controllers/Api/PulseController.php:30
 * @route '/api/pulse-surveys/{id}'
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
 * @see \App\Http\Controllers\Api\PulseController::storeResponse
 * @see app/Http/Controllers/Api/PulseController.php:40
 * @route '/api/pulse-responses'
 */
export const storeResponse = (
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: storeResponse.url(options),
    method: 'post',
});

storeResponse.definition = {
    methods: ['post'],
    url: '/api/pulse-responses',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\PulseController::storeResponse
 * @see app/Http/Controllers/Api/PulseController.php:40
 * @route '/api/pulse-responses'
 */
storeResponse.url = (options?: RouteQueryOptions) => {
    return storeResponse.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\PulseController::storeResponse
 * @see app/Http/Controllers/Api/PulseController.php:40
 * @route '/api/pulse-responses'
 */
storeResponse.post = (
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: storeResponse.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::storeResponse
 * @see app/Http/Controllers/Api/PulseController.php:40
 * @route '/api/pulse-responses'
 */
const storeResponseForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: storeResponse.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::storeResponse
 * @see app/Http/Controllers/Api/PulseController.php:40
 * @route '/api/pulse-responses'
 */
storeResponseForm.post = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: storeResponse.url(options),
    method: 'post',
});

storeResponse.form = storeResponseForm;

/**
 * @see \App\Http\Controllers\Api\PulseController::healthScan
 * @see app/Http/Controllers/Api/PulseController.php:117
 * @route '/api/pulse/health-scan'
 */
export const healthScan = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: healthScan.url(options),
    method: 'get',
});

healthScan.definition = {
    methods: ['get', 'head'],
    url: '/api/pulse/health-scan',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\PulseController::healthScan
 * @see app/Http/Controllers/Api/PulseController.php:117
 * @route '/api/pulse/health-scan'
 */
healthScan.url = (options?: RouteQueryOptions) => {
    return healthScan.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\PulseController::healthScan
 * @see app/Http/Controllers/Api/PulseController.php:117
 * @route '/api/pulse/health-scan'
 */
healthScan.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: healthScan.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::healthScan
 * @see app/Http/Controllers/Api/PulseController.php:117
 * @route '/api/pulse/health-scan'
 */
healthScan.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: healthScan.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::healthScan
 * @see app/Http/Controllers/Api/PulseController.php:117
 * @route '/api/pulse/health-scan'
 */
const healthScanForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: healthScan.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::healthScan
 * @see app/Http/Controllers/Api/PulseController.php:117
 * @route '/api/pulse/health-scan'
 */
healthScanForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: healthScan.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::healthScan
 * @see app/Http/Controllers/Api/PulseController.php:117
 * @route '/api/pulse/health-scan'
 */
healthScanForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: healthScan.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

healthScan.form = healthScanForm;

/**
 * @see \App\Http\Controllers\Api\PulseController::storeEmployeePulse
 * @see app/Http/Controllers/Api/PulseController.php:67
 * @route '/api/people-experience/employee-pulses'
 */
export const storeEmployeePulse = (
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: storeEmployeePulse.url(options),
    method: 'post',
});

storeEmployeePulse.definition = {
    methods: ['post'],
    url: '/api/people-experience/employee-pulses',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\PulseController::storeEmployeePulse
 * @see app/Http/Controllers/Api/PulseController.php:67
 * @route '/api/people-experience/employee-pulses'
 */
storeEmployeePulse.url = (options?: RouteQueryOptions) => {
    return storeEmployeePulse.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\PulseController::storeEmployeePulse
 * @see app/Http/Controllers/Api/PulseController.php:67
 * @route '/api/people-experience/employee-pulses'
 */
storeEmployeePulse.post = (
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: storeEmployeePulse.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::storeEmployeePulse
 * @see app/Http/Controllers/Api/PulseController.php:67
 * @route '/api/people-experience/employee-pulses'
 */
const storeEmployeePulseForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: storeEmployeePulse.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::storeEmployeePulse
 * @see app/Http/Controllers/Api/PulseController.php:67
 * @route '/api/people-experience/employee-pulses'
 */
storeEmployeePulseForm.post = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: storeEmployeePulse.url(options),
    method: 'post',
});

storeEmployeePulse.form = storeEmployeePulseForm;

/**
 * @see \App\Http\Controllers\Api\PulseController::listEmployeePulses
 * @see app/Http/Controllers/Api/PulseController.php:136
 * @route '/api/people-experience/employee-pulses'
 */
export const listEmployeePulses = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: listEmployeePulses.url(options),
    method: 'get',
});

listEmployeePulses.definition = {
    methods: ['get', 'head'],
    url: '/api/people-experience/employee-pulses',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\PulseController::listEmployeePulses
 * @see app/Http/Controllers/Api/PulseController.php:136
 * @route '/api/people-experience/employee-pulses'
 */
listEmployeePulses.url = (options?: RouteQueryOptions) => {
    return listEmployeePulses.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\PulseController::listEmployeePulses
 * @see app/Http/Controllers/Api/PulseController.php:136
 * @route '/api/people-experience/employee-pulses'
 */
listEmployeePulses.get = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: listEmployeePulses.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::listEmployeePulses
 * @see app/Http/Controllers/Api/PulseController.php:136
 * @route '/api/people-experience/employee-pulses'
 */
listEmployeePulses.head = (
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: listEmployeePulses.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::listEmployeePulses
 * @see app/Http/Controllers/Api/PulseController.php:136
 * @route '/api/people-experience/employee-pulses'
 */
const listEmployeePulsesForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: listEmployeePulses.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::listEmployeePulses
 * @see app/Http/Controllers/Api/PulseController.php:136
 * @route '/api/people-experience/employee-pulses'
 */
listEmployeePulsesForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: listEmployeePulses.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::listEmployeePulses
 * @see app/Http/Controllers/Api/PulseController.php:136
 * @route '/api/people-experience/employee-pulses'
 */
listEmployeePulsesForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: listEmployeePulses.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

listEmployeePulses.form = listEmployeePulsesForm;

/**
 * @see \App\Http\Controllers\Api\PulseController::listTurnoverHeatmap
 * @see app/Http/Controllers/Api/PulseController.php:152
 * @route '/api/people-experience/turnover-heatmap'
 */
export const listTurnoverHeatmap = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: listTurnoverHeatmap.url(options),
    method: 'get',
});

listTurnoverHeatmap.definition = {
    methods: ['get', 'head'],
    url: '/api/people-experience/turnover-heatmap',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\PulseController::listTurnoverHeatmap
 * @see app/Http/Controllers/Api/PulseController.php:152
 * @route '/api/people-experience/turnover-heatmap'
 */
listTurnoverHeatmap.url = (options?: RouteQueryOptions) => {
    return listTurnoverHeatmap.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\PulseController::listTurnoverHeatmap
 * @see app/Http/Controllers/Api/PulseController.php:152
 * @route '/api/people-experience/turnover-heatmap'
 */
listTurnoverHeatmap.get = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: listTurnoverHeatmap.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::listTurnoverHeatmap
 * @see app/Http/Controllers/Api/PulseController.php:152
 * @route '/api/people-experience/turnover-heatmap'
 */
listTurnoverHeatmap.head = (
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: listTurnoverHeatmap.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::listTurnoverHeatmap
 * @see app/Http/Controllers/Api/PulseController.php:152
 * @route '/api/people-experience/turnover-heatmap'
 */
const listTurnoverHeatmapForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: listTurnoverHeatmap.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::listTurnoverHeatmap
 * @see app/Http/Controllers/Api/PulseController.php:152
 * @route '/api/people-experience/turnover-heatmap'
 */
listTurnoverHeatmapForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: listTurnoverHeatmap.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\PulseController::listTurnoverHeatmap
 * @see app/Http/Controllers/Api/PulseController.php:152
 * @route '/api/people-experience/turnover-heatmap'
 */
listTurnoverHeatmapForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: listTurnoverHeatmap.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

listTurnoverHeatmap.form = listTurnoverHeatmapForm;

const PulseController = {
    index,
    show,
    storeResponse,
    healthScan,
    storeEmployeePulse,
    listEmployeePulses,
    listTurnoverHeatmap,
};

export default PulseController;
