import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::compareScenarios
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:21
* @route '/api/scenarios/compare'
*/
export const compareScenarios = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: compareScenarios.url(options),
    method: 'post',
})

compareScenarios.definition = {
    methods: ["post"],
    url: '/api/scenarios/compare',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::compareScenarios
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:21
* @route '/api/scenarios/compare'
*/
compareScenarios.url = (options?: RouteQueryOptions) => {
    return compareScenarios.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::compareScenarios
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:21
* @route '/api/scenarios/compare'
*/
compareScenarios.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: compareScenarios.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::analytics
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:67
* @route '/api/scenarios/{scenario}/analytics'
*/
export const analytics = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: analytics.url(args, options),
    method: 'get',
})

analytics.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenario}/analytics',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::analytics
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:67
* @route '/api/scenarios/{scenario}/analytics'
*/
analytics.url = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return analytics.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::analytics
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:67
* @route '/api/scenarios/{scenario}/analytics'
*/
analytics.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: analytics.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::analytics
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:67
* @route '/api/scenarios/{scenario}/analytics'
*/
analytics.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: analytics.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::financialImpact
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:95
* @route '/api/scenarios/{scenario}/financial-impact'
*/
export const financialImpact = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: financialImpact.url(args, options),
    method: 'get',
})

financialImpact.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenario}/financial-impact',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::financialImpact
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:95
* @route '/api/scenarios/{scenario}/financial-impact'
*/
financialImpact.url = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return financialImpact.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::financialImpact
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:95
* @route '/api/scenarios/{scenario}/financial-impact'
*/
financialImpact.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: financialImpact.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::financialImpact
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:95
* @route '/api/scenarios/{scenario}/financial-impact'
*/
financialImpact.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: financialImpact.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::riskAssessment
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:111
* @route '/api/scenarios/{scenario}/risk-assessment'
*/
export const riskAssessment = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: riskAssessment.url(args, options),
    method: 'get',
})

riskAssessment.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenario}/risk-assessment',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::riskAssessment
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:111
* @route '/api/scenarios/{scenario}/risk-assessment'
*/
riskAssessment.url = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return riskAssessment.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::riskAssessment
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:111
* @route '/api/scenarios/{scenario}/risk-assessment'
*/
riskAssessment.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: riskAssessment.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::riskAssessment
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:111
* @route '/api/scenarios/{scenario}/risk-assessment'
*/
riskAssessment.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: riskAssessment.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::skillGaps
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:127
* @route '/api/scenarios/{scenario}/skill-gaps'
*/
export const skillGaps = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: skillGaps.url(args, options),
    method: 'get',
})

skillGaps.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenario}/skill-gaps',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::skillGaps
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:127
* @route '/api/scenarios/{scenario}/skill-gaps'
*/
skillGaps.url = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return skillGaps.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::skillGaps
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:127
* @route '/api/scenarios/{scenario}/skill-gaps'
*/
skillGaps.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: skillGaps.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioAnalyticsController::skillGaps
* @see app/Http/Controllers/Api/ScenarioAnalyticsController.php:127
* @route '/api/scenarios/{scenario}/skill-gaps'
*/
skillGaps.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: skillGaps.url(args, options),
    method: 'head',
})

const ScenarioAnalyticsController = { compareScenarios, analytics, financialImpact, riskAssessment, skillGaps }

export default ScenarioAnalyticsController