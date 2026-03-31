import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
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
* @see \App\Http\Controllers\Api\TalentRiskController::summary
* @see app/Http/Controllers/Api/TalentRiskController.php:95
* @route '/api/scenarios/{scenario}/risks/summary'
*/
export const summary = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: summary.url(args, options),
    method: 'get',
})

summary.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenario}/risks/summary',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\TalentRiskController::summary
* @see app/Http/Controllers/Api/TalentRiskController.php:95
* @route '/api/scenarios/{scenario}/risks/summary'
*/
summary.url = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return summary.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TalentRiskController::summary
* @see app/Http/Controllers/Api/TalentRiskController.php:95
* @route '/api/scenarios/{scenario}/risks/summary'
*/
summary.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: summary.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::summary
* @see app/Http/Controllers/Api/TalentRiskController.php:95
* @route '/api/scenarios/{scenario}/risks/summary'
*/
summary.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: summary.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::summary
* @see app/Http/Controllers/Api/TalentRiskController.php:95
* @route '/api/scenarios/{scenario}/risks/summary'
*/
const summaryForm = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: summary.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::summary
* @see app/Http/Controllers/Api/TalentRiskController.php:95
* @route '/api/scenarios/{scenario}/risks/summary'
*/
summaryForm.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: summary.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::summary
* @see app/Http/Controllers/Api/TalentRiskController.php:95
* @route '/api/scenarios/{scenario}/risks/summary'
*/
summaryForm.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: summary.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

summary.form = summaryForm

/**
* @see \App\Http\Controllers\Api\TalentRiskController::detailsByType
* @see app/Http/Controllers/Api/TalentRiskController.php:119
* @route '/api/scenarios/{scenario}/risks/{riskType}/details'
*/
export const detailsByType = (args: { scenario: number | { id: number }, riskType: string | number } | [scenario: number | { id: number }, riskType: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detailsByType.url(args, options),
    method: 'get',
})

detailsByType.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenario}/risks/{riskType}/details',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\TalentRiskController::detailsByType
* @see app/Http/Controllers/Api/TalentRiskController.php:119
* @route '/api/scenarios/{scenario}/risks/{riskType}/details'
*/
detailsByType.url = (args: { scenario: number | { id: number }, riskType: string | number } | [scenario: number | { id: number }, riskType: string | number ], options?: RouteQueryOptions) => {
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

    return detailsByType.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace('{riskType}', parsedArgs.riskType.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TalentRiskController::detailsByType
* @see app/Http/Controllers/Api/TalentRiskController.php:119
* @route '/api/scenarios/{scenario}/risks/{riskType}/details'
*/
detailsByType.get = (args: { scenario: number | { id: number }, riskType: string | number } | [scenario: number | { id: number }, riskType: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detailsByType.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::detailsByType
* @see app/Http/Controllers/Api/TalentRiskController.php:119
* @route '/api/scenarios/{scenario}/risks/{riskType}/details'
*/
detailsByType.head = (args: { scenario: number | { id: number }, riskType: string | number } | [scenario: number | { id: number }, riskType: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: detailsByType.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::detailsByType
* @see app/Http/Controllers/Api/TalentRiskController.php:119
* @route '/api/scenarios/{scenario}/risks/{riskType}/details'
*/
const detailsByTypeForm = (args: { scenario: number | { id: number }, riskType: string | number } | [scenario: number | { id: number }, riskType: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: detailsByType.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::detailsByType
* @see app/Http/Controllers/Api/TalentRiskController.php:119
* @route '/api/scenarios/{scenario}/risks/{riskType}/details'
*/
detailsByTypeForm.get = (args: { scenario: number | { id: number }, riskType: string | number } | [scenario: number | { id: number }, riskType: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: detailsByType.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::detailsByType
* @see app/Http/Controllers/Api/TalentRiskController.php:119
* @route '/api/scenarios/{scenario}/risks/{riskType}/details'
*/
detailsByTypeForm.head = (args: { scenario: number | { id: number }, riskType: string | number } | [scenario: number | { id: number }, riskType: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: detailsByType.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

detailsByType.form = detailsByTypeForm

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
* @see \App\Http\Controllers\Api\TalentRiskController::updateStatus
* @see app/Http/Controllers/Api/TalentRiskController.php:165
* @route '/api/mitigations/{mitigation}/status'
*/
export const updateStatus = (args: { mitigation: number | { id: number } } | [mitigation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateStatus.url(args, options),
    method: 'patch',
})

updateStatus.definition = {
    methods: ["patch"],
    url: '/api/mitigations/{mitigation}/status',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\TalentRiskController::updateStatus
* @see app/Http/Controllers/Api/TalentRiskController.php:165
* @route '/api/mitigations/{mitigation}/status'
*/
updateStatus.url = (args: { mitigation: number | { id: number } } | [mitigation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return updateStatus.definition.url
            .replace('{mitigation}', parsedArgs.mitigation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TalentRiskController::updateStatus
* @see app/Http/Controllers/Api/TalentRiskController.php:165
* @route '/api/mitigations/{mitigation}/status'
*/
updateStatus.patch = (args: { mitigation: number | { id: number } } | [mitigation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateStatus.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::updateStatus
* @see app/Http/Controllers/Api/TalentRiskController.php:165
* @route '/api/mitigations/{mitigation}/status'
*/
const updateStatusForm = (args: { mitigation: number | { id: number } } | [mitigation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateStatus.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::updateStatus
* @see app/Http/Controllers/Api/TalentRiskController.php:165
* @route '/api/mitigations/{mitigation}/status'
*/
updateStatusForm.patch = (args: { mitigation: number | { id: number } } | [mitigation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateStatus.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateStatus.form = updateStatusForm

/**
* @see \App\Http\Controllers\Api\TalentRiskController::heatmap
* @see app/Http/Controllers/Api/TalentRiskController.php:186
* @route '/api/scenarios/{scenario}/risks/heatmap'
*/
export const heatmap = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: heatmap.url(args, options),
    method: 'get',
})

heatmap.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenario}/risks/heatmap',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\TalentRiskController::heatmap
* @see app/Http/Controllers/Api/TalentRiskController.php:186
* @route '/api/scenarios/{scenario}/risks/heatmap'
*/
heatmap.url = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return heatmap.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TalentRiskController::heatmap
* @see app/Http/Controllers/Api/TalentRiskController.php:186
* @route '/api/scenarios/{scenario}/risks/heatmap'
*/
heatmap.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: heatmap.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::heatmap
* @see app/Http/Controllers/Api/TalentRiskController.php:186
* @route '/api/scenarios/{scenario}/risks/heatmap'
*/
heatmap.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: heatmap.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::heatmap
* @see app/Http/Controllers/Api/TalentRiskController.php:186
* @route '/api/scenarios/{scenario}/risks/heatmap'
*/
const heatmapForm = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: heatmap.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::heatmap
* @see app/Http/Controllers/Api/TalentRiskController.php:186
* @route '/api/scenarios/{scenario}/risks/heatmap'
*/
heatmapForm.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: heatmap.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentRiskController::heatmap
* @see app/Http/Controllers/Api/TalentRiskController.php:186
* @route '/api/scenarios/{scenario}/risks/heatmap'
*/
heatmapForm.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: heatmap.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

heatmap.form = heatmapForm

const risks = {
    indexIndicators: Object.assign(indexIndicators, indexIndicators),
    analyze: Object.assign(analyze, analyze),
    summary: Object.assign(summary, summary),
    detailsByType: Object.assign(detailsByType, detailsByType),
    recordMitigation: Object.assign(recordMitigation, recordMitigation),
    listMitigations: Object.assign(listMitigations, listMitigations),
    updateStatus: Object.assign(updateStatus, updateStatus),
    heatmap: Object.assign(heatmap, heatmap),
}

export default risks