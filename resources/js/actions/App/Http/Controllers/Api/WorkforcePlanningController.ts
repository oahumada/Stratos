import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::thresholds
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:40
* @route '/api/strategic-planning/workforce-planning/thresholds'
*/
export const thresholds = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: thresholds.url(options),
    method: 'get',
})

thresholds.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/workforce-planning/thresholds',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::thresholds
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:40
* @route '/api/strategic-planning/workforce-planning/thresholds'
*/
thresholds.url = (options?: RouteQueryOptions) => {
    return thresholds.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::thresholds
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:40
* @route '/api/strategic-planning/workforce-planning/thresholds'
*/
thresholds.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: thresholds.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::thresholds
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:40
* @route '/api/strategic-planning/workforce-planning/thresholds'
*/
thresholds.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: thresholds.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::thresholds
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:40
* @route '/api/strategic-planning/workforce-planning/thresholds'
*/
const thresholdsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: thresholds.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::thresholds
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:40
* @route '/api/strategic-planning/workforce-planning/thresholds'
*/
thresholdsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: thresholds.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::thresholds
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:40
* @route '/api/strategic-planning/workforce-planning/thresholds'
*/
thresholdsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: thresholds.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

thresholds.form = thresholdsForm

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::updateThresholds
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:55
* @route '/api/strategic-planning/workforce-planning/thresholds'
*/
export const updateThresholds = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateThresholds.url(options),
    method: 'patch',
})

updateThresholds.definition = {
    methods: ["patch"],
    url: '/api/strategic-planning/workforce-planning/thresholds',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::updateThresholds
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:55
* @route '/api/strategic-planning/workforce-planning/thresholds'
*/
updateThresholds.url = (options?: RouteQueryOptions) => {
    return updateThresholds.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::updateThresholds
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:55
* @route '/api/strategic-planning/workforce-planning/thresholds'
*/
updateThresholds.patch = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateThresholds.url(options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::updateThresholds
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:55
* @route '/api/strategic-planning/workforce-planning/thresholds'
*/
const updateThresholdsForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateThresholds.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::updateThresholds
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:55
* @route '/api/strategic-planning/workforce-planning/thresholds'
*/
updateThresholdsForm.patch = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateThresholds.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateThresholds.form = updateThresholdsForm

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::monitoringSummary
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:113
* @route '/api/strategic-planning/workforce-planning/monitoring/summary'
*/
export const monitoringSummary = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: monitoringSummary.url(options),
    method: 'get',
})

monitoringSummary.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/workforce-planning/monitoring/summary',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::monitoringSummary
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:113
* @route '/api/strategic-planning/workforce-planning/monitoring/summary'
*/
monitoringSummary.url = (options?: RouteQueryOptions) => {
    return monitoringSummary.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::monitoringSummary
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:113
* @route '/api/strategic-planning/workforce-planning/monitoring/summary'
*/
monitoringSummary.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: monitoringSummary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::monitoringSummary
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:113
* @route '/api/strategic-planning/workforce-planning/monitoring/summary'
*/
monitoringSummary.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: monitoringSummary.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::monitoringSummary
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:113
* @route '/api/strategic-planning/workforce-planning/monitoring/summary'
*/
const monitoringSummaryForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: monitoringSummary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::monitoringSummary
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:113
* @route '/api/strategic-planning/workforce-planning/monitoring/summary'
*/
monitoringSummaryForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: monitoringSummary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::monitoringSummary
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:113
* @route '/api/strategic-planning/workforce-planning/monitoring/summary'
*/
monitoringSummaryForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: monitoringSummary.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

monitoringSummary.form = monitoringSummaryForm

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::enterpriseSummary
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:197
* @route '/api/strategic-planning/workforce-planning/enterprise/summary'
*/
export const enterpriseSummary = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: enterpriseSummary.url(options),
    method: 'get',
})

enterpriseSummary.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/workforce-planning/enterprise/summary',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::enterpriseSummary
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:197
* @route '/api/strategic-planning/workforce-planning/enterprise/summary'
*/
enterpriseSummary.url = (options?: RouteQueryOptions) => {
    return enterpriseSummary.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::enterpriseSummary
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:197
* @route '/api/strategic-planning/workforce-planning/enterprise/summary'
*/
enterpriseSummary.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: enterpriseSummary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::enterpriseSummary
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:197
* @route '/api/strategic-planning/workforce-planning/enterprise/summary'
*/
enterpriseSummary.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: enterpriseSummary.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::enterpriseSummary
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:197
* @route '/api/strategic-planning/workforce-planning/enterprise/summary'
*/
const enterpriseSummaryForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: enterpriseSummary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::enterpriseSummary
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:197
* @route '/api/strategic-planning/workforce-planning/enterprise/summary'
*/
enterpriseSummaryForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: enterpriseSummary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::enterpriseSummary
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:197
* @route '/api/strategic-planning/workforce-planning/enterprise/summary'
*/
enterpriseSummaryForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: enterpriseSummary.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

enterpriseSummary.form = enterpriseSummaryForm

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::baselineSummary
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:215
* @route '/api/strategic-planning/workforce-planning/baseline/summary'
*/
export const baselineSummary = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: baselineSummary.url(options),
    method: 'get',
})

baselineSummary.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/workforce-planning/baseline/summary',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::baselineSummary
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:215
* @route '/api/strategic-planning/workforce-planning/baseline/summary'
*/
baselineSummary.url = (options?: RouteQueryOptions) => {
    return baselineSummary.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::baselineSummary
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:215
* @route '/api/strategic-planning/workforce-planning/baseline/summary'
*/
baselineSummary.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: baselineSummary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::baselineSummary
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:215
* @route '/api/strategic-planning/workforce-planning/baseline/summary'
*/
baselineSummary.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: baselineSummary.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::baselineSummary
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:215
* @route '/api/strategic-planning/workforce-planning/baseline/summary'
*/
const baselineSummaryForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: baselineSummary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::baselineSummary
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:215
* @route '/api/strategic-planning/workforce-planning/baseline/summary'
*/
baselineSummaryForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: baselineSummary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::baselineSummary
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:215
* @route '/api/strategic-planning/workforce-planning/baseline/summary'
*/
baselineSummaryForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: baselineSummary.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

baselineSummary.form = baselineSummaryForm

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::compareBaseline
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:232
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/compare-baseline'
*/
export const compareBaseline = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: compareBaseline.url(args, options),
    method: 'post',
})

compareBaseline.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/workforce-planning/scenarios/{id}/compare-baseline',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::compareBaseline
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:232
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/compare-baseline'
*/
compareBaseline.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return compareBaseline.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::compareBaseline
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:232
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/compare-baseline'
*/
compareBaseline.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: compareBaseline.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::compareBaseline
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:232
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/compare-baseline'
*/
const compareBaselineForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: compareBaseline.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::compareBaseline
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:232
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/compare-baseline'
*/
compareBaselineForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: compareBaseline.url(args, options),
    method: 'post',
})

compareBaseline.form = compareBaselineForm

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::analyzeScenario
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:257
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/analyze'
*/
export const analyzeScenario = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyzeScenario.url(args, options),
    method: 'post',
})

analyzeScenario.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/workforce-planning/scenarios/{id}/analyze',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::analyzeScenario
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:257
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/analyze'
*/
analyzeScenario.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return analyzeScenario.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::analyzeScenario
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:257
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/analyze'
*/
analyzeScenario.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyzeScenario.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::analyzeScenario
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:257
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/analyze'
*/
const analyzeScenarioForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: analyzeScenario.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::analyzeScenario
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:257
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/analyze'
*/
analyzeScenarioForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: analyzeScenario.url(args, options),
    method: 'post',
})

analyzeScenario.form = analyzeScenarioForm

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::compareBaselineImpact
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:292
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/compare-baseline-impact'
*/
export const compareBaselineImpact = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: compareBaselineImpact.url(args, options),
    method: 'post',
})

compareBaselineImpact.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/workforce-planning/scenarios/{id}/compare-baseline-impact',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::compareBaselineImpact
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:292
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/compare-baseline-impact'
*/
compareBaselineImpact.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return compareBaselineImpact.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::compareBaselineImpact
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:292
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/compare-baseline-impact'
*/
compareBaselineImpact.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: compareBaselineImpact.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::compareBaselineImpact
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:292
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/compare-baseline-impact'
*/
const compareBaselineImpactForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: compareBaselineImpact.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::compareBaselineImpact
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:292
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/compare-baseline-impact'
*/
compareBaselineImpactForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: compareBaselineImpact.url(args, options),
    method: 'post',
})

compareBaselineImpact.form = compareBaselineImpactForm

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::operationalSensitivity
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:321
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/operational-sensitivity'
*/
export const operationalSensitivity = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: operationalSensitivity.url(args, options),
    method: 'post',
})

operationalSensitivity.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/workforce-planning/scenarios/{id}/operational-sensitivity',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::operationalSensitivity
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:321
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/operational-sensitivity'
*/
operationalSensitivity.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return operationalSensitivity.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::operationalSensitivity
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:321
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/operational-sensitivity'
*/
operationalSensitivity.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: operationalSensitivity.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::operationalSensitivity
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:321
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/operational-sensitivity'
*/
const operationalSensitivityForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: operationalSensitivity.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::operationalSensitivity
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:321
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/operational-sensitivity'
*/
operationalSensitivityForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: operationalSensitivity.url(args, options),
    method: 'post',
})

operationalSensitivity.form = operationalSensitivityForm

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::compareScenariosMulti
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:687
* @route '/api/strategic-planning/workforce-planning/scenarios/compare-multi'
*/
export const compareScenariosMulti = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: compareScenariosMulti.url(options),
    method: 'post',
})

compareScenariosMulti.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/workforce-planning/scenarios/compare-multi',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::compareScenariosMulti
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:687
* @route '/api/strategic-planning/workforce-planning/scenarios/compare-multi'
*/
compareScenariosMulti.url = (options?: RouteQueryOptions) => {
    return compareScenariosMulti.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::compareScenariosMulti
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:687
* @route '/api/strategic-planning/workforce-planning/scenarios/compare-multi'
*/
compareScenariosMulti.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: compareScenariosMulti.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::compareScenariosMulti
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:687
* @route '/api/strategic-planning/workforce-planning/scenarios/compare-multi'
*/
const compareScenariosMultiForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: compareScenariosMulti.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::compareScenariosMulti
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:687
* @route '/api/strategic-planning/workforce-planning/scenarios/compare-multi'
*/
compareScenariosMultiForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: compareScenariosMulti.url(options),
    method: 'post',
})

compareScenariosMulti.form = compareScenariosMultiForm

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::sensitivitySweep
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:719
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/sensitivity-sweep'
*/
export const sensitivitySweep = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sensitivitySweep.url(args, options),
    method: 'post',
})

sensitivitySweep.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/workforce-planning/scenarios/{id}/sensitivity-sweep',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::sensitivitySweep
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:719
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/sensitivity-sweep'
*/
sensitivitySweep.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return sensitivitySweep.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::sensitivitySweep
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:719
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/sensitivity-sweep'
*/
sensitivitySweep.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sensitivitySweep.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::sensitivitySweep
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:719
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/sensitivity-sweep'
*/
const sensitivitySweepForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sensitivitySweep.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::sensitivitySweep
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:719
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/sensitivity-sweep'
*/
sensitivitySweepForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sensitivitySweep.url(args, options),
    method: 'post',
})

sensitivitySweep.form = sensitivitySweepForm

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::updateScenarioStatus
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:384
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/status'
*/
export const updateScenarioStatus = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateScenarioStatus.url(args, options),
    method: 'patch',
})

updateScenarioStatus.definition = {
    methods: ["patch"],
    url: '/api/strategic-planning/workforce-planning/scenarios/{id}/status',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::updateScenarioStatus
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:384
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/status'
*/
updateScenarioStatus.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return updateScenarioStatus.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::updateScenarioStatus
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:384
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/status'
*/
updateScenarioStatus.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateScenarioStatus.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::updateScenarioStatus
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:384
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/status'
*/
const updateScenarioStatusForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateScenarioStatus.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::updateScenarioStatus
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:384
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/status'
*/
updateScenarioStatusForm.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateScenarioStatus.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateScenarioStatus.form = updateScenarioStatusForm

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::listActionPlan
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:349
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/action-plan'
*/
export const listActionPlan = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listActionPlan.url(args, options),
    method: 'get',
})

listActionPlan.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/workforce-planning/scenarios/{id}/action-plan',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::listActionPlan
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:349
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/action-plan'
*/
listActionPlan.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return listActionPlan.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::listActionPlan
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:349
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/action-plan'
*/
listActionPlan.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listActionPlan.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::listActionPlan
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:349
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/action-plan'
*/
listActionPlan.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: listActionPlan.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::listActionPlan
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:349
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/action-plan'
*/
const listActionPlanForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listActionPlan.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::listActionPlan
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:349
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/action-plan'
*/
listActionPlanForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listActionPlan.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::listActionPlan
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:349
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/action-plan'
*/
listActionPlanForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listActionPlan.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

listActionPlan.form = listActionPlanForm

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::storeActionPlan
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:424
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/action-plan'
*/
export const storeActionPlan = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeActionPlan.url(args, options),
    method: 'post',
})

storeActionPlan.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/workforce-planning/scenarios/{id}/action-plan',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::storeActionPlan
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:424
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/action-plan'
*/
storeActionPlan.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return storeActionPlan.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::storeActionPlan
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:424
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/action-plan'
*/
storeActionPlan.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeActionPlan.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::storeActionPlan
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:424
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/action-plan'
*/
const storeActionPlanForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeActionPlan.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::storeActionPlan
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:424
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/action-plan'
*/
storeActionPlanForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeActionPlan.url(args, options),
    method: 'post',
})

storeActionPlan.form = storeActionPlanForm

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::updateActionPlan
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:475
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/action-plan/{actionId}'
*/
export const updateActionPlan = (args: { id: string | number, actionId: string | number } | [id: string | number, actionId: string | number ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateActionPlan.url(args, options),
    method: 'patch',
})

updateActionPlan.definition = {
    methods: ["patch"],
    url: '/api/strategic-planning/workforce-planning/scenarios/{id}/action-plan/{actionId}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::updateActionPlan
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:475
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/action-plan/{actionId}'
*/
updateActionPlan.url = (args: { id: string | number, actionId: string | number } | [id: string | number, actionId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            id: args[0],
            actionId: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
        actionId: args.actionId,
    }

    return updateActionPlan.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace('{actionId}', parsedArgs.actionId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::updateActionPlan
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:475
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/action-plan/{actionId}'
*/
updateActionPlan.patch = (args: { id: string | number, actionId: string | number } | [id: string | number, actionId: string | number ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateActionPlan.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::updateActionPlan
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:475
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/action-plan/{actionId}'
*/
const updateActionPlanForm = (args: { id: string | number, actionId: string | number } | [id: string | number, actionId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateActionPlan.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::updateActionPlan
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:475
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/action-plan/{actionId}'
*/
updateActionPlanForm.patch = (args: { id: string | number, actionId: string | number } | [id: string | number, actionId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateActionPlan.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateActionPlan.form = updateActionPlanForm

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::executionDashboard
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:519
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/execution-dashboard'
*/
export const executionDashboard = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: executionDashboard.url(args, options),
    method: 'get',
})

executionDashboard.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/workforce-planning/scenarios/{id}/execution-dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::executionDashboard
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:519
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/execution-dashboard'
*/
executionDashboard.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return executionDashboard.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::executionDashboard
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:519
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/execution-dashboard'
*/
executionDashboard.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: executionDashboard.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::executionDashboard
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:519
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/execution-dashboard'
*/
executionDashboard.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: executionDashboard.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::executionDashboard
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:519
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/execution-dashboard'
*/
const executionDashboardForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: executionDashboard.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::executionDashboard
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:519
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/execution-dashboard'
*/
executionDashboardForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: executionDashboard.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WorkforcePlanningController::executionDashboard
* @see app/Http/Controllers/Api/WorkforcePlanningController.php:519
* @route '/api/strategic-planning/workforce-planning/scenarios/{id}/execution-dashboard'
*/
executionDashboardForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: executionDashboard.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

executionDashboard.form = executionDashboardForm

const WorkforcePlanningController = { thresholds, updateThresholds, monitoringSummary, enterpriseSummary, baselineSummary, compareBaseline, analyzeScenario, compareBaselineImpact, operationalSensitivity, compareScenariosMulti, sensitivitySweep, updateScenarioStatus, listActionPlan, storeActionPlan, updateActionPlan, executionDashboard }

export default WorkforcePlanningController