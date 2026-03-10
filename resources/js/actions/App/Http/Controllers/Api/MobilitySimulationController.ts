import {
    applyUrlDefaults,
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../../../../wayfinder';
/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::simulate
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:25
 * @route '/api/strategic-planning/mobility/simulate'
 */
export const simulate = (
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: simulate.url(options),
    method: 'post',
});

simulate.definition = {
    methods: ['post'],
    url: '/api/strategic-planning/mobility/simulate',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::simulate
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:25
 * @route '/api/strategic-planning/mobility/simulate'
 */
simulate.url = (options?: RouteQueryOptions) => {
    return simulate.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::simulate
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:25
 * @route '/api/strategic-planning/mobility/simulate'
 */
simulate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: simulate.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::simulate
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:25
 * @route '/api/strategic-planning/mobility/simulate'
 */
const simulateForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: simulate.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::simulate
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:25
 * @route '/api/strategic-planning/mobility/simulate'
 */
simulateForm.post = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: simulate.url(options),
    method: 'post',
});

simulate.form = simulateForm;

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::organizationImpact
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:79
 * @route '/api/strategic-planning/mobility/organization-impact'
 */
export const organizationImpact = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: organizationImpact.url(options),
    method: 'get',
});

organizationImpact.definition = {
    methods: ['get', 'head'],
    url: '/api/strategic-planning/mobility/organization-impact',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::organizationImpact
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:79
 * @route '/api/strategic-planning/mobility/organization-impact'
 */
organizationImpact.url = (options?: RouteQueryOptions) => {
    return organizationImpact.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::organizationImpact
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:79
 * @route '/api/strategic-planning/mobility/organization-impact'
 */
organizationImpact.get = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: organizationImpact.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::organizationImpact
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:79
 * @route '/api/strategic-planning/mobility/organization-impact'
 */
organizationImpact.head = (
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: organizationImpact.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::organizationImpact
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:79
 * @route '/api/strategic-planning/mobility/organization-impact'
 */
const organizationImpactForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: organizationImpact.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::organizationImpact
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:79
 * @route '/api/strategic-planning/mobility/organization-impact'
 */
organizationImpactForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: organizationImpact.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::organizationImpact
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:79
 * @route '/api/strategic-planning/mobility/organization-impact'
 */
organizationImpactForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: organizationImpact.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

organizationImpact.form = organizationImpactForm;

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::saveScenario
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:101
 * @route '/api/strategic-planning/mobility/save-scenario'
 */
export const saveScenario = (
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: saveScenario.url(options),
    method: 'post',
});

saveScenario.definition = {
    methods: ['post'],
    url: '/api/strategic-planning/mobility/save-scenario',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::saveScenario
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:101
 * @route '/api/strategic-planning/mobility/save-scenario'
 */
saveScenario.url = (options?: RouteQueryOptions) => {
    return saveScenario.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::saveScenario
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:101
 * @route '/api/strategic-planning/mobility/save-scenario'
 */
saveScenario.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveScenario.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::saveScenario
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:101
 * @route '/api/strategic-planning/mobility/save-scenario'
 */
const saveScenarioForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: saveScenario.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::saveScenario
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:101
 * @route '/api/strategic-planning/mobility/save-scenario'
 */
saveScenarioForm.post = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: saveScenario.url(options),
    method: 'post',
});

saveScenario.form = saveScenarioForm;

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::materialize
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:142
 * @route '/api/strategic-planning/mobility/scenarios/{id}/materialize'
 */
export const materialize = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: materialize.url(args, options),
    method: 'post',
});

materialize.definition = {
    methods: ['post'],
    url: '/api/strategic-planning/mobility/scenarios/{id}/materialize',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::materialize
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:142
 * @route '/api/strategic-planning/mobility/scenarios/{id}/materialize'
 */
materialize.url = (
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
        materialize.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::materialize
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:142
 * @route '/api/strategic-planning/mobility/scenarios/{id}/materialize'
 */
materialize.post = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: materialize.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::materialize
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:142
 * @route '/api/strategic-planning/mobility/scenarios/{id}/materialize'
 */
const materializeForm = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: materialize.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::materialize
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:142
 * @route '/api/strategic-planning/mobility/scenarios/{id}/materialize'
 */
materializeForm.post = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: materialize.url(args, options),
    method: 'post',
});

materialize.form = materializeForm;

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::getExecutionTracking
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:163
 * @route '/api/strategic-planning/mobility/execution-status'
 */
export const getExecutionTracking = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getExecutionTracking.url(options),
    method: 'get',
});

getExecutionTracking.definition = {
    methods: ['get', 'head'],
    url: '/api/strategic-planning/mobility/execution-status',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::getExecutionTracking
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:163
 * @route '/api/strategic-planning/mobility/execution-status'
 */
getExecutionTracking.url = (options?: RouteQueryOptions) => {
    return getExecutionTracking.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::getExecutionTracking
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:163
 * @route '/api/strategic-planning/mobility/execution-status'
 */
getExecutionTracking.get = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getExecutionTracking.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::getExecutionTracking
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:163
 * @route '/api/strategic-planning/mobility/execution-status'
 */
getExecutionTracking.head = (
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: getExecutionTracking.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::getExecutionTracking
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:163
 * @route '/api/strategic-planning/mobility/execution-status'
 */
const getExecutionTrackingForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getExecutionTracking.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::getExecutionTracking
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:163
 * @route '/api/strategic-planning/mobility/execution-status'
 */
getExecutionTrackingForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getExecutionTracking.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::getExecutionTracking
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:163
 * @route '/api/strategic-planning/mobility/execution-status'
 */
getExecutionTrackingForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getExecutionTracking.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

getExecutionTracking.form = getExecutionTrackingForm;

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::getAiSuggestions
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:210
 * @route '/api/strategic-planning/mobility/ai-suggestions'
 */
export const getAiSuggestions = (
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: getAiSuggestions.url(options),
    method: 'post',
});

getAiSuggestions.definition = {
    methods: ['post'],
    url: '/api/strategic-planning/mobility/ai-suggestions',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::getAiSuggestions
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:210
 * @route '/api/strategic-planning/mobility/ai-suggestions'
 */
getAiSuggestions.url = (options?: RouteQueryOptions) => {
    return getAiSuggestions.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::getAiSuggestions
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:210
 * @route '/api/strategic-planning/mobility/ai-suggestions'
 */
getAiSuggestions.post = (
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: getAiSuggestions.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::getAiSuggestions
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:210
 * @route '/api/strategic-planning/mobility/ai-suggestions'
 */
const getAiSuggestionsForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: getAiSuggestions.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\MobilitySimulationController::getAiSuggestions
 * @see app/Http/Controllers/Api/MobilitySimulationController.php:210
 * @route '/api/strategic-planning/mobility/ai-suggestions'
 */
getAiSuggestionsForm.post = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: getAiSuggestions.url(options),
    method: 'post',
});

getAiSuggestions.form = getAiSuggestionsForm;

const MobilitySimulationController = {
    simulate,
    organizationImpact,
    saveScenario,
    materialize,
    getExecutionTracking,
    getAiSuggestions,
};

export default MobilitySimulationController;
