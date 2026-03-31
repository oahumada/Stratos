import {
    applyUrlDefaults,
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../../../../wayfinder';
/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::generateBlueprint
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:44
 * @route '/api/learning-blueprints/{peopleId}'
 */
export const generateBlueprint = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: generateBlueprint.url(args, options),
    method: 'post',
});

generateBlueprint.definition = {
    methods: ['post'],
    url: '/api/learning-blueprints/{peopleId}',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::generateBlueprint
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:44
 * @route '/api/learning-blueprints/{peopleId}'
 */
generateBlueprint.url = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { peopleId: args };
    }

    if (Array.isArray(args)) {
        args = {
            peopleId: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        peopleId: args.peopleId,
    };

    return (
        generateBlueprint.definition.url
            .replace('{peopleId}', parsedArgs.peopleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::generateBlueprint
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:44
 * @route '/api/learning-blueprints/{peopleId}'
 */
generateBlueprint.post = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: generateBlueprint.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::generateBlueprint
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:44
 * @route '/api/learning-blueprints/{peopleId}'
 */
const generateBlueprintForm = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: generateBlueprint.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::generateBlueprint
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:44
 * @route '/api/learning-blueprints/{peopleId}'
 */
generateBlueprintForm.post = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: generateBlueprint.url(args, options),
    method: 'post',
});

generateBlueprint.form = generateBlueprintForm;

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::materializeBlueprint
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:60
 * @route '/api/learning-blueprints/{peopleId}/materialize'
 */
export const materializeBlueprint = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: materializeBlueprint.url(args, options),
    method: 'post',
});

materializeBlueprint.definition = {
    methods: ['post'],
    url: '/api/learning-blueprints/{peopleId}/materialize',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::materializeBlueprint
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:60
 * @route '/api/learning-blueprints/{peopleId}/materialize'
 */
materializeBlueprint.url = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { peopleId: args };
    }

    if (Array.isArray(args)) {
        args = {
            peopleId: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        peopleId: args.peopleId,
    };

    return (
        materializeBlueprint.definition.url
            .replace('{peopleId}', parsedArgs.peopleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::materializeBlueprint
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:60
 * @route '/api/learning-blueprints/{peopleId}/materialize'
 */
materializeBlueprint.post = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: materializeBlueprint.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::materializeBlueprint
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:60
 * @route '/api/learning-blueprints/{peopleId}/materialize'
 */
const materializeBlueprintForm = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: materializeBlueprint.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::materializeBlueprint
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:60
 * @route '/api/learning-blueprints/{peopleId}/materialize'
 */
materializeBlueprintForm.post = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: materializeBlueprint.url(args, options),
    method: 'post',
});

materializeBlueprint.form = materializeBlueprintForm;

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::runSentinelScan
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:78
 * @route '/api/sentinel/scan'
 */
export const runSentinelScan = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: runSentinelScan.url(options),
    method: 'get',
});

runSentinelScan.definition = {
    methods: ['get', 'head'],
    url: '/api/sentinel/scan',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::runSentinelScan
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:78
 * @route '/api/sentinel/scan'
 */
runSentinelScan.url = (options?: RouteQueryOptions) => {
    return runSentinelScan.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::runSentinelScan
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:78
 * @route '/api/sentinel/scan'
 */
runSentinelScan.get = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: runSentinelScan.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::runSentinelScan
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:78
 * @route '/api/sentinel/scan'
 */
runSentinelScan.head = (
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: runSentinelScan.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::runSentinelScan
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:78
 * @route '/api/sentinel/scan'
 */
const runSentinelScanForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: runSentinelScan.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::runSentinelScan
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:78
 * @route '/api/sentinel/scan'
 */
runSentinelScanForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: runSentinelScan.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::runSentinelScan
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:78
 * @route '/api/sentinel/scan'
 */
runSentinelScanForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: runSentinelScan.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

runSentinelScan.form = runSentinelScanForm;

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::getSentinelHealth
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:92
 * @route '/api/sentinel/health'
 */
export const getSentinelHealth = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getSentinelHealth.url(options),
    method: 'get',
});

getSentinelHealth.definition = {
    methods: ['get', 'head'],
    url: '/api/sentinel/health',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::getSentinelHealth
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:92
 * @route '/api/sentinel/health'
 */
getSentinelHealth.url = (options?: RouteQueryOptions) => {
    return getSentinelHealth.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::getSentinelHealth
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:92
 * @route '/api/sentinel/health'
 */
getSentinelHealth.get = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getSentinelHealth.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::getSentinelHealth
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:92
 * @route '/api/sentinel/health'
 */
getSentinelHealth.head = (
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: getSentinelHealth.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::getSentinelHealth
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:92
 * @route '/api/sentinel/health'
 */
const getSentinelHealthForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getSentinelHealth.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::getSentinelHealth
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:92
 * @route '/api/sentinel/health'
 */
getSentinelHealthForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getSentinelHealth.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::getSentinelHealth
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:92
 * @route '/api/sentinel/health'
 */
getSentinelHealthForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getSentinelHealth.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

getSentinelHealth.form = getSentinelHealthForm;

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::getGuideSuggestions
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:112
 * @route '/api/guide/suggestions'
 */
export const getGuideSuggestions = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getGuideSuggestions.url(options),
    method: 'get',
});

getGuideSuggestions.definition = {
    methods: ['get', 'head'],
    url: '/api/guide/suggestions',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::getGuideSuggestions
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:112
 * @route '/api/guide/suggestions'
 */
getGuideSuggestions.url = (options?: RouteQueryOptions) => {
    return getGuideSuggestions.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::getGuideSuggestions
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:112
 * @route '/api/guide/suggestions'
 */
getGuideSuggestions.get = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getGuideSuggestions.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::getGuideSuggestions
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:112
 * @route '/api/guide/suggestions'
 */
getGuideSuggestions.head = (
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: getGuideSuggestions.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::getGuideSuggestions
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:112
 * @route '/api/guide/suggestions'
 */
const getGuideSuggestionsForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getGuideSuggestions.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::getGuideSuggestions
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:112
 * @route '/api/guide/suggestions'
 */
getGuideSuggestionsForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getGuideSuggestions.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::getGuideSuggestions
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:112
 * @route '/api/guide/suggestions'
 */
getGuideSuggestionsForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getGuideSuggestions.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

getGuideSuggestions.form = getGuideSuggestionsForm;

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::askGuide
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:129
 * @route '/api/guide/ask'
 */
export const askGuide = (
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: askGuide.url(options),
    method: 'post',
});

askGuide.definition = {
    methods: ['post'],
    url: '/api/guide/ask',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::askGuide
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:129
 * @route '/api/guide/ask'
 */
askGuide.url = (options?: RouteQueryOptions) => {
    return askGuide.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::askGuide
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:129
 * @route '/api/guide/ask'
 */
askGuide.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: askGuide.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::askGuide
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:129
 * @route '/api/guide/ask'
 */
const askGuideForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: askGuide.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::askGuide
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:129
 * @route '/api/guide/ask'
 */
askGuideForm.post = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: askGuide.url(options),
    method: 'post',
});

askGuide.form = askGuideForm;

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::completeOnboardingStep
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:150
 * @route '/api/guide/onboarding/complete'
 */
export const completeOnboardingStep = (
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: completeOnboardingStep.url(options),
    method: 'post',
});

completeOnboardingStep.definition = {
    methods: ['post'],
    url: '/api/guide/onboarding/complete',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::completeOnboardingStep
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:150
 * @route '/api/guide/onboarding/complete'
 */
completeOnboardingStep.url = (options?: RouteQueryOptions) => {
    return completeOnboardingStep.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::completeOnboardingStep
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:150
 * @route '/api/guide/onboarding/complete'
 */
completeOnboardingStep.post = (
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: completeOnboardingStep.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::completeOnboardingStep
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:150
 * @route '/api/guide/onboarding/complete'
 */
const completeOnboardingStepForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: completeOnboardingStep.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::completeOnboardingStep
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:150
 * @route '/api/guide/onboarding/complete'
 */
completeOnboardingStepForm.post = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: completeOnboardingStep.url(options),
    method: 'post',
});

completeOnboardingStep.form = completeOnboardingStepForm;

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::getRetentionForecast
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:28
 * @route '/api/retention-forecast/{peopleId}'
 */
export const getRetentionForecast = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getRetentionForecast.url(args, options),
    method: 'get',
});

getRetentionForecast.definition = {
    methods: ['get', 'head'],
    url: '/api/retention-forecast/{peopleId}',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::getRetentionForecast
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:28
 * @route '/api/retention-forecast/{peopleId}'
 */
getRetentionForecast.url = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { peopleId: args };
    }

    if (Array.isArray(args)) {
        args = {
            peopleId: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        peopleId: args.peopleId,
    };

    return (
        getRetentionForecast.definition.url
            .replace('{peopleId}', parsedArgs.peopleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::getRetentionForecast
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:28
 * @route '/api/retention-forecast/{peopleId}'
 */
getRetentionForecast.get = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getRetentionForecast.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::getRetentionForecast
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:28
 * @route '/api/retention-forecast/{peopleId}'
 */
getRetentionForecast.head = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: getRetentionForecast.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::getRetentionForecast
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:28
 * @route '/api/retention-forecast/{peopleId}'
 */
const getRetentionForecastForm = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getRetentionForecast.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::getRetentionForecast
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:28
 * @route '/api/retention-forecast/{peopleId}'
 */
getRetentionForecastForm.get = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getRetentionForecast.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\StratosIntelligenceController::getRetentionForecast
 * @see app/Http/Controllers/Api/StratosIntelligenceController.php:28
 * @route '/api/retention-forecast/{peopleId}'
 */
getRetentionForecastForm.head = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getRetentionForecast.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

getRetentionForecast.form = getRetentionForecastForm;

const StratosIntelligenceController = {
    generateBlueprint,
    materializeBlueprint,
    runSentinelScan,
    getSentinelHealth,
    getGuideSuggestions,
    askGuide,
    completeOnboardingStep,
    getRetentionForecast,
};

export default StratosIntelligenceController;
