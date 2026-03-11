import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ScenarioIQController::simulateAttrition
* @see app/Http/Controllers/Api/ScenarioIQController.php:28
* @route '/api/strategic-planning/scenarios/{scenarioId}/crisis/attrition'
*/
export const simulateAttrition = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: simulateAttrition.url(args, options),
    method: 'post',
})

simulateAttrition.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/{scenarioId}/crisis/attrition',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::simulateAttrition
* @see app/Http/Controllers/Api/ScenarioIQController.php:28
* @route '/api/strategic-planning/scenarios/{scenarioId}/crisis/attrition'
*/
simulateAttrition.url = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenarioId: args }
    }

    if (Array.isArray(args)) {
        args = {
            scenarioId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenarioId: args.scenarioId,
    }

    return simulateAttrition.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::simulateAttrition
* @see app/Http/Controllers/Api/ScenarioIQController.php:28
* @route '/api/strategic-planning/scenarios/{scenarioId}/crisis/attrition'
*/
simulateAttrition.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: simulateAttrition.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::simulateAttrition
* @see app/Http/Controllers/Api/ScenarioIQController.php:28
* @route '/api/strategic-planning/scenarios/{scenarioId}/crisis/attrition'
*/
const simulateAttritionForm = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: simulateAttrition.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::simulateAttrition
* @see app/Http/Controllers/Api/ScenarioIQController.php:28
* @route '/api/strategic-planning/scenarios/{scenarioId}/crisis/attrition'
*/
simulateAttritionForm.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: simulateAttrition.url(args, options),
    method: 'post',
})

simulateAttrition.form = simulateAttritionForm

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::simulateObsolescence
* @see app/Http/Controllers/Api/ScenarioIQController.php:48
* @route '/api/strategic-planning/scenarios/{scenarioId}/crisis/obsolescence'
*/
export const simulateObsolescence = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: simulateObsolescence.url(args, options),
    method: 'post',
})

simulateObsolescence.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/{scenarioId}/crisis/obsolescence',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::simulateObsolescence
* @see app/Http/Controllers/Api/ScenarioIQController.php:48
* @route '/api/strategic-planning/scenarios/{scenarioId}/crisis/obsolescence'
*/
simulateObsolescence.url = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenarioId: args }
    }

    if (Array.isArray(args)) {
        args = {
            scenarioId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenarioId: args.scenarioId,
    }

    return simulateObsolescence.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::simulateObsolescence
* @see app/Http/Controllers/Api/ScenarioIQController.php:48
* @route '/api/strategic-planning/scenarios/{scenarioId}/crisis/obsolescence'
*/
simulateObsolescence.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: simulateObsolescence.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::simulateObsolescence
* @see app/Http/Controllers/Api/ScenarioIQController.php:48
* @route '/api/strategic-planning/scenarios/{scenarioId}/crisis/obsolescence'
*/
const simulateObsolescenceForm = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: simulateObsolescence.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::simulateObsolescence
* @see app/Http/Controllers/Api/ScenarioIQController.php:48
* @route '/api/strategic-planning/scenarios/{scenarioId}/crisis/obsolescence'
*/
simulateObsolescenceForm.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: simulateObsolescence.url(args, options),
    method: 'post',
})

simulateObsolescence.form = simulateObsolescenceForm

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::simulateRestructuring
* @see app/Http/Controllers/Api/ScenarioIQController.php:68
* @route '/api/strategic-planning/scenarios/{scenarioId}/crisis/restructuring'
*/
export const simulateRestructuring = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: simulateRestructuring.url(args, options),
    method: 'post',
})

simulateRestructuring.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/{scenarioId}/crisis/restructuring',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::simulateRestructuring
* @see app/Http/Controllers/Api/ScenarioIQController.php:68
* @route '/api/strategic-planning/scenarios/{scenarioId}/crisis/restructuring'
*/
simulateRestructuring.url = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenarioId: args }
    }

    if (Array.isArray(args)) {
        args = {
            scenarioId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenarioId: args.scenarioId,
    }

    return simulateRestructuring.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::simulateRestructuring
* @see app/Http/Controllers/Api/ScenarioIQController.php:68
* @route '/api/strategic-planning/scenarios/{scenarioId}/crisis/restructuring'
*/
simulateRestructuring.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: simulateRestructuring.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::simulateRestructuring
* @see app/Http/Controllers/Api/ScenarioIQController.php:68
* @route '/api/strategic-planning/scenarios/{scenarioId}/crisis/restructuring'
*/
const simulateRestructuringForm = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: simulateRestructuring.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::simulateRestructuring
* @see app/Http/Controllers/Api/ScenarioIQController.php:68
* @route '/api/strategic-planning/scenarios/{scenarioId}/crisis/restructuring'
*/
simulateRestructuringForm.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: simulateRestructuring.url(args, options),
    method: 'post',
})

simulateRestructuring.form = simulateRestructuringForm

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::getCareerPaths
* @see app/Http/Controllers/Api/ScenarioIQController.php:90
* @route '/api/career-paths/{peopleId}'
*/
export const getCareerPaths = (args: { peopleId: string | number } | [peopleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCareerPaths.url(args, options),
    method: 'get',
})

getCareerPaths.definition = {
    methods: ["get","head"],
    url: '/api/career-paths/{peopleId}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::getCareerPaths
* @see app/Http/Controllers/Api/ScenarioIQController.php:90
* @route '/api/career-paths/{peopleId}'
*/
getCareerPaths.url = (args: { peopleId: string | number } | [peopleId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { peopleId: args }
    }

    if (Array.isArray(args)) {
        args = {
            peopleId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        peopleId: args.peopleId,
    }

    return getCareerPaths.definition.url
            .replace('{peopleId}', parsedArgs.peopleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::getCareerPaths
* @see app/Http/Controllers/Api/ScenarioIQController.php:90
* @route '/api/career-paths/{peopleId}'
*/
getCareerPaths.get = (args: { peopleId: string | number } | [peopleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCareerPaths.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::getCareerPaths
* @see app/Http/Controllers/Api/ScenarioIQController.php:90
* @route '/api/career-paths/{peopleId}'
*/
getCareerPaths.head = (args: { peopleId: string | number } | [peopleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCareerPaths.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::getCareerPaths
* @see app/Http/Controllers/Api/ScenarioIQController.php:90
* @route '/api/career-paths/{peopleId}'
*/
const getCareerPathsForm = (args: { peopleId: string | number } | [peopleId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCareerPaths.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::getCareerPaths
* @see app/Http/Controllers/Api/ScenarioIQController.php:90
* @route '/api/career-paths/{peopleId}'
*/
getCareerPathsForm.get = (args: { peopleId: string | number } | [peopleId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCareerPaths.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::getCareerPaths
* @see app/Http/Controllers/Api/ScenarioIQController.php:90
* @route '/api/career-paths/{peopleId}'
*/
getCareerPathsForm.head = (args: { peopleId: string | number } | [peopleId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCareerPaths.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getCareerPaths.form = getCareerPathsForm

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::getOptimalRoute
* @see app/Http/Controllers/Api/ScenarioIQController.php:104
* @route '/api/career-paths/route/{fromRoleId}/{toRoleId}'
*/
export const getOptimalRoute = (args: { fromRoleId: string | number, toRoleId: string | number } | [fromRoleId: string | number, toRoleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getOptimalRoute.url(args, options),
    method: 'get',
})

getOptimalRoute.definition = {
    methods: ["get","head"],
    url: '/api/career-paths/route/{fromRoleId}/{toRoleId}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::getOptimalRoute
* @see app/Http/Controllers/Api/ScenarioIQController.php:104
* @route '/api/career-paths/route/{fromRoleId}/{toRoleId}'
*/
getOptimalRoute.url = (args: { fromRoleId: string | number, toRoleId: string | number } | [fromRoleId: string | number, toRoleId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            fromRoleId: args[0],
            toRoleId: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        fromRoleId: args.fromRoleId,
        toRoleId: args.toRoleId,
    }

    return getOptimalRoute.definition.url
            .replace('{fromRoleId}', parsedArgs.fromRoleId.toString())
            .replace('{toRoleId}', parsedArgs.toRoleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::getOptimalRoute
* @see app/Http/Controllers/Api/ScenarioIQController.php:104
* @route '/api/career-paths/route/{fromRoleId}/{toRoleId}'
*/
getOptimalRoute.get = (args: { fromRoleId: string | number, toRoleId: string | number } | [fromRoleId: string | number, toRoleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getOptimalRoute.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::getOptimalRoute
* @see app/Http/Controllers/Api/ScenarioIQController.php:104
* @route '/api/career-paths/route/{fromRoleId}/{toRoleId}'
*/
getOptimalRoute.head = (args: { fromRoleId: string | number, toRoleId: string | number } | [fromRoleId: string | number, toRoleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getOptimalRoute.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::getOptimalRoute
* @see app/Http/Controllers/Api/ScenarioIQController.php:104
* @route '/api/career-paths/route/{fromRoleId}/{toRoleId}'
*/
const getOptimalRouteForm = (args: { fromRoleId: string | number, toRoleId: string | number } | [fromRoleId: string | number, toRoleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getOptimalRoute.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::getOptimalRoute
* @see app/Http/Controllers/Api/ScenarioIQController.php:104
* @route '/api/career-paths/route/{fromRoleId}/{toRoleId}'
*/
getOptimalRouteForm.get = (args: { fromRoleId: string | number, toRoleId: string | number } | [fromRoleId: string | number, toRoleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getOptimalRoute.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::getOptimalRoute
* @see app/Http/Controllers/Api/ScenarioIQController.php:104
* @route '/api/career-paths/route/{fromRoleId}/{toRoleId}'
*/
getOptimalRouteForm.head = (args: { fromRoleId: string | number, toRoleId: string | number } | [fromRoleId: string | number, toRoleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getOptimalRoute.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getOptimalRoute.form = getOptimalRouteForm

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::getMobilityMap
* @see app/Http/Controllers/Api/ScenarioIQController.php:118
* @route '/api/career-paths/mobility-map/{organizationId}'
*/
export const getMobilityMap = (args: { organizationId: string | number } | [organizationId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMobilityMap.url(args, options),
    method: 'get',
})

getMobilityMap.definition = {
    methods: ["get","head"],
    url: '/api/career-paths/mobility-map/{organizationId}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::getMobilityMap
* @see app/Http/Controllers/Api/ScenarioIQController.php:118
* @route '/api/career-paths/mobility-map/{organizationId}'
*/
getMobilityMap.url = (args: { organizationId: string | number } | [organizationId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organizationId: args }
    }

    if (Array.isArray(args)) {
        args = {
            organizationId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organizationId: args.organizationId,
    }

    return getMobilityMap.definition.url
            .replace('{organizationId}', parsedArgs.organizationId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::getMobilityMap
* @see app/Http/Controllers/Api/ScenarioIQController.php:118
* @route '/api/career-paths/mobility-map/{organizationId}'
*/
getMobilityMap.get = (args: { organizationId: string | number } | [organizationId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMobilityMap.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::getMobilityMap
* @see app/Http/Controllers/Api/ScenarioIQController.php:118
* @route '/api/career-paths/mobility-map/{organizationId}'
*/
getMobilityMap.head = (args: { organizationId: string | number } | [organizationId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getMobilityMap.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::getMobilityMap
* @see app/Http/Controllers/Api/ScenarioIQController.php:118
* @route '/api/career-paths/mobility-map/{organizationId}'
*/
const getMobilityMapForm = (args: { organizationId: string | number } | [organizationId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getMobilityMap.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::getMobilityMap
* @see app/Http/Controllers/Api/ScenarioIQController.php:118
* @route '/api/career-paths/mobility-map/{organizationId}'
*/
getMobilityMapForm.get = (args: { organizationId: string | number } | [organizationId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getMobilityMap.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::getMobilityMap
* @see app/Http/Controllers/Api/ScenarioIQController.php:118
* @route '/api/career-paths/mobility-map/{organizationId}'
*/
getMobilityMapForm.head = (args: { organizationId: string | number } | [organizationId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getMobilityMap.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getMobilityMap.form = getMobilityMapForm

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::predictTransition
* @see app/Http/Controllers/Api/ScenarioIQController.php:132
* @route '/api/career-paths/predict/{peopleId}/{targetRoleId}'
*/
export const predictTransition = (args: { peopleId: string | number, targetRoleId: string | number } | [peopleId: string | number, targetRoleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: predictTransition.url(args, options),
    method: 'get',
})

predictTransition.definition = {
    methods: ["get","head"],
    url: '/api/career-paths/predict/{peopleId}/{targetRoleId}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::predictTransition
* @see app/Http/Controllers/Api/ScenarioIQController.php:132
* @route '/api/career-paths/predict/{peopleId}/{targetRoleId}'
*/
predictTransition.url = (args: { peopleId: string | number, targetRoleId: string | number } | [peopleId: string | number, targetRoleId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            peopleId: args[0],
            targetRoleId: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        peopleId: args.peopleId,
        targetRoleId: args.targetRoleId,
    }

    return predictTransition.definition.url
            .replace('{peopleId}', parsedArgs.peopleId.toString())
            .replace('{targetRoleId}', parsedArgs.targetRoleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::predictTransition
* @see app/Http/Controllers/Api/ScenarioIQController.php:132
* @route '/api/career-paths/predict/{peopleId}/{targetRoleId}'
*/
predictTransition.get = (args: { peopleId: string | number, targetRoleId: string | number } | [peopleId: string | number, targetRoleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: predictTransition.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::predictTransition
* @see app/Http/Controllers/Api/ScenarioIQController.php:132
* @route '/api/career-paths/predict/{peopleId}/{targetRoleId}'
*/
predictTransition.head = (args: { peopleId: string | number, targetRoleId: string | number } | [peopleId: string | number, targetRoleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: predictTransition.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::predictTransition
* @see app/Http/Controllers/Api/ScenarioIQController.php:132
* @route '/api/career-paths/predict/{peopleId}/{targetRoleId}'
*/
const predictTransitionForm = (args: { peopleId: string | number, targetRoleId: string | number } | [peopleId: string | number, targetRoleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: predictTransition.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::predictTransition
* @see app/Http/Controllers/Api/ScenarioIQController.php:132
* @route '/api/career-paths/predict/{peopleId}/{targetRoleId}'
*/
predictTransitionForm.get = (args: { peopleId: string | number, targetRoleId: string | number } | [peopleId: string | number, targetRoleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: predictTransition.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::predictTransition
* @see app/Http/Controllers/Api/ScenarioIQController.php:132
* @route '/api/career-paths/predict/{peopleId}/{targetRoleId}'
*/
predictTransitionForm.head = (args: { peopleId: string | number, targetRoleId: string | number } | [peopleId: string | number, targetRoleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: predictTransition.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

predictTransition.form = predictTransitionForm

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::runAgenticSimulation
* @see app/Http/Controllers/Api/ScenarioIQController.php:148
* @route '/api/strategic-planning/scenarios/{scenarioId}/agentic-simulation'
*/
export const runAgenticSimulation = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: runAgenticSimulation.url(args, options),
    method: 'post',
})

runAgenticSimulation.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/{scenarioId}/agentic-simulation',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::runAgenticSimulation
* @see app/Http/Controllers/Api/ScenarioIQController.php:148
* @route '/api/strategic-planning/scenarios/{scenarioId}/agentic-simulation'
*/
runAgenticSimulation.url = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenarioId: args }
    }

    if (Array.isArray(args)) {
        args = {
            scenarioId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenarioId: args.scenarioId,
    }

    return runAgenticSimulation.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::runAgenticSimulation
* @see app/Http/Controllers/Api/ScenarioIQController.php:148
* @route '/api/strategic-planning/scenarios/{scenarioId}/agentic-simulation'
*/
runAgenticSimulation.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: runAgenticSimulation.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::runAgenticSimulation
* @see app/Http/Controllers/Api/ScenarioIQController.php:148
* @route '/api/strategic-planning/scenarios/{scenarioId}/agentic-simulation'
*/
const runAgenticSimulationForm = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: runAgenticSimulation.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::runAgenticSimulation
* @see app/Http/Controllers/Api/ScenarioIQController.php:148
* @route '/api/strategic-planning/scenarios/{scenarioId}/agentic-simulation'
*/
runAgenticSimulationForm.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: runAgenticSimulation.url(args, options),
    method: 'post',
})

runAgenticSimulation.form = runAgenticSimulationForm

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::quickWhatIf
* @see app/Http/Controllers/Api/ScenarioIQController.php:176
* @route '/api/strategic-planning/scenarios/{scenarioId}/what-if'
*/
export const quickWhatIf = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: quickWhatIf.url(args, options),
    method: 'post',
})

quickWhatIf.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/{scenarioId}/what-if',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::quickWhatIf
* @see app/Http/Controllers/Api/ScenarioIQController.php:176
* @route '/api/strategic-planning/scenarios/{scenarioId}/what-if'
*/
quickWhatIf.url = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenarioId: args }
    }

    if (Array.isArray(args)) {
        args = {
            scenarioId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenarioId: args.scenarioId,
    }

    return quickWhatIf.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::quickWhatIf
* @see app/Http/Controllers/Api/ScenarioIQController.php:176
* @route '/api/strategic-planning/scenarios/{scenarioId}/what-if'
*/
quickWhatIf.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: quickWhatIf.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::quickWhatIf
* @see app/Http/Controllers/Api/ScenarioIQController.php:176
* @route '/api/strategic-planning/scenarios/{scenarioId}/what-if'
*/
const quickWhatIfForm = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: quickWhatIf.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioIQController::quickWhatIf
* @see app/Http/Controllers/Api/ScenarioIQController.php:176
* @route '/api/strategic-planning/scenarios/{scenarioId}/what-if'
*/
quickWhatIfForm.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: quickWhatIf.url(args, options),
    method: 'post',
})

quickWhatIf.form = quickWhatIfForm

const ScenarioIQController = { simulateAttrition, simulateObsolescence, simulateRestructuring, getCareerPaths, getOptimalRoute, getMobilityMap, predictTransition, runAgenticSimulation, quickWhatIf }

export default ScenarioIQController