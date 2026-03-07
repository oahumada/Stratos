import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ScenarioController::listScenarios
* @see app/Http/Controllers/Api/ScenarioController.php:67
* @route '/api/strategic-planning/scenarios'
*/
export const listScenarios = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listScenarios.url(options),
    method: 'get',
})

listScenarios.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenarios',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::listScenarios
* @see app/Http/Controllers/Api/ScenarioController.php:67
* @route '/api/strategic-planning/scenarios'
*/
listScenarios.url = (options?: RouteQueryOptions) => {
    return listScenarios.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::listScenarios
* @see app/Http/Controllers/Api/ScenarioController.php:67
* @route '/api/strategic-planning/scenarios'
*/
listScenarios.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listScenarios.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::listScenarios
* @see app/Http/Controllers/Api/ScenarioController.php:67
* @route '/api/strategic-planning/scenarios'
*/
listScenarios.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: listScenarios.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::listScenarios
* @see app/Http/Controllers/Api/ScenarioController.php:67
* @route '/api/strategic-planning/scenarios'
*/
const listScenariosForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listScenarios.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::listScenarios
* @see app/Http/Controllers/Api/ScenarioController.php:67
* @route '/api/strategic-planning/scenarios'
*/
listScenariosForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listScenarios.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::listScenarios
* @see app/Http/Controllers/Api/ScenarioController.php:67
* @route '/api/strategic-planning/scenarios'
*/
listScenariosForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listScenarios.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

listScenarios.form = listScenariosForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::showScenario
* @see app/Http/Controllers/Api/ScenarioController.php:230
* @route '/api/strategic-planning/scenarios/{id}'
*/
export const showScenario = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showScenario.url(args, options),
    method: 'get',
})

showScenario.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenarios/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::showScenario
* @see app/Http/Controllers/Api/ScenarioController.php:230
* @route '/api/strategic-planning/scenarios/{id}'
*/
showScenario.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return showScenario.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::showScenario
* @see app/Http/Controllers/Api/ScenarioController.php:230
* @route '/api/strategic-planning/scenarios/{id}'
*/
showScenario.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showScenario.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::showScenario
* @see app/Http/Controllers/Api/ScenarioController.php:230
* @route '/api/strategic-planning/scenarios/{id}'
*/
showScenario.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showScenario.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::showScenario
* @see app/Http/Controllers/Api/ScenarioController.php:230
* @route '/api/strategic-planning/scenarios/{id}'
*/
const showScenarioForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showScenario.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::showScenario
* @see app/Http/Controllers/Api/ScenarioController.php:230
* @route '/api/strategic-planning/scenarios/{id}'
*/
showScenarioForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showScenario.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::showScenario
* @see app/Http/Controllers/Api/ScenarioController.php:230
* @route '/api/strategic-planning/scenarios/{id}'
*/
showScenarioForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showScenario.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

showScenario.form = showScenarioForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::getCapabilityTree
* @see app/Http/Controllers/Api/ScenarioController.php:96
* @route '/api/strategic-planning/scenarios/{id}/capability-tree'
*/
export const getCapabilityTree = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCapabilityTree.url(args, options),
    method: 'get',
})

getCapabilityTree.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenarios/{id}/capability-tree',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::getCapabilityTree
* @see app/Http/Controllers/Api/ScenarioController.php:96
* @route '/api/strategic-planning/scenarios/{id}/capability-tree'
*/
getCapabilityTree.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getCapabilityTree.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::getCapabilityTree
* @see app/Http/Controllers/Api/ScenarioController.php:96
* @route '/api/strategic-planning/scenarios/{id}/capability-tree'
*/
getCapabilityTree.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCapabilityTree.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getCapabilityTree
* @see app/Http/Controllers/Api/ScenarioController.php:96
* @route '/api/strategic-planning/scenarios/{id}/capability-tree'
*/
getCapabilityTree.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCapabilityTree.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getCapabilityTree
* @see app/Http/Controllers/Api/ScenarioController.php:96
* @route '/api/strategic-planning/scenarios/{id}/capability-tree'
*/
const getCapabilityTreeForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCapabilityTree.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getCapabilityTree
* @see app/Http/Controllers/Api/ScenarioController.php:96
* @route '/api/strategic-planning/scenarios/{id}/capability-tree'
*/
getCapabilityTreeForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCapabilityTree.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getCapabilityTree
* @see app/Http/Controllers/Api/ScenarioController.php:96
* @route '/api/strategic-planning/scenarios/{id}/capability-tree'
*/
getCapabilityTreeForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCapabilityTree.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getCapabilityTree.form = getCapabilityTreeForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::getVersions
* @see app/Http/Controllers/Api/ScenarioController.php:761
* @route '/api/strategic-planning/scenarios/{id}/versions'
*/
export const getVersions = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getVersions.url(args, options),
    method: 'get',
})

getVersions.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenarios/{id}/versions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::getVersions
* @see app/Http/Controllers/Api/ScenarioController.php:761
* @route '/api/strategic-planning/scenarios/{id}/versions'
*/
getVersions.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getVersions.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::getVersions
* @see app/Http/Controllers/Api/ScenarioController.php:761
* @route '/api/strategic-planning/scenarios/{id}/versions'
*/
getVersions.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getVersions.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getVersions
* @see app/Http/Controllers/Api/ScenarioController.php:761
* @route '/api/strategic-planning/scenarios/{id}/versions'
*/
getVersions.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getVersions.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getVersions
* @see app/Http/Controllers/Api/ScenarioController.php:761
* @route '/api/strategic-planning/scenarios/{id}/versions'
*/
const getVersionsForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getVersions.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getVersions
* @see app/Http/Controllers/Api/ScenarioController.php:761
* @route '/api/strategic-planning/scenarios/{id}/versions'
*/
getVersionsForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getVersions.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getVersions
* @see app/Http/Controllers/Api/ScenarioController.php:761
* @route '/api/strategic-planning/scenarios/{id}/versions'
*/
getVersionsForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getVersions.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getVersions.form = getVersionsForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::getImpact
* @see app/Http/Controllers/Api/ScenarioController.php:782
* @route '/api/strategic-planning/scenarios/{id}/impact'
*/
export const getImpact = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getImpact.url(args, options),
    method: 'get',
})

getImpact.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenarios/{id}/impact',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::getImpact
* @see app/Http/Controllers/Api/ScenarioController.php:782
* @route '/api/strategic-planning/scenarios/{id}/impact'
*/
getImpact.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getImpact.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::getImpact
* @see app/Http/Controllers/Api/ScenarioController.php:782
* @route '/api/strategic-planning/scenarios/{id}/impact'
*/
getImpact.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getImpact.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getImpact
* @see app/Http/Controllers/Api/ScenarioController.php:782
* @route '/api/strategic-planning/scenarios/{id}/impact'
*/
getImpact.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getImpact.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getImpact
* @see app/Http/Controllers/Api/ScenarioController.php:782
* @route '/api/strategic-planning/scenarios/{id}/impact'
*/
const getImpactForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getImpact.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getImpact
* @see app/Http/Controllers/Api/ScenarioController.php:782
* @route '/api/strategic-planning/scenarios/{id}/impact'
*/
getImpactForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getImpact.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getImpact
* @see app/Http/Controllers/Api/ScenarioController.php:782
* @route '/api/strategic-planning/scenarios/{id}/impact'
*/
getImpactForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getImpact.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getImpact.form = getImpactForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::exportFinancial
* @see app/Http/Controllers/Api/ScenarioController.php:812
* @route '/api/strategic-planning/scenarios/{id}/export-financial'
*/
export const exportFinancial = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportFinancial.url(args, options),
    method: 'get',
})

exportFinancial.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenarios/{id}/export-financial',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::exportFinancial
* @see app/Http/Controllers/Api/ScenarioController.php:812
* @route '/api/strategic-planning/scenarios/{id}/export-financial'
*/
exportFinancial.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return exportFinancial.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::exportFinancial
* @see app/Http/Controllers/Api/ScenarioController.php:812
* @route '/api/strategic-planning/scenarios/{id}/export-financial'
*/
exportFinancial.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportFinancial.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::exportFinancial
* @see app/Http/Controllers/Api/ScenarioController.php:812
* @route '/api/strategic-planning/scenarios/{id}/export-financial'
*/
exportFinancial.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportFinancial.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::exportFinancial
* @see app/Http/Controllers/Api/ScenarioController.php:812
* @route '/api/strategic-planning/scenarios/{id}/export-financial'
*/
const exportFinancialForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportFinancial.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::exportFinancial
* @see app/Http/Controllers/Api/ScenarioController.php:812
* @route '/api/strategic-planning/scenarios/{id}/export-financial'
*/
exportFinancialForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportFinancial.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::exportFinancial
* @see app/Http/Controllers/Api/ScenarioController.php:812
* @route '/api/strategic-planning/scenarios/{id}/export-financial'
*/
exportFinancialForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportFinancial.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

exportFinancial.form = exportFinancialForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::store
* @see app/Http/Controllers/Api/ScenarioController.php:272
* @route '/api/strategic-planning/scenarios'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::store
* @see app/Http/Controllers/Api/ScenarioController.php:272
* @route '/api/strategic-planning/scenarios'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::store
* @see app/Http/Controllers/Api/ScenarioController.php:272
* @route '/api/strategic-planning/scenarios'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::store
* @see app/Http/Controllers/Api/ScenarioController.php:272
* @route '/api/strategic-planning/scenarios'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::store
* @see app/Http/Controllers/Api/ScenarioController.php:272
* @route '/api/strategic-planning/scenarios'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::updateScenario
* @see app/Http/Controllers/Api/ScenarioController.php:331
* @route '/api/strategic-planning/scenarios/{id}'
*/
export const updateScenario = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateScenario.url(args, options),
    method: 'patch',
})

updateScenario.definition = {
    methods: ["patch"],
    url: '/api/strategic-planning/scenarios/{id}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::updateScenario
* @see app/Http/Controllers/Api/ScenarioController.php:331
* @route '/api/strategic-planning/scenarios/{id}'
*/
updateScenario.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return updateScenario.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::updateScenario
* @see app/Http/Controllers/Api/ScenarioController.php:331
* @route '/api/strategic-planning/scenarios/{id}'
*/
updateScenario.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateScenario.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::updateScenario
* @see app/Http/Controllers/Api/ScenarioController.php:331
* @route '/api/strategic-planning/scenarios/{id}'
*/
const updateScenarioForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateScenario.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::updateScenario
* @see app/Http/Controllers/Api/ScenarioController.php:331
* @route '/api/strategic-planning/scenarios/{id}'
*/
updateScenarioForm.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateScenario.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateScenario.form = updateScenarioForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::instantiateFromTemplate
* @see app/Http/Controllers/Api/ScenarioController.php:356
* @route '/api/strategic-planning/scenarios/{template_id}/instantiate-from-template'
*/
export const instantiateFromTemplate = (args: { template_id: string | number } | [template_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: instantiateFromTemplate.url(args, options),
    method: 'post',
})

instantiateFromTemplate.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/{template_id}/instantiate-from-template',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::instantiateFromTemplate
* @see app/Http/Controllers/Api/ScenarioController.php:356
* @route '/api/strategic-planning/scenarios/{template_id}/instantiate-from-template'
*/
instantiateFromTemplate.url = (args: { template_id: string | number } | [template_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { template_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            template_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        template_id: args.template_id,
    }

    return instantiateFromTemplate.definition.url
            .replace('{template_id}', parsedArgs.template_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::instantiateFromTemplate
* @see app/Http/Controllers/Api/ScenarioController.php:356
* @route '/api/strategic-planning/scenarios/{template_id}/instantiate-from-template'
*/
instantiateFromTemplate.post = (args: { template_id: string | number } | [template_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: instantiateFromTemplate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::instantiateFromTemplate
* @see app/Http/Controllers/Api/ScenarioController.php:356
* @route '/api/strategic-planning/scenarios/{template_id}/instantiate-from-template'
*/
const instantiateFromTemplateForm = (args: { template_id: string | number } | [template_id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: instantiateFromTemplate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::instantiateFromTemplate
* @see app/Http/Controllers/Api/ScenarioController.php:356
* @route '/api/strategic-planning/scenarios/{template_id}/instantiate-from-template'
*/
instantiateFromTemplateForm.post = (args: { template_id: string | number } | [template_id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: instantiateFromTemplate.url(args, options),
    method: 'post',
})

instantiateFromTemplate.form = instantiateFromTemplateForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::calculateGaps
* @see app/Http/Controllers/Api/ScenarioController.php:393
* @route '/api/strategic-planning/scenarios/{id}/calculate-gaps'
*/
export const calculateGaps = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: calculateGaps.url(args, options),
    method: 'post',
})

calculateGaps.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/{id}/calculate-gaps',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::calculateGaps
* @see app/Http/Controllers/Api/ScenarioController.php:393
* @route '/api/strategic-planning/scenarios/{id}/calculate-gaps'
*/
calculateGaps.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return calculateGaps.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::calculateGaps
* @see app/Http/Controllers/Api/ScenarioController.php:393
* @route '/api/strategic-planning/scenarios/{id}/calculate-gaps'
*/
calculateGaps.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: calculateGaps.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::calculateGaps
* @see app/Http/Controllers/Api/ScenarioController.php:393
* @route '/api/strategic-planning/scenarios/{id}/calculate-gaps'
*/
const calculateGapsForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: calculateGaps.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::calculateGaps
* @see app/Http/Controllers/Api/ScenarioController.php:393
* @route '/api/strategic-planning/scenarios/{id}/calculate-gaps'
*/
calculateGapsForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: calculateGaps.url(args, options),
    method: 'post',
})

calculateGaps.form = calculateGapsForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::refreshSuggestedStrategies
* @see app/Http/Controllers/Api/ScenarioController.php:409
* @route '/api/strategic-planning/scenarios/{id}/refresh-suggested-strategies'
*/
export const refreshSuggestedStrategies = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: refreshSuggestedStrategies.url(args, options),
    method: 'post',
})

refreshSuggestedStrategies.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/{id}/refresh-suggested-strategies',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::refreshSuggestedStrategies
* @see app/Http/Controllers/Api/ScenarioController.php:409
* @route '/api/strategic-planning/scenarios/{id}/refresh-suggested-strategies'
*/
refreshSuggestedStrategies.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return refreshSuggestedStrategies.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::refreshSuggestedStrategies
* @see app/Http/Controllers/Api/ScenarioController.php:409
* @route '/api/strategic-planning/scenarios/{id}/refresh-suggested-strategies'
*/
refreshSuggestedStrategies.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: refreshSuggestedStrategies.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::refreshSuggestedStrategies
* @see app/Http/Controllers/Api/ScenarioController.php:409
* @route '/api/strategic-planning/scenarios/{id}/refresh-suggested-strategies'
*/
const refreshSuggestedStrategiesForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: refreshSuggestedStrategies.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::refreshSuggestedStrategies
* @see app/Http/Controllers/Api/ScenarioController.php:409
* @route '/api/strategic-planning/scenarios/{id}/refresh-suggested-strategies'
*/
refreshSuggestedStrategiesForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: refreshSuggestedStrategies.url(args, options),
    method: 'post',
})

refreshSuggestedStrategies.form = refreshSuggestedStrategiesForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::finalizeScenario
* @see app/Http/Controllers/Api/ScenarioController.php:532
* @route '/api/strategic-planning/scenarios/{id}/finalize'
*/
export const finalizeScenario = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: finalizeScenario.url(args, options),
    method: 'post',
})

finalizeScenario.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/{id}/finalize',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::finalizeScenario
* @see app/Http/Controllers/Api/ScenarioController.php:532
* @route '/api/strategic-planning/scenarios/{id}/finalize'
*/
finalizeScenario.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return finalizeScenario.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::finalizeScenario
* @see app/Http/Controllers/Api/ScenarioController.php:532
* @route '/api/strategic-planning/scenarios/{id}/finalize'
*/
finalizeScenario.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: finalizeScenario.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::finalizeScenario
* @see app/Http/Controllers/Api/ScenarioController.php:532
* @route '/api/strategic-planning/scenarios/{id}/finalize'
*/
const finalizeScenarioForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: finalizeScenario.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::finalizeScenario
* @see app/Http/Controllers/Api/ScenarioController.php:532
* @route '/api/strategic-planning/scenarios/{id}/finalize'
*/
finalizeScenarioForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: finalizeScenario.url(args, options),
    method: 'post',
})

finalizeScenario.form = finalizeScenarioForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::compareVersions
* @see app/Http/Controllers/Api/ScenarioController.php:570
* @route '/api/strategic-planning/scenarios/{id}/compare-versions'
*/
export const compareVersions = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: compareVersions.url(args, options),
    method: 'get',
})

compareVersions.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenarios/{id}/compare-versions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::compareVersions
* @see app/Http/Controllers/Api/ScenarioController.php:570
* @route '/api/strategic-planning/scenarios/{id}/compare-versions'
*/
compareVersions.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return compareVersions.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::compareVersions
* @see app/Http/Controllers/Api/ScenarioController.php:570
* @route '/api/strategic-planning/scenarios/{id}/compare-versions'
*/
compareVersions.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: compareVersions.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::compareVersions
* @see app/Http/Controllers/Api/ScenarioController.php:570
* @route '/api/strategic-planning/scenarios/{id}/compare-versions'
*/
compareVersions.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: compareVersions.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::compareVersions
* @see app/Http/Controllers/Api/ScenarioController.php:570
* @route '/api/strategic-planning/scenarios/{id}/compare-versions'
*/
const compareVersionsForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: compareVersions.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::compareVersions
* @see app/Http/Controllers/Api/ScenarioController.php:570
* @route '/api/strategic-planning/scenarios/{id}/compare-versions'
*/
compareVersionsForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: compareVersions.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::compareVersions
* @see app/Http/Controllers/Api/ScenarioController.php:570
* @route '/api/strategic-planning/scenarios/{id}/compare-versions'
*/
compareVersionsForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: compareVersions.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

compareVersions.form = compareVersionsForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::summarize
* @see app/Http/Controllers/Api/ScenarioController.php:700
* @route '/api/strategic-planning/scenarios/{id}/summary'
*/
export const summarize = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: summarize.url(args, options),
    method: 'get',
})

summarize.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenarios/{id}/summary',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::summarize
* @see app/Http/Controllers/Api/ScenarioController.php:700
* @route '/api/strategic-planning/scenarios/{id}/summary'
*/
summarize.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return summarize.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::summarize
* @see app/Http/Controllers/Api/ScenarioController.php:700
* @route '/api/strategic-planning/scenarios/{id}/summary'
*/
summarize.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: summarize.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::summarize
* @see app/Http/Controllers/Api/ScenarioController.php:700
* @route '/api/strategic-planning/scenarios/{id}/summary'
*/
summarize.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: summarize.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::summarize
* @see app/Http/Controllers/Api/ScenarioController.php:700
* @route '/api/strategic-planning/scenarios/{id}/summary'
*/
const summarizeForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: summarize.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::summarize
* @see app/Http/Controllers/Api/ScenarioController.php:700
* @route '/api/strategic-planning/scenarios/{id}/summary'
*/
summarizeForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: summarize.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::summarize
* @see app/Http/Controllers/Api/ScenarioController.php:700
* @route '/api/strategic-planning/scenarios/{id}/summary'
*/
summarizeForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: summarize.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

summarize.form = summarizeForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::destroyScenario
* @see app/Http/Controllers/Api/ScenarioController.php:885
* @route '/api/strategic-planning/scenarios/{id}'
*/
export const destroyScenario = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyScenario.url(args, options),
    method: 'delete',
})

destroyScenario.definition = {
    methods: ["delete"],
    url: '/api/strategic-planning/scenarios/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::destroyScenario
* @see app/Http/Controllers/Api/ScenarioController.php:885
* @route '/api/strategic-planning/scenarios/{id}'
*/
destroyScenario.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return destroyScenario.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::destroyScenario
* @see app/Http/Controllers/Api/ScenarioController.php:885
* @route '/api/strategic-planning/scenarios/{id}'
*/
destroyScenario.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyScenario.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::destroyScenario
* @see app/Http/Controllers/Api/ScenarioController.php:885
* @route '/api/strategic-planning/scenarios/{id}'
*/
const destroyScenarioForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyScenario.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::destroyScenario
* @see app/Http/Controllers/Api/ScenarioController.php:885
* @route '/api/strategic-planning/scenarios/{id}'
*/
destroyScenarioForm.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyScenario.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroyScenario.form = destroyScenarioForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::designTalent
* @see app/Http/Controllers/Api/ScenarioController.php:610
* @route '/api/strategic-planning/scenarios/{id}/orchestrate'
*/
const designTalent14e3fa9c27a9681e666c0a5b14d6ff59 = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: designTalent14e3fa9c27a9681e666c0a5b14d6ff59.url(args, options),
    method: 'post',
})

designTalent14e3fa9c27a9681e666c0a5b14d6ff59.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/{id}/orchestrate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::designTalent
* @see app/Http/Controllers/Api/ScenarioController.php:610
* @route '/api/strategic-planning/scenarios/{id}/orchestrate'
*/
designTalent14e3fa9c27a9681e666c0a5b14d6ff59.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return designTalent14e3fa9c27a9681e666c0a5b14d6ff59.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::designTalent
* @see app/Http/Controllers/Api/ScenarioController.php:610
* @route '/api/strategic-planning/scenarios/{id}/orchestrate'
*/
designTalent14e3fa9c27a9681e666c0a5b14d6ff59.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: designTalent14e3fa9c27a9681e666c0a5b14d6ff59.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::designTalent
* @see app/Http/Controllers/Api/ScenarioController.php:610
* @route '/api/strategic-planning/scenarios/{id}/orchestrate'
*/
const designTalent14e3fa9c27a9681e666c0a5b14d6ff59Form = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: designTalent14e3fa9c27a9681e666c0a5b14d6ff59.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::designTalent
* @see app/Http/Controllers/Api/ScenarioController.php:610
* @route '/api/strategic-planning/scenarios/{id}/orchestrate'
*/
designTalent14e3fa9c27a9681e666c0a5b14d6ff59Form.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: designTalent14e3fa9c27a9681e666c0a5b14d6ff59.url(args, options),
    method: 'post',
})

designTalent14e3fa9c27a9681e666c0a5b14d6ff59.form = designTalent14e3fa9c27a9681e666c0a5b14d6ff59Form
/**
* @see \App\Http\Controllers\Api\ScenarioController::designTalent
* @see app/Http/Controllers/Api/ScenarioController.php:610
* @route '/api/scenarios/{scenarioId}/step2/design-talent'
*/
const designTalent5b91851da882d80f085755d6afc34213 = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: designTalent5b91851da882d80f085755d6afc34213.url(args, options),
    method: 'post',
})

designTalent5b91851da882d80f085755d6afc34213.definition = {
    methods: ["post"],
    url: '/api/scenarios/{scenarioId}/step2/design-talent',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::designTalent
* @see app/Http/Controllers/Api/ScenarioController.php:610
* @route '/api/scenarios/{scenarioId}/step2/design-talent'
*/
designTalent5b91851da882d80f085755d6afc34213.url = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return designTalent5b91851da882d80f085755d6afc34213.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::designTalent
* @see app/Http/Controllers/Api/ScenarioController.php:610
* @route '/api/scenarios/{scenarioId}/step2/design-talent'
*/
designTalent5b91851da882d80f085755d6afc34213.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: designTalent5b91851da882d80f085755d6afc34213.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::designTalent
* @see app/Http/Controllers/Api/ScenarioController.php:610
* @route '/api/scenarios/{scenarioId}/step2/design-talent'
*/
const designTalent5b91851da882d80f085755d6afc34213Form = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: designTalent5b91851da882d80f085755d6afc34213.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::designTalent
* @see app/Http/Controllers/Api/ScenarioController.php:610
* @route '/api/scenarios/{scenarioId}/step2/design-talent'
*/
designTalent5b91851da882d80f085755d6afc34213Form.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: designTalent5b91851da882d80f085755d6afc34213.url(args, options),
    method: 'post',
})

designTalent5b91851da882d80f085755d6afc34213.form = designTalent5b91851da882d80f085755d6afc34213Form

export const designTalent = {
    '/api/strategic-planning/scenarios/{id}/orchestrate': designTalent14e3fa9c27a9681e666c0a5b14d6ff59,
    '/api/scenarios/{scenarioId}/step2/design-talent': designTalent5b91851da882d80f085755d6afc34213,
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::getIncubatedTree
* @see app/Http/Controllers/Api/ScenarioController.php:107
* @route '/api/scenarios/{id}/step1/incubated-tree'
*/
export const getIncubatedTree = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getIncubatedTree.url(args, options),
    method: 'get',
})

getIncubatedTree.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{id}/step1/incubated-tree',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::getIncubatedTree
* @see app/Http/Controllers/Api/ScenarioController.php:107
* @route '/api/scenarios/{id}/step1/incubated-tree'
*/
getIncubatedTree.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getIncubatedTree.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::getIncubatedTree
* @see app/Http/Controllers/Api/ScenarioController.php:107
* @route '/api/scenarios/{id}/step1/incubated-tree'
*/
getIncubatedTree.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getIncubatedTree.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getIncubatedTree
* @see app/Http/Controllers/Api/ScenarioController.php:107
* @route '/api/scenarios/{id}/step1/incubated-tree'
*/
getIncubatedTree.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getIncubatedTree.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getIncubatedTree
* @see app/Http/Controllers/Api/ScenarioController.php:107
* @route '/api/scenarios/{id}/step1/incubated-tree'
*/
const getIncubatedTreeForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getIncubatedTree.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getIncubatedTree
* @see app/Http/Controllers/Api/ScenarioController.php:107
* @route '/api/scenarios/{id}/step1/incubated-tree'
*/
getIncubatedTreeForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getIncubatedTree.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getIncubatedTree
* @see app/Http/Controllers/Api/ScenarioController.php:107
* @route '/api/scenarios/{id}/step1/incubated-tree'
*/
getIncubatedTreeForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getIncubatedTree.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getIncubatedTree.form = getIncubatedTreeForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::promoteAll
* @see app/Http/Controllers/Api/ScenarioController.php:133
* @route '/api/scenarios/{id}/step1/promote-all'
*/
export const promoteAll = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: promoteAll.url(args, options),
    method: 'post',
})

promoteAll.definition = {
    methods: ["post"],
    url: '/api/scenarios/{id}/step1/promote-all',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::promoteAll
* @see app/Http/Controllers/Api/ScenarioController.php:133
* @route '/api/scenarios/{id}/step1/promote-all'
*/
promoteAll.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return promoteAll.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::promoteAll
* @see app/Http/Controllers/Api/ScenarioController.php:133
* @route '/api/scenarios/{id}/step1/promote-all'
*/
promoteAll.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: promoteAll.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::promoteAll
* @see app/Http/Controllers/Api/ScenarioController.php:133
* @route '/api/scenarios/{id}/step1/promote-all'
*/
const promoteAllForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: promoteAll.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::promoteAll
* @see app/Http/Controllers/Api/ScenarioController.php:133
* @route '/api/scenarios/{id}/step1/promote-all'
*/
promoteAllForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: promoteAll.url(args, options),
    method: 'post',
})

promoteAll.form = promoteAllForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::applyAgentProposals
* @see app/Http/Controllers/Api/ScenarioController.php:624
* @route '/api/scenarios/{scenarioId}/step2/agent-proposals/apply'
*/
export const applyAgentProposals = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: applyAgentProposals.url(args, options),
    method: 'post',
})

applyAgentProposals.definition = {
    methods: ["post"],
    url: '/api/scenarios/{scenarioId}/step2/agent-proposals/apply',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::applyAgentProposals
* @see app/Http/Controllers/Api/ScenarioController.php:624
* @route '/api/scenarios/{scenarioId}/step2/agent-proposals/apply'
*/
applyAgentProposals.url = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return applyAgentProposals.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::applyAgentProposals
* @see app/Http/Controllers/Api/ScenarioController.php:624
* @route '/api/scenarios/{scenarioId}/step2/agent-proposals/apply'
*/
applyAgentProposals.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: applyAgentProposals.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::applyAgentProposals
* @see app/Http/Controllers/Api/ScenarioController.php:624
* @route '/api/scenarios/{scenarioId}/step2/agent-proposals/apply'
*/
const applyAgentProposalsForm = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: applyAgentProposals.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::applyAgentProposals
* @see app/Http/Controllers/Api/ScenarioController.php:624
* @route '/api/scenarios/{scenarioId}/step2/agent-proposals/apply'
*/
applyAgentProposalsForm.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: applyAgentProposals.url(args, options),
    method: 'post',
})

applyAgentProposals.form = applyAgentProposalsForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::finalizeStep2
* @see app/Http/Controllers/Api/ScenarioController.php:679
* @route '/api/scenarios/{scenarioId}/step2/finalize'
*/
export const finalizeStep2 = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: finalizeStep2.url(args, options),
    method: 'post',
})

finalizeStep2.definition = {
    methods: ["post"],
    url: '/api/scenarios/{scenarioId}/step2/finalize',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::finalizeStep2
* @see app/Http/Controllers/Api/ScenarioController.php:679
* @route '/api/scenarios/{scenarioId}/step2/finalize'
*/
finalizeStep2.url = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return finalizeStep2.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::finalizeStep2
* @see app/Http/Controllers/Api/ScenarioController.php:679
* @route '/api/scenarios/{scenarioId}/step2/finalize'
*/
finalizeStep2.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: finalizeStep2.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::finalizeStep2
* @see app/Http/Controllers/Api/ScenarioController.php:679
* @route '/api/scenarios/{scenarioId}/step2/finalize'
*/
const finalizeStep2Form = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: finalizeStep2.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::finalizeStep2
* @see app/Http/Controllers/Api/ScenarioController.php:679
* @route '/api/scenarios/{scenarioId}/step2/finalize'
*/
finalizeStep2Form.post = (args: { scenarioId: string | number } | [scenarioId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: finalizeStep2.url(args, options),
    method: 'post',
})

finalizeStep2.form = finalizeStep2Form

/**
* @see \App\Http\Controllers\Api\ScenarioController::getIQ
* @see app/Http/Controllers/Api/ScenarioController.php:35
* @route '/scenarios/{id}/iq'
*/
export const getIQ = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getIQ.url(args, options),
    method: 'get',
})

getIQ.definition = {
    methods: ["get","head"],
    url: '/scenarios/{id}/iq',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::getIQ
* @see app/Http/Controllers/Api/ScenarioController.php:35
* @route '/scenarios/{id}/iq'
*/
getIQ.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getIQ.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::getIQ
* @see app/Http/Controllers/Api/ScenarioController.php:35
* @route '/scenarios/{id}/iq'
*/
getIQ.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getIQ.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getIQ
* @see app/Http/Controllers/Api/ScenarioController.php:35
* @route '/scenarios/{id}/iq'
*/
getIQ.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getIQ.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getIQ
* @see app/Http/Controllers/Api/ScenarioController.php:35
* @route '/scenarios/{id}/iq'
*/
const getIQForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getIQ.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getIQ
* @see app/Http/Controllers/Api/ScenarioController.php:35
* @route '/scenarios/{id}/iq'
*/
getIQForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getIQ.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getIQ
* @see app/Http/Controllers/Api/ScenarioController.php:35
* @route '/scenarios/{id}/iq'
*/
getIQForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getIQ.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getIQ.form = getIQForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::getCompetencyGaps
* @see app/Http/Controllers/Api/ScenarioController.php:0
* @route '/scenarios/{id}/roles/{roleId}/competency-gaps'
*/
export const getCompetencyGaps = (args: { id: string | number, roleId: string | number } | [id: string | number, roleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCompetencyGaps.url(args, options),
    method: 'get',
})

getCompetencyGaps.definition = {
    methods: ["get","head"],
    url: '/scenarios/{id}/roles/{roleId}/competency-gaps',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::getCompetencyGaps
* @see app/Http/Controllers/Api/ScenarioController.php:0
* @route '/scenarios/{id}/roles/{roleId}/competency-gaps'
*/
getCompetencyGaps.url = (args: { id: string | number, roleId: string | number } | [id: string | number, roleId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            id: args[0],
            roleId: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
        roleId: args.roleId,
    }

    return getCompetencyGaps.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace('{roleId}', parsedArgs.roleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::getCompetencyGaps
* @see app/Http/Controllers/Api/ScenarioController.php:0
* @route '/scenarios/{id}/roles/{roleId}/competency-gaps'
*/
getCompetencyGaps.get = (args: { id: string | number, roleId: string | number } | [id: string | number, roleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCompetencyGaps.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getCompetencyGaps
* @see app/Http/Controllers/Api/ScenarioController.php:0
* @route '/scenarios/{id}/roles/{roleId}/competency-gaps'
*/
getCompetencyGaps.head = (args: { id: string | number, roleId: string | number } | [id: string | number, roleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCompetencyGaps.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getCompetencyGaps
* @see app/Http/Controllers/Api/ScenarioController.php:0
* @route '/scenarios/{id}/roles/{roleId}/competency-gaps'
*/
const getCompetencyGapsForm = (args: { id: string | number, roleId: string | number } | [id: string | number, roleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCompetencyGaps.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getCompetencyGaps
* @see app/Http/Controllers/Api/ScenarioController.php:0
* @route '/scenarios/{id}/roles/{roleId}/competency-gaps'
*/
getCompetencyGapsForm.get = (args: { id: string | number, roleId: string | number } | [id: string | number, roleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCompetencyGaps.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getCompetencyGaps
* @see app/Http/Controllers/Api/ScenarioController.php:0
* @route '/scenarios/{id}/roles/{roleId}/competency-gaps'
*/
getCompetencyGapsForm.head = (args: { id: string | number, roleId: string | number } | [id: string | number, roleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCompetencyGaps.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getCompetencyGaps.form = getCompetencyGapsForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::deriveSkills
* @see app/Http/Controllers/Api/ScenarioController.php:46
* @route '/scenarios/{id}/roles/{roleId}/derive-skills'
*/
export const deriveSkills = (args: { id: string | number, roleId: string | number } | [id: string | number, roleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deriveSkills.url(args, options),
    method: 'post',
})

deriveSkills.definition = {
    methods: ["post"],
    url: '/scenarios/{id}/roles/{roleId}/derive-skills',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::deriveSkills
* @see app/Http/Controllers/Api/ScenarioController.php:46
* @route '/scenarios/{id}/roles/{roleId}/derive-skills'
*/
deriveSkills.url = (args: { id: string | number, roleId: string | number } | [id: string | number, roleId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            id: args[0],
            roleId: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
        roleId: args.roleId,
    }

    return deriveSkills.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace('{roleId}', parsedArgs.roleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::deriveSkills
* @see app/Http/Controllers/Api/ScenarioController.php:46
* @route '/scenarios/{id}/roles/{roleId}/derive-skills'
*/
deriveSkills.post = (args: { id: string | number, roleId: string | number } | [id: string | number, roleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deriveSkills.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::deriveSkills
* @see app/Http/Controllers/Api/ScenarioController.php:46
* @route '/scenarios/{id}/roles/{roleId}/derive-skills'
*/
const deriveSkillsForm = (args: { id: string | number, roleId: string | number } | [id: string | number, roleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deriveSkills.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::deriveSkills
* @see app/Http/Controllers/Api/ScenarioController.php:46
* @route '/scenarios/{id}/roles/{roleId}/derive-skills'
*/
deriveSkillsForm.post = (args: { id: string | number, roleId: string | number } | [id: string | number, roleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deriveSkills.url(args, options),
    method: 'post',
})

deriveSkills.form = deriveSkillsForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::deriveAllSkills
* @see app/Http/Controllers/Api/ScenarioController.php:57
* @route '/scenarios/{id}/derive-all-skills'
*/
export const deriveAllSkills = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deriveAllSkills.url(args, options),
    method: 'post',
})

deriveAllSkills.definition = {
    methods: ["post"],
    url: '/scenarios/{id}/derive-all-skills',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::deriveAllSkills
* @see app/Http/Controllers/Api/ScenarioController.php:57
* @route '/scenarios/{id}/derive-all-skills'
*/
deriveAllSkills.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return deriveAllSkills.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::deriveAllSkills
* @see app/Http/Controllers/Api/ScenarioController.php:57
* @route '/scenarios/{id}/derive-all-skills'
*/
deriveAllSkills.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deriveAllSkills.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::deriveAllSkills
* @see app/Http/Controllers/Api/ScenarioController.php:57
* @route '/scenarios/{id}/derive-all-skills'
*/
const deriveAllSkillsForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deriveAllSkills.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::deriveAllSkills
* @see app/Http/Controllers/Api/ScenarioController.php:57
* @route '/scenarios/{id}/derive-all-skills'
*/
deriveAllSkillsForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deriveAllSkills.url(args, options),
    method: 'post',
})

deriveAllSkills.form = deriveAllSkillsForm

const ScenarioController = { listScenarios, showScenario, getCapabilityTree, getVersions, getImpact, exportFinancial, store, updateScenario, instantiateFromTemplate, calculateGaps, refreshSuggestedStrategies, finalizeScenario, compareVersions, summarize, destroyScenario, designTalent, getIncubatedTree, promoteAll, applyAgentProposals, finalizeStep2, getIQ, getCompetencyGaps, deriveSkills, deriveAllSkills }

export default ScenarioController