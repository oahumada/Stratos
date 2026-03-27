import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getMatrixData
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:42
* @route '/api/scenarios/{id}/step2/data'
*/
export const getMatrixData = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMatrixData.url(args, options),
    method: 'get',
})

getMatrixData.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{id}/step2/data',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getMatrixData
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:42
* @route '/api/scenarios/{id}/step2/data'
*/
getMatrixData.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getMatrixData.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getMatrixData
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:42
* @route '/api/scenarios/{id}/step2/data'
*/
getMatrixData.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMatrixData.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getMatrixData
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:42
* @route '/api/scenarios/{id}/step2/data'
*/
getMatrixData.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getMatrixData.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::saveMapping
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:142
* @route '/api/scenarios/{id}/step2/mappings'
*/
export const saveMapping = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveMapping.url(args, options),
    method: 'post',
})

saveMapping.definition = {
    methods: ["post"],
    url: '/api/scenarios/{id}/step2/mappings',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::saveMapping
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:142
* @route '/api/scenarios/{id}/step2/mappings'
*/
saveMapping.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return saveMapping.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::saveMapping
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:142
* @route '/api/scenarios/{id}/step2/mappings'
*/
saveMapping.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveMapping.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::deleteMapping
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:202
* @route '/api/scenarios/{id}/step2/mappings/{mappingId}'
*/
export const deleteMapping = (args: { id: string | number, mappingId: string | number } | [id: string | number, mappingId: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMapping.url(args, options),
    method: 'delete',
})

deleteMapping.definition = {
    methods: ["delete"],
    url: '/api/scenarios/{id}/step2/mappings/{mappingId}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::deleteMapping
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:202
* @route '/api/scenarios/{id}/step2/mappings/{mappingId}'
*/
deleteMapping.url = (args: { id: string | number, mappingId: string | number } | [id: string | number, mappingId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            id: args[0],
            mappingId: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
        mappingId: args.mappingId,
    }

    return deleteMapping.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace('{mappingId}', parsedArgs.mappingId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::deleteMapping
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:202
* @route '/api/scenarios/{id}/step2/mappings/{mappingId}'
*/
deleteMapping.delete = (args: { id: string | number, mappingId: string | number } | [id: string | number, mappingId: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMapping.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::addRole
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:229
* @route '/api/scenarios/{id}/step2/roles'
*/
export const addRole = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addRole.url(args, options),
    method: 'post',
})

addRole.definition = {
    methods: ["post"],
    url: '/api/scenarios/{id}/step2/roles',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::addRole
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:229
* @route '/api/scenarios/{id}/step2/roles'
*/
addRole.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return addRole.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::addRole
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:229
* @route '/api/scenarios/{id}/step2/roles'
*/
addRole.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addRole.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getRoleForecasts
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:468
* @route '/api/scenarios/{id}/step2/role-forecasts'
*/
export const getRoleForecasts = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getRoleForecasts.url(args, options),
    method: 'get',
})

getRoleForecasts.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{id}/step2/role-forecasts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getRoleForecasts
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:468
* @route '/api/scenarios/{id}/step2/role-forecasts'
*/
getRoleForecasts.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getRoleForecasts.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getRoleForecasts
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:468
* @route '/api/scenarios/{id}/step2/role-forecasts'
*/
getRoleForecasts.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getRoleForecasts.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getRoleForecasts
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:468
* @route '/api/scenarios/{id}/step2/role-forecasts'
*/
getRoleForecasts.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getRoleForecasts.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getSkillGapsMatrix
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:509
* @route '/api/scenarios/{id}/step2/skill-gaps-matrix'
*/
export const getSkillGapsMatrix = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSkillGapsMatrix.url(args, options),
    method: 'get',
})

getSkillGapsMatrix.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{id}/step2/skill-gaps-matrix',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getSkillGapsMatrix
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:509
* @route '/api/scenarios/{id}/step2/skill-gaps-matrix'
*/
getSkillGapsMatrix.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getSkillGapsMatrix.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getSkillGapsMatrix
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:509
* @route '/api/scenarios/{id}/step2/skill-gaps-matrix'
*/
getSkillGapsMatrix.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSkillGapsMatrix.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getSkillGapsMatrix
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:509
* @route '/api/scenarios/{id}/step2/skill-gaps-matrix'
*/
getSkillGapsMatrix.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getSkillGapsMatrix.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getMatchingResults
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:592
* @route '/api/scenarios/{id}/step2/matching-results'
*/
export const getMatchingResults = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMatchingResults.url(args, options),
    method: 'get',
})

getMatchingResults.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{id}/step2/matching-results',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getMatchingResults
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:592
* @route '/api/scenarios/{id}/step2/matching-results'
*/
getMatchingResults.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getMatchingResults.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getMatchingResults
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:592
* @route '/api/scenarios/{id}/step2/matching-results'
*/
getMatchingResults.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMatchingResults.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getMatchingResults
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:592
* @route '/api/scenarios/{id}/step2/matching-results'
*/
getMatchingResults.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getMatchingResults.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getSuccessionPlans
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:645
* @route '/api/scenarios/{id}/step2/succession-plans'
*/
export const getSuccessionPlans = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSuccessionPlans.url(args, options),
    method: 'get',
})

getSuccessionPlans.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{id}/step2/succession-plans',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getSuccessionPlans
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:645
* @route '/api/scenarios/{id}/step2/succession-plans'
*/
getSuccessionPlans.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getSuccessionPlans.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getSuccessionPlans
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:645
* @route '/api/scenarios/{id}/step2/succession-plans'
*/
getSuccessionPlans.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSuccessionPlans.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getSuccessionPlans
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:645
* @route '/api/scenarios/{id}/step2/succession-plans'
*/
getSuccessionPlans.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getSuccessionPlans.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getCubeData
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:802
* @route '/api/scenarios/{id}/step2/cube'
*/
export const getCubeData = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCubeData.url(args, options),
    method: 'get',
})

getCubeData.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{id}/step2/cube',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getCubeData
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:802
* @route '/api/scenarios/{id}/step2/cube'
*/
getCubeData.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getCubeData.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getCubeData
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:802
* @route '/api/scenarios/{id}/step2/cube'
*/
getCubeData.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCubeData.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getCubeData
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:802
* @route '/api/scenarios/{id}/step2/cube'
*/
getCubeData.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCubeData.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::orchestrateCapabilities
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:373
* @route '/api/scenarios/{id}/step2/orchestrate-capabilities'
*/
export const orchestrateCapabilities = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: orchestrateCapabilities.url(args, options),
    method: 'post',
})

orchestrateCapabilities.definition = {
    methods: ["post"],
    url: '/api/scenarios/{id}/step2/orchestrate-capabilities',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::orchestrateCapabilities
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:373
* @route '/api/scenarios/{id}/step2/orchestrate-capabilities'
*/
orchestrateCapabilities.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return orchestrateCapabilities.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::orchestrateCapabilities
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:373
* @route '/api/scenarios/{id}/step2/orchestrate-capabilities'
*/
orchestrateCapabilities.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: orchestrateCapabilities.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::approveCube
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:919
* @route '/api/scenarios/{id}/step2/approve-cube'
*/
export const approveCube = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approveCube.url(args, options),
    method: 'post',
})

approveCube.definition = {
    methods: ["post"],
    url: '/api/scenarios/{id}/step2/approve-cube',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::approveCube
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:919
* @route '/api/scenarios/{id}/step2/approve-cube'
*/
approveCube.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return approveCube.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::approveCube
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:919
* @route '/api/scenarios/{id}/step2/approve-cube'
*/
approveCube.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approveCube.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::generateBars
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:985
* @route '/api/scenarios/{id}/step2/engine/generate-bars'
*/
export const generateBars = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateBars.url(args, options),
    method: 'post',
})

generateBars.definition = {
    methods: ["post"],
    url: '/api/scenarios/{id}/step2/engine/generate-bars',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::generateBars
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:985
* @route '/api/scenarios/{id}/step2/engine/generate-bars'
*/
generateBars.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return generateBars.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::generateBars
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:985
* @route '/api/scenarios/{id}/step2/engine/generate-bars'
*/
generateBars.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateBars.url(args, options),
    method: 'post',
})

const Step2RoleCompetencyController = { getMatrixData, saveMapping, deleteMapping, addRole, getRoleForecasts, getSkillGapsMatrix, getMatchingResults, getSuccessionPlans, getCubeData, orchestrateCapabilities, approveCube, generateBars }

export default Step2RoleCompetencyController