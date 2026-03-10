import {
    applyUrlDefaults,
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../../../../wayfinder';
/**
 * @see \App\Http\Controllers\Api\ImpactReportController::scenarioImpact
 * @see app/Http/Controllers/Api/ImpactReportController.php:19
 * @route '/api/reports/scenario/{scenarioId}/impact'
 */
export const scenarioImpact = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: scenarioImpact.url(args, options),
    method: 'get',
});

scenarioImpact.definition = {
    methods: ['get', 'head'],
    url: '/api/reports/scenario/{scenarioId}/impact',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\ImpactReportController::scenarioImpact
 * @see app/Http/Controllers/Api/ImpactReportController.php:19
 * @route '/api/reports/scenario/{scenarioId}/impact'
 */
scenarioImpact.url = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenarioId: args };
    }

    if (Array.isArray(args)) {
        args = {
            scenarioId: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        scenarioId: args.scenarioId,
    };

    return (
        scenarioImpact.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\ImpactReportController::scenarioImpact
 * @see app/Http/Controllers/Api/ImpactReportController.php:19
 * @route '/api/reports/scenario/{scenarioId}/impact'
 */
scenarioImpact.get = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: scenarioImpact.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\ImpactReportController::scenarioImpact
 * @see app/Http/Controllers/Api/ImpactReportController.php:19
 * @route '/api/reports/scenario/{scenarioId}/impact'
 */
scenarioImpact.head = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: scenarioImpact.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\ImpactReportController::scenarioImpact
 * @see app/Http/Controllers/Api/ImpactReportController.php:19
 * @route '/api/reports/scenario/{scenarioId}/impact'
 */
const scenarioImpactForm = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: scenarioImpact.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\ImpactReportController::scenarioImpact
 * @see app/Http/Controllers/Api/ImpactReportController.php:19
 * @route '/api/reports/scenario/{scenarioId}/impact'
 */
scenarioImpactForm.get = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: scenarioImpact.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\ImpactReportController::scenarioImpact
 * @see app/Http/Controllers/Api/ImpactReportController.php:19
 * @route '/api/reports/scenario/{scenarioId}/impact'
 */
scenarioImpactForm.head = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: scenarioImpact.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

scenarioImpact.form = scenarioImpactForm;

/**
 * @see \App\Http\Controllers\Api\ImpactReportController::organizationalRoi
 * @see app/Http/Controllers/Api/ImpactReportController.php:33
 * @route '/api/reports/roi'
 */
export const organizationalRoi = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: organizationalRoi.url(options),
    method: 'get',
});

organizationalRoi.definition = {
    methods: ['get', 'head'],
    url: '/api/reports/roi',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\ImpactReportController::organizationalRoi
 * @see app/Http/Controllers/Api/ImpactReportController.php:33
 * @route '/api/reports/roi'
 */
organizationalRoi.url = (options?: RouteQueryOptions) => {
    return organizationalRoi.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\ImpactReportController::organizationalRoi
 * @see app/Http/Controllers/Api/ImpactReportController.php:33
 * @route '/api/reports/roi'
 */
organizationalRoi.get = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: organizationalRoi.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\ImpactReportController::organizationalRoi
 * @see app/Http/Controllers/Api/ImpactReportController.php:33
 * @route '/api/reports/roi'
 */
organizationalRoi.head = (
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: organizationalRoi.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\ImpactReportController::organizationalRoi
 * @see app/Http/Controllers/Api/ImpactReportController.php:33
 * @route '/api/reports/roi'
 */
const organizationalRoiForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: organizationalRoi.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\ImpactReportController::organizationalRoi
 * @see app/Http/Controllers/Api/ImpactReportController.php:33
 * @route '/api/reports/roi'
 */
organizationalRoiForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: organizationalRoi.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\ImpactReportController::organizationalRoi
 * @see app/Http/Controllers/Api/ImpactReportController.php:33
 * @route '/api/reports/roi'
 */
organizationalRoiForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: organizationalRoi.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

organizationalRoi.form = organizationalRoiForm;

/**
 * @see \App\Http\Controllers\Api\ImpactReportController::consolidated
 * @see app/Http/Controllers/Api/ImpactReportController.php:47
 * @route '/api/reports/consolidated'
 */
export const consolidated = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: consolidated.url(options),
    method: 'get',
});

consolidated.definition = {
    methods: ['get', 'head'],
    url: '/api/reports/consolidated',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\ImpactReportController::consolidated
 * @see app/Http/Controllers/Api/ImpactReportController.php:47
 * @route '/api/reports/consolidated'
 */
consolidated.url = (options?: RouteQueryOptions) => {
    return consolidated.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\ImpactReportController::consolidated
 * @see app/Http/Controllers/Api/ImpactReportController.php:47
 * @route '/api/reports/consolidated'
 */
consolidated.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: consolidated.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\ImpactReportController::consolidated
 * @see app/Http/Controllers/Api/ImpactReportController.php:47
 * @route '/api/reports/consolidated'
 */
consolidated.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: consolidated.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\ImpactReportController::consolidated
 * @see app/Http/Controllers/Api/ImpactReportController.php:47
 * @route '/api/reports/consolidated'
 */
const consolidatedForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: consolidated.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\ImpactReportController::consolidated
 * @see app/Http/Controllers/Api/ImpactReportController.php:47
 * @route '/api/reports/consolidated'
 */
consolidatedForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: consolidated.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\ImpactReportController::consolidated
 * @see app/Http/Controllers/Api/ImpactReportController.php:47
 * @route '/api/reports/consolidated'
 */
consolidatedForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: consolidated.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

consolidated.form = consolidatedForm;

const ImpactReportController = {
    scenarioImpact,
    organizationalRoi,
    consolidated,
};

export default ImpactReportController;
