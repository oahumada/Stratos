import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::simulateGrowth
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:16
* @route '/api/strategic-planning/scenarios/{id}/simulate-growth'
*/
const simulateGrowth027f1a2913bcc4b1306096fc8848654f = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: simulateGrowth027f1a2913bcc4b1306096fc8848654f.url(args, options),
    method: 'post',
})

simulateGrowth027f1a2913bcc4b1306096fc8848654f.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/{id}/simulate-growth',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::simulateGrowth
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:16
* @route '/api/strategic-planning/scenarios/{id}/simulate-growth'
*/
simulateGrowth027f1a2913bcc4b1306096fc8848654f.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return simulateGrowth027f1a2913bcc4b1306096fc8848654f.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::simulateGrowth
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:16
* @route '/api/strategic-planning/scenarios/{id}/simulate-growth'
*/
simulateGrowth027f1a2913bcc4b1306096fc8848654f.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: simulateGrowth027f1a2913bcc4b1306096fc8848654f.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::simulateGrowth
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:16
* @route '/api/strategic-planning/scenarios/{id}/simulate-growth'
*/
const simulateGrowth027f1a2913bcc4b1306096fc8848654fForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: simulateGrowth027f1a2913bcc4b1306096fc8848654f.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::simulateGrowth
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:16
* @route '/api/strategic-planning/scenarios/{id}/simulate-growth'
*/
simulateGrowth027f1a2913bcc4b1306096fc8848654fForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: simulateGrowth027f1a2913bcc4b1306096fc8848654f.url(args, options),
    method: 'post',
})

simulateGrowth027f1a2913bcc4b1306096fc8848654f.form = simulateGrowth027f1a2913bcc4b1306096fc8848654fForm
/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::simulateGrowth
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:16
* @route '/scenarios/{id}/simulate-growth'
*/
const simulateGrowth1faaf4ba472ddd4b41ea310e43be4ae4 = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: simulateGrowth1faaf4ba472ddd4b41ea310e43be4ae4.url(args, options),
    method: 'post',
})

simulateGrowth1faaf4ba472ddd4b41ea310e43be4ae4.definition = {
    methods: ["post"],
    url: '/scenarios/{id}/simulate-growth',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::simulateGrowth
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:16
* @route '/scenarios/{id}/simulate-growth'
*/
simulateGrowth1faaf4ba472ddd4b41ea310e43be4ae4.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return simulateGrowth1faaf4ba472ddd4b41ea310e43be4ae4.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::simulateGrowth
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:16
* @route '/scenarios/{id}/simulate-growth'
*/
simulateGrowth1faaf4ba472ddd4b41ea310e43be4ae4.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: simulateGrowth1faaf4ba472ddd4b41ea310e43be4ae4.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::simulateGrowth
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:16
* @route '/scenarios/{id}/simulate-growth'
*/
const simulateGrowth1faaf4ba472ddd4b41ea310e43be4ae4Form = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: simulateGrowth1faaf4ba472ddd4b41ea310e43be4ae4.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::simulateGrowth
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:16
* @route '/scenarios/{id}/simulate-growth'
*/
simulateGrowth1faaf4ba472ddd4b41ea310e43be4ae4Form.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: simulateGrowth1faaf4ba472ddd4b41ea310e43be4ae4.url(args, options),
    method: 'post',
})

simulateGrowth1faaf4ba472ddd4b41ea310e43be4ae4.form = simulateGrowth1faaf4ba472ddd4b41ea310e43be4ae4Form

export const simulateGrowth = {
    '/api/strategic-planning/scenarios/{id}/simulate-growth': simulateGrowth027f1a2913bcc4b1306096fc8848654f,
    '/scenarios/{id}/simulate-growth': simulateGrowth1faaf4ba472ddd4b41ea310e43be4ae4,
}

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getMitigationPlan
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:133
* @route '/api/strategic-planning/scenarios/{id}/mitigate'
*/
const getMitigationPlanf4f90e7852745dde0a49f6713a1be25a = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: getMitigationPlanf4f90e7852745dde0a49f6713a1be25a.url(args, options),
    method: 'post',
})

getMitigationPlanf4f90e7852745dde0a49f6713a1be25a.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/{id}/mitigate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getMitigationPlan
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:133
* @route '/api/strategic-planning/scenarios/{id}/mitigate'
*/
getMitigationPlanf4f90e7852745dde0a49f6713a1be25a.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getMitigationPlanf4f90e7852745dde0a49f6713a1be25a.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getMitigationPlan
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:133
* @route '/api/strategic-planning/scenarios/{id}/mitigate'
*/
getMitigationPlanf4f90e7852745dde0a49f6713a1be25a.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: getMitigationPlanf4f90e7852745dde0a49f6713a1be25a.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getMitigationPlan
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:133
* @route '/api/strategic-planning/scenarios/{id}/mitigate'
*/
const getMitigationPlanf4f90e7852745dde0a49f6713a1be25aForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: getMitigationPlanf4f90e7852745dde0a49f6713a1be25a.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getMitigationPlan
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:133
* @route '/api/strategic-planning/scenarios/{id}/mitigate'
*/
getMitigationPlanf4f90e7852745dde0a49f6713a1be25aForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: getMitigationPlanf4f90e7852745dde0a49f6713a1be25a.url(args, options),
    method: 'post',
})

getMitigationPlanf4f90e7852745dde0a49f6713a1be25a.form = getMitigationPlanf4f90e7852745dde0a49f6713a1be25aForm
/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getMitigationPlan
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:133
* @route '/scenarios/{id}/mitigate'
*/
const getMitigationPlanca0ea349e00ad41e75a55353fe780fcf = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: getMitigationPlanca0ea349e00ad41e75a55353fe780fcf.url(args, options),
    method: 'post',
})

getMitigationPlanca0ea349e00ad41e75a55353fe780fcf.definition = {
    methods: ["post"],
    url: '/scenarios/{id}/mitigate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getMitigationPlan
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:133
* @route '/scenarios/{id}/mitigate'
*/
getMitigationPlanca0ea349e00ad41e75a55353fe780fcf.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getMitigationPlanca0ea349e00ad41e75a55353fe780fcf.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getMitigationPlan
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:133
* @route '/scenarios/{id}/mitigate'
*/
getMitigationPlanca0ea349e00ad41e75a55353fe780fcf.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: getMitigationPlanca0ea349e00ad41e75a55353fe780fcf.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getMitigationPlan
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:133
* @route '/scenarios/{id}/mitigate'
*/
const getMitigationPlanca0ea349e00ad41e75a55353fe780fcfForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: getMitigationPlanca0ea349e00ad41e75a55353fe780fcf.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getMitigationPlan
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:133
* @route '/scenarios/{id}/mitigate'
*/
getMitigationPlanca0ea349e00ad41e75a55353fe780fcfForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: getMitigationPlanca0ea349e00ad41e75a55353fe780fcf.url(args, options),
    method: 'post',
})

getMitigationPlanca0ea349e00ad41e75a55353fe780fcf.form = getMitigationPlanca0ea349e00ad41e75a55353fe780fcfForm

export const getMitigationPlan = {
    '/api/strategic-planning/scenarios/{id}/mitigate': getMitigationPlanf4f90e7852745dde0a49f6713a1be25a,
    '/scenarios/{id}/mitigate': getMitigationPlanca0ea349e00ad41e75a55353fe780fcf,
}

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getCriticalTalents
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:85
* @route '/api/strategic-planning/critical-talents'
*/
export const getCriticalTalents = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCriticalTalents.url(options),
    method: 'get',
})

getCriticalTalents.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/critical-talents',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getCriticalTalents
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:85
* @route '/api/strategic-planning/critical-talents'
*/
getCriticalTalents.url = (options?: RouteQueryOptions) => {
    return getCriticalTalents.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getCriticalTalents
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:85
* @route '/api/strategic-planning/critical-talents'
*/
getCriticalTalents.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCriticalTalents.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getCriticalTalents
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:85
* @route '/api/strategic-planning/critical-talents'
*/
getCriticalTalents.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCriticalTalents.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getCriticalTalents
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:85
* @route '/api/strategic-planning/critical-talents'
*/
const getCriticalTalentsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCriticalTalents.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getCriticalTalents
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:85
* @route '/api/strategic-planning/critical-talents'
*/
getCriticalTalentsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCriticalTalents.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioSimulationController::getCriticalTalents
* @see app/Http/Controllers/Api/ScenarioSimulationController.php:85
* @route '/api/strategic-planning/critical-talents'
*/
getCriticalTalentsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCriticalTalents.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getCriticalTalents.form = getCriticalTalentsForm

const ScenarioSimulationController = { simulateGrowth, getMitigationPlan, getCriticalTalents }

export default ScenarioSimulationController