import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\TalentRiskController::indexIndicators
* @see app/Http/Controllers/Api/TalentRiskController.php:20
* @route '/api/scenarios/{scenario}/risks/indicators'
*/
export const indexIndicators = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexIndicators.url(args, options),
    method: 'get',
})

indexIndicators.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenario}/risks/indicators',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\TalentRiskController::indexIndicators
* @see app/Http/Controllers/Api/TalentRiskController.php:20
* @route '/api/scenarios/{scenario}/risks/indicators'
*/
indexIndicators.url = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenario: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { scenario: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            scenario: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenario: typeof args.scenario === 'object'
        ? args.scenario.id
        : args.scenario,
    }

    return indexIndicators.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TalentRiskController::indexIndicators
* @see app/Http/Controllers/Api/TalentRiskController.php:20
* @route '/api/scenarios/{scenario}/risks/indicators'
*/
indexIndicators.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexIndicators.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::indexIndicators
* @see app/Http/Controllers/Api/TalentRiskController.php:20
* @route '/api/scenarios/{scenario}/risks/indicators'
*/
indexIndicators.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: indexIndicators.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::indexIndicators
* @see app/Http/Controllers/Api/TalentRiskController.php:20
* @route '/api/scenarios/{scenario}/risks/indicators'
*/
const indexIndicatorsForm = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexIndicators.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::indexIndicators
* @see app/Http/Controllers/Api/TalentRiskController.php:20
* @route '/api/scenarios/{scenario}/risks/indicators'
*/
indexIndicatorsForm.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexIndicators.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::indexIndicators
* @see app/Http/Controllers/Api/TalentRiskController.php:20
* @route '/api/scenarios/{scenario}/risks/indicators'
*/
indexIndicatorsForm.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexIndicators.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

indexIndicators.form = indexIndicatorsForm

/**
* @see \App\Http\Controllers\Api\TalentRiskController::analyze
* @see app/Http/Controllers/Api/TalentRiskController.php:43
* @route '/api/scenarios/{scenario}/risks/analyze'
*/
export const analyze = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyze.url(args, options),
    method: 'post',
})

analyze.definition = {
    methods: ["post"],
    url: '/api/scenarios/{scenario}/risks/analyze',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\TalentRiskController::analyze
* @see app/Http/Controllers/Api/TalentRiskController.php:43
* @route '/api/scenarios/{scenario}/risks/analyze'
*/
analyze.url = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenario: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { scenario: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            scenario: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenario: typeof args.scenario === 'object'
        ? args.scenario.id
        : args.scenario,
    }

    return analyze.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TalentRiskController::analyze
* @see app/Http/Controllers/Api/TalentRiskController.php:43
* @route '/api/scenarios/{scenario}/risks/analyze'
*/
analyze.post = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyze.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::analyze
* @see app/Http/Controllers/Api/TalentRiskController.php:43
* @route '/api/scenarios/{scenario}/risks/analyze'
*/
const analyzeForm = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: analyze.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::analyze
* @see app/Http/Controllers/Api/TalentRiskController.php:43
* @route '/api/scenarios/{scenario}/risks/analyze'
*/
analyzeForm.post = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: analyze.url(args, options),
    method: 'post',
})

analyze.form = analyzeForm

/**
* @see \App\Http\Controllers\Api\TalentRiskController::getSummary
* @see app/Http/Controllers/Api/TalentRiskController.php:95
* @route '/api/scenarios/{scenario}/risks/summary'
*/
export const getSummary = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSummary.url(args, options),
    method: 'get',
})

getSummary.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenario}/risks/summary',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\TalentRiskController::getSummary
* @see app/Http/Controllers/Api/TalentRiskController.php:95
* @route '/api/scenarios/{scenario}/risks/summary'
*/
getSummary.url = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenario: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { scenario: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            scenario: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenario: typeof args.scenario === 'object'
        ? args.scenario.id
        : args.scenario,
    }

    return getSummary.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TalentRiskController::getSummary
* @see app/Http/Controllers/Api/TalentRiskController.php:95
* @route '/api/scenarios/{scenario}/risks/summary'
*/
getSummary.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSummary.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::getSummary
* @see app/Http/Controllers/Api/TalentRiskController.php:95
* @route '/api/scenarios/{scenario}/risks/summary'
*/
getSummary.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getSummary.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::getSummary
* @see app/Http/Controllers/Api/TalentRiskController.php:95
* @route '/api/scenarios/{scenario}/risks/summary'
*/
const getSummaryForm = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getSummary.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::getSummary
* @see app/Http/Controllers/Api/TalentRiskController.php:95
* @route '/api/scenarios/{scenario}/risks/summary'
*/
getSummaryForm.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getSummary.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::getSummary
* @see app/Http/Controllers/Api/TalentRiskController.php:95
* @route '/api/scenarios/{scenario}/risks/summary'
*/
getSummaryForm.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getSummary.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getSummary.form = getSummaryForm

/**
* @see \App\Http\Controllers\Api\TalentRiskController::getDetailsByType
* @see app/Http/Controllers/Api/TalentRiskController.php:119
* @route '/api/scenarios/{scenario}/risks/{riskType}/details'
*/
export const getDetailsByType = (args: { scenario: number | { id: number }, riskType: string | number } | [scenario: number | { id: number }, riskType: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getDetailsByType.url(args, options),
    method: 'get',
})

getDetailsByType.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenario}/risks/{riskType}/details',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\TalentRiskController::getDetailsByType
* @see app/Http/Controllers/Api/TalentRiskController.php:119
* @route '/api/scenarios/{scenario}/risks/{riskType}/details'
*/
getDetailsByType.url = (args: { scenario: number | { id: number }, riskType: string | number } | [scenario: number | { id: number }, riskType: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            scenario: args[0],
            riskType: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenario: typeof args.scenario === 'object'
        ? args.scenario.id
        : args.scenario,
        riskType: args.riskType,
    }

    return getDetailsByType.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace('{riskType}', parsedArgs.riskType.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TalentRiskController::getDetailsByType
* @see app/Http/Controllers/Api/TalentRiskController.php:119
* @route '/api/scenarios/{scenario}/risks/{riskType}/details'
*/
getDetailsByType.get = (args: { scenario: number | { id: number }, riskType: string | number } | [scenario: number | { id: number }, riskType: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getDetailsByType.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::getDetailsByType
* @see app/Http/Controllers/Api/TalentRiskController.php:119
* @route '/api/scenarios/{scenario}/risks/{riskType}/details'
*/
getDetailsByType.head = (args: { scenario: number | { id: number }, riskType: string | number } | [scenario: number | { id: number }, riskType: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getDetailsByType.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::getDetailsByType
* @see app/Http/Controllers/Api/TalentRiskController.php:119
* @route '/api/scenarios/{scenario}/risks/{riskType}/details'
*/
const getDetailsByTypeForm = (args: { scenario: number | { id: number }, riskType: string | number } | [scenario: number | { id: number }, riskType: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getDetailsByType.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::getDetailsByType
* @see app/Http/Controllers/Api/TalentRiskController.php:119
* @route '/api/scenarios/{scenario}/risks/{riskType}/details'
*/
getDetailsByTypeForm.get = (args: { scenario: number | { id: number }, riskType: string | number } | [scenario: number | { id: number }, riskType: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getDetailsByType.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::getDetailsByType
* @see app/Http/Controllers/Api/TalentRiskController.php:119
* @route '/api/scenarios/{scenario}/risks/{riskType}/details'
*/
getDetailsByTypeForm.head = (args: { scenario: number | { id: number }, riskType: string | number } | [scenario: number | { id: number }, riskType: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getDetailsByType.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getDetailsByType.form = getDetailsByTypeForm

/**
* @see \App\Http\Controllers\Api\TalentRiskController::recordMitigation
* @see app/Http/Controllers/Api/TalentRiskController.php:134
* @route '/api/risks/{indicator}/mitigations'
*/
export const recordMitigation = (args: { indicator: number | { id: number } } | [indicator: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: recordMitigation.url(args, options),
    method: 'post',
})

recordMitigation.definition = {
    methods: ["post"],
    url: '/api/risks/{indicator}/mitigations',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\TalentRiskController::recordMitigation
* @see app/Http/Controllers/Api/TalentRiskController.php:134
* @route '/api/risks/{indicator}/mitigations'
*/
recordMitigation.url = (args: { indicator: number | { id: number } } | [indicator: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { indicator: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { indicator: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            indicator: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        indicator: typeof args.indicator === 'object'
        ? args.indicator.id
        : args.indicator,
    }

    return recordMitigation.definition.url
            .replace('{indicator}', parsedArgs.indicator.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TalentRiskController::recordMitigation
* @see app/Http/Controllers/Api/TalentRiskController.php:134
* @route '/api/risks/{indicator}/mitigations'
*/
recordMitigation.post = (args: { indicator: number | { id: number } } | [indicator: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: recordMitigation.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::recordMitigation
* @see app/Http/Controllers/Api/TalentRiskController.php:134
* @route '/api/risks/{indicator}/mitigations'
*/
const recordMitigationForm = (args: { indicator: number | { id: number } } | [indicator: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: recordMitigation.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::recordMitigation
* @see app/Http/Controllers/Api/TalentRiskController.php:134
* @route '/api/risks/{indicator}/mitigations'
*/
recordMitigationForm.post = (args: { indicator: number | { id: number } } | [indicator: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: recordMitigation.url(args, options),
    method: 'post',
})

recordMitigation.form = recordMitigationForm

/**
* @see \App\Http\Controllers\Api\TalentRiskController::listMitigations
* @see app/Http/Controllers/Api/TalentRiskController.php:151
* @route '/api/risks/{indicator}/mitigations'
*/
export const listMitigations = (args: { indicator: number | { id: number } } | [indicator: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listMitigations.url(args, options),
    method: 'get',
})

listMitigations.definition = {
    methods: ["get","head"],
    url: '/api/risks/{indicator}/mitigations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\TalentRiskController::listMitigations
* @see app/Http/Controllers/Api/TalentRiskController.php:151
* @route '/api/risks/{indicator}/mitigations'
*/
listMitigations.url = (args: { indicator: number | { id: number } } | [indicator: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { indicator: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { indicator: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            indicator: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        indicator: typeof args.indicator === 'object'
        ? args.indicator.id
        : args.indicator,
    }

    return listMitigations.definition.url
            .replace('{indicator}', parsedArgs.indicator.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TalentRiskController::listMitigations
* @see app/Http/Controllers/Api/TalentRiskController.php:151
* @route '/api/risks/{indicator}/mitigations'
*/
listMitigations.get = (args: { indicator: number | { id: number } } | [indicator: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listMitigations.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::listMitigations
* @see app/Http/Controllers/Api/TalentRiskController.php:151
* @route '/api/risks/{indicator}/mitigations'
*/
listMitigations.head = (args: { indicator: number | { id: number } } | [indicator: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: listMitigations.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::listMitigations
* @see app/Http/Controllers/Api/TalentRiskController.php:151
* @route '/api/risks/{indicator}/mitigations'
*/
const listMitigationsForm = (args: { indicator: number | { id: number } } | [indicator: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listMitigations.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::listMitigations
* @see app/Http/Controllers/Api/TalentRiskController.php:151
* @route '/api/risks/{indicator}/mitigations'
*/
listMitigationsForm.get = (args: { indicator: number | { id: number } } | [indicator: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listMitigations.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::listMitigations
* @see app/Http/Controllers/Api/TalentRiskController.php:151
* @route '/api/risks/{indicator}/mitigations'
*/
listMitigationsForm.head = (args: { indicator: number | { id: number } } | [indicator: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listMitigations.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

listMitigations.form = listMitigationsForm

/**
* @see \App\Http\Controllers\Api\TalentRiskController::updateMitigationStatus
* @see app/Http/Controllers/Api/TalentRiskController.php:165
* @route '/api/mitigations/{mitigation}/status'
*/
export const updateMitigationStatus = (args: { mitigation: number | { id: number } } | [mitigation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateMitigationStatus.url(args, options),
    method: 'patch',
})

updateMitigationStatus.definition = {
    methods: ["patch"],
    url: '/api/mitigations/{mitigation}/status',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\TalentRiskController::updateMitigationStatus
* @see app/Http/Controllers/Api/TalentRiskController.php:165
* @route '/api/mitigations/{mitigation}/status'
*/
updateMitigationStatus.url = (args: { mitigation: number | { id: number } } | [mitigation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { mitigation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { mitigation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            mitigation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        mitigation: typeof args.mitigation === 'object'
        ? args.mitigation.id
        : args.mitigation,
    }

    return updateMitigationStatus.definition.url
            .replace('{mitigation}', parsedArgs.mitigation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TalentRiskController::updateMitigationStatus
* @see app/Http/Controllers/Api/TalentRiskController.php:165
* @route '/api/mitigations/{mitigation}/status'
*/
updateMitigationStatus.patch = (args: { mitigation: number | { id: number } } | [mitigation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateMitigationStatus.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::updateMitigationStatus
* @see app/Http/Controllers/Api/TalentRiskController.php:165
* @route '/api/mitigations/{mitigation}/status'
*/
const updateMitigationStatusForm = (args: { mitigation: number | { id: number } } | [mitigation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateMitigationStatus.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::updateMitigationStatus
* @see app/Http/Controllers/Api/TalentRiskController.php:165
* @route '/api/mitigations/{mitigation}/status'
*/
updateMitigationStatusForm.patch = (args: { mitigation: number | { id: number } } | [mitigation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateMitigationStatus.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateMitigationStatus.form = updateMitigationStatusForm

/**
* @see \App\Http\Controllers\Api\TalentRiskController::getRiskHeatmap
* @see app/Http/Controllers/Api/TalentRiskController.php:186
* @route '/api/scenarios/{scenario}/risks/heatmap'
*/
export const getRiskHeatmap = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getRiskHeatmap.url(args, options),
    method: 'get',
})

getRiskHeatmap.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenario}/risks/heatmap',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\TalentRiskController::getRiskHeatmap
* @see app/Http/Controllers/Api/TalentRiskController.php:186
* @route '/api/scenarios/{scenario}/risks/heatmap'
*/
getRiskHeatmap.url = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenario: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { scenario: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            scenario: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenario: typeof args.scenario === 'object'
        ? args.scenario.id
        : args.scenario,
    }

    return getRiskHeatmap.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TalentRiskController::getRiskHeatmap
* @see app/Http/Controllers/Api/TalentRiskController.php:186
* @route '/api/scenarios/{scenario}/risks/heatmap'
*/
getRiskHeatmap.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getRiskHeatmap.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::getRiskHeatmap
* @see app/Http/Controllers/Api/TalentRiskController.php:186
* @route '/api/scenarios/{scenario}/risks/heatmap'
*/
getRiskHeatmap.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getRiskHeatmap.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::getRiskHeatmap
* @see app/Http/Controllers/Api/TalentRiskController.php:186
* @route '/api/scenarios/{scenario}/risks/heatmap'
*/
const getRiskHeatmapForm = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getRiskHeatmap.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::getRiskHeatmap
* @see app/Http/Controllers/Api/TalentRiskController.php:186
* @route '/api/scenarios/{scenario}/risks/heatmap'
*/
getRiskHeatmapForm.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getRiskHeatmap.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::getRiskHeatmap
* @see app/Http/Controllers/Api/TalentRiskController.php:186
* @route '/api/scenarios/{scenario}/risks/heatmap'
*/
getRiskHeatmapForm.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getRiskHeatmap.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getRiskHeatmap.form = getRiskHeatmapForm

const TalentRiskController = { indexIndicators, analyze, getSummary, getDetailsByType, recordMitigation, listMitigations, updateMitigationStatus, getRiskHeatmap }

export default TalentRiskController