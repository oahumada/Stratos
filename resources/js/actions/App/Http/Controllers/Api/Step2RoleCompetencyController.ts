import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getMatrixData
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:32
* @route '/api/scenarios/{scenarioId}/step2/data'
*/
export const getMatrixData = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMatrixData.url(args, options),
    method: 'get',
})

getMatrixData.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenarioId}/step2/data',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getMatrixData
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:32
* @route '/api/scenarios/{scenarioId}/step2/data'
*/
getMatrixData.url = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getMatrixData.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getMatrixData
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:32
* @route '/api/scenarios/{scenarioId}/step2/data'
*/
getMatrixData.get = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMatrixData.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getMatrixData
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:32
* @route '/api/scenarios/{scenarioId}/step2/data'
*/
getMatrixData.head = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getMatrixData.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getMatrixData
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:32
* @route '/api/scenarios/{scenarioId}/step2/data'
*/
const getMatrixDataForm = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getMatrixData.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getMatrixData
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:32
* @route '/api/scenarios/{scenarioId}/step2/data'
*/
getMatrixDataForm.get = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getMatrixData.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getMatrixData
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:32
* @route '/api/scenarios/{scenarioId}/step2/data'
*/
getMatrixDataForm.head = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getMatrixData.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getMatrixData.form = getMatrixDataForm

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::saveMapping
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:97
* @route '/api/scenarios/{scenarioId}/step2/mappings'
*/
export const saveMapping = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveMapping.url(args, options),
    method: 'post',
})

saveMapping.definition = {
    methods: ["post"],
    url: '/api/scenarios/{scenarioId}/step2/mappings',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::saveMapping
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:97
* @route '/api/scenarios/{scenarioId}/step2/mappings'
*/
saveMapping.url = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return saveMapping.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::saveMapping
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:97
* @route '/api/scenarios/{scenarioId}/step2/mappings'
*/
saveMapping.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveMapping.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::saveMapping
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:97
* @route '/api/scenarios/{scenarioId}/step2/mappings'
*/
const saveMappingForm = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: saveMapping.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::saveMapping
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:97
* @route '/api/scenarios/{scenarioId}/step2/mappings'
*/
saveMappingForm.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: saveMapping.url(args, options),
    method: 'post',
})

saveMapping.form = saveMappingForm

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::deleteMapping
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:149
* @route '/api/scenarios/{scenarioId}/step2/mappings/{mappingId}'
*/
export const deleteMapping = (args: { scenarioId: string | number, mappingId: string | number } | [scenarioId: string | number, mappingId: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMapping.url(args, options),
    method: 'delete',
})

deleteMapping.definition = {
    methods: ["delete"],
    url: '/api/scenarios/{scenarioId}/step2/mappings/{mappingId}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::deleteMapping
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:149
* @route '/api/scenarios/{scenarioId}/step2/mappings/{mappingId}'
*/
deleteMapping.url = (args: { scenarioId: string | number, mappingId: string | number } | [scenarioId: string | number, mappingId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            scenarioId: args[0],
            mappingId: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenarioId: args.scenarioId,
        mappingId: args.mappingId,
    }

    return deleteMapping.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace('{mappingId}', parsedArgs.mappingId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::deleteMapping
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:149
* @route '/api/scenarios/{scenarioId}/step2/mappings/{mappingId}'
*/
deleteMapping.delete = (args: { scenarioId: string | number, mappingId: string | number } | [scenarioId: string | number, mappingId: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMapping.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::deleteMapping
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:149
* @route '/api/scenarios/{scenarioId}/step2/mappings/{mappingId}'
*/
const deleteMappingForm = (args: { scenarioId: string | number, mappingId: string | number } | [scenarioId: string | number, mappingId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deleteMapping.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::deleteMapping
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:149
* @route '/api/scenarios/{scenarioId}/step2/mappings/{mappingId}'
*/
deleteMappingForm.delete = (args: { scenarioId: string | number, mappingId: string | number } | [scenarioId: string | number, mappingId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deleteMapping.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

deleteMapping.form = deleteMappingForm

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::addRole
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:176
* @route '/api/scenarios/{scenarioId}/step2/roles'
*/
export const addRole = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addRole.url(args, options),
    method: 'post',
})

addRole.definition = {
    methods: ["post"],
    url: '/api/scenarios/{scenarioId}/step2/roles',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::addRole
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:176
* @route '/api/scenarios/{scenarioId}/step2/roles'
*/
addRole.url = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return addRole.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::addRole
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:176
* @route '/api/scenarios/{scenarioId}/step2/roles'
*/
addRole.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addRole.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::addRole
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:176
* @route '/api/scenarios/{scenarioId}/step2/roles'
*/
const addRoleForm = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: addRole.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::addRole
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:176
* @route '/api/scenarios/{scenarioId}/step2/roles'
*/
addRoleForm.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: addRole.url(args, options),
    method: 'post',
})

addRole.form = addRoleForm

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getRoleForecasts
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:244
* @route '/api/scenarios/{scenarioId}/step2/role-forecasts'
*/
export const getRoleForecasts = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getRoleForecasts.url(args, options),
    method: 'get',
})

getRoleForecasts.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenarioId}/step2/role-forecasts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getRoleForecasts
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:244
* @route '/api/scenarios/{scenarioId}/step2/role-forecasts'
*/
getRoleForecasts.url = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getRoleForecasts.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getRoleForecasts
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:244
* @route '/api/scenarios/{scenarioId}/step2/role-forecasts'
*/
getRoleForecasts.get = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getRoleForecasts.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getRoleForecasts
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:244
* @route '/api/scenarios/{scenarioId}/step2/role-forecasts'
*/
getRoleForecasts.head = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getRoleForecasts.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getRoleForecasts
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:244
* @route '/api/scenarios/{scenarioId}/step2/role-forecasts'
*/
const getRoleForecastsForm = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getRoleForecasts.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getRoleForecasts
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:244
* @route '/api/scenarios/{scenarioId}/step2/role-forecasts'
*/
getRoleForecastsForm.get = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getRoleForecasts.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getRoleForecasts
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:244
* @route '/api/scenarios/{scenarioId}/step2/role-forecasts'
*/
getRoleForecastsForm.head = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getRoleForecasts.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getRoleForecasts.form = getRoleForecastsForm

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getSkillGapsMatrix
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:280
* @route '/api/scenarios/{scenarioId}/step2/skill-gaps-matrix'
*/
export const getSkillGapsMatrix = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSkillGapsMatrix.url(args, options),
    method: 'get',
})

getSkillGapsMatrix.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenarioId}/step2/skill-gaps-matrix',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getSkillGapsMatrix
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:280
* @route '/api/scenarios/{scenarioId}/step2/skill-gaps-matrix'
*/
getSkillGapsMatrix.url = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getSkillGapsMatrix.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getSkillGapsMatrix
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:280
* @route '/api/scenarios/{scenarioId}/step2/skill-gaps-matrix'
*/
getSkillGapsMatrix.get = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSkillGapsMatrix.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getSkillGapsMatrix
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:280
* @route '/api/scenarios/{scenarioId}/step2/skill-gaps-matrix'
*/
getSkillGapsMatrix.head = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getSkillGapsMatrix.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getSkillGapsMatrix
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:280
* @route '/api/scenarios/{scenarioId}/step2/skill-gaps-matrix'
*/
const getSkillGapsMatrixForm = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getSkillGapsMatrix.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getSkillGapsMatrix
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:280
* @route '/api/scenarios/{scenarioId}/step2/skill-gaps-matrix'
*/
getSkillGapsMatrixForm.get = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getSkillGapsMatrix.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getSkillGapsMatrix
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:280
* @route '/api/scenarios/{scenarioId}/step2/skill-gaps-matrix'
*/
getSkillGapsMatrixForm.head = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getSkillGapsMatrix.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getSkillGapsMatrix.form = getSkillGapsMatrixForm

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getMatchingResults
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:330
* @route '/api/scenarios/{scenarioId}/step2/matching-results'
*/
export const getMatchingResults = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMatchingResults.url(args, options),
    method: 'get',
})

getMatchingResults.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenarioId}/step2/matching-results',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getMatchingResults
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:330
* @route '/api/scenarios/{scenarioId}/step2/matching-results'
*/
getMatchingResults.url = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getMatchingResults.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getMatchingResults
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:330
* @route '/api/scenarios/{scenarioId}/step2/matching-results'
*/
getMatchingResults.get = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMatchingResults.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getMatchingResults
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:330
* @route '/api/scenarios/{scenarioId}/step2/matching-results'
*/
getMatchingResults.head = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getMatchingResults.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getMatchingResults
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:330
* @route '/api/scenarios/{scenarioId}/step2/matching-results'
*/
const getMatchingResultsForm = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getMatchingResults.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getMatchingResults
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:330
* @route '/api/scenarios/{scenarioId}/step2/matching-results'
*/
getMatchingResultsForm.get = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getMatchingResults.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getMatchingResults
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:330
* @route '/api/scenarios/{scenarioId}/step2/matching-results'
*/
getMatchingResultsForm.head = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getMatchingResults.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getMatchingResults.form = getMatchingResultsForm

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getSuccessionPlans
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:349
* @route '/api/scenarios/{scenarioId}/step2/succession-plans'
*/
export const getSuccessionPlans = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSuccessionPlans.url(args, options),
    method: 'get',
})

getSuccessionPlans.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenarioId}/step2/succession-plans',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getSuccessionPlans
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:349
* @route '/api/scenarios/{scenarioId}/step2/succession-plans'
*/
getSuccessionPlans.url = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getSuccessionPlans.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getSuccessionPlans
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:349
* @route '/api/scenarios/{scenarioId}/step2/succession-plans'
*/
getSuccessionPlans.get = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSuccessionPlans.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getSuccessionPlans
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:349
* @route '/api/scenarios/{scenarioId}/step2/succession-plans'
*/
getSuccessionPlans.head = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getSuccessionPlans.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getSuccessionPlans
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:349
* @route '/api/scenarios/{scenarioId}/step2/succession-plans'
*/
const getSuccessionPlansForm = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getSuccessionPlans.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getSuccessionPlans
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:349
* @route '/api/scenarios/{scenarioId}/step2/succession-plans'
*/
getSuccessionPlansForm.get = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getSuccessionPlans.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Step2RoleCompetencyController::getSuccessionPlans
* @see app/Http/Controllers/Api/Step2RoleCompetencyController.php:349
* @route '/api/scenarios/{scenarioId}/step2/succession-plans'
*/
getSuccessionPlansForm.head = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getSuccessionPlans.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getSuccessionPlans.form = getSuccessionPlansForm

const Step2RoleCompetencyController = { getMatrixData, saveMapping, deleteMapping, addRole, getRoleForecasts, getSkillGapsMatrix, getMatchingResults, getSuccessionPlans }

export default Step2RoleCompetencyController