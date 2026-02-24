import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ScenarioController::listScenarios
* @see app/Http/Controllers/Api/ScenarioController.php:113
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
* @see app/Http/Controllers/Api/ScenarioController.php:113
* @route '/api/strategic-planning/scenarios'
*/
listScenarios.url = (options?: RouteQueryOptions) => {
    return listScenarios.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::listScenarios
* @see app/Http/Controllers/Api/ScenarioController.php:113
* @route '/api/strategic-planning/scenarios'
*/
listScenarios.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listScenarios.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::listScenarios
* @see app/Http/Controllers/Api/ScenarioController.php:113
* @route '/api/strategic-planning/scenarios'
*/
listScenarios.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: listScenarios.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::listScenarios
* @see app/Http/Controllers/Api/ScenarioController.php:113
* @route '/api/strategic-planning/scenarios'
*/
const listScenariosForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listScenarios.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::listScenarios
* @see app/Http/Controllers/Api/ScenarioController.php:113
* @route '/api/strategic-planning/scenarios'
*/
listScenariosForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listScenarios.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::listScenarios
* @see app/Http/Controllers/Api/ScenarioController.php:113
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
* @see app/Http/Controllers/Api/ScenarioController.php:277
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
* @see app/Http/Controllers/Api/ScenarioController.php:277
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
* @see app/Http/Controllers/Api/ScenarioController.php:277
* @route '/api/strategic-planning/scenarios/{id}'
*/
showScenario.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showScenario.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::showScenario
* @see app/Http/Controllers/Api/ScenarioController.php:277
* @route '/api/strategic-planning/scenarios/{id}'
*/
showScenario.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showScenario.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::showScenario
* @see app/Http/Controllers/Api/ScenarioController.php:277
* @route '/api/strategic-planning/scenarios/{id}'
*/
const showScenarioForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showScenario.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::showScenario
* @see app/Http/Controllers/Api/ScenarioController.php:277
* @route '/api/strategic-planning/scenarios/{id}'
*/
showScenarioForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showScenario.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::showScenario
* @see app/Http/Controllers/Api/ScenarioController.php:277
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
* @see app/Http/Controllers/Api/ScenarioController.php:143
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
* @see app/Http/Controllers/Api/ScenarioController.php:143
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
* @see app/Http/Controllers/Api/ScenarioController.php:143
* @route '/api/strategic-planning/scenarios/{id}/capability-tree'
*/
getCapabilityTree.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCapabilityTree.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getCapabilityTree
* @see app/Http/Controllers/Api/ScenarioController.php:143
* @route '/api/strategic-planning/scenarios/{id}/capability-tree'
*/
getCapabilityTree.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCapabilityTree.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getCapabilityTree
* @see app/Http/Controllers/Api/ScenarioController.php:143
* @route '/api/strategic-planning/scenarios/{id}/capability-tree'
*/
const getCapabilityTreeForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCapabilityTree.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getCapabilityTree
* @see app/Http/Controllers/Api/ScenarioController.php:143
* @route '/api/strategic-planning/scenarios/{id}/capability-tree'
*/
getCapabilityTreeForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCapabilityTree.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getCapabilityTree
* @see app/Http/Controllers/Api/ScenarioController.php:143
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
* @see \App\Http\Controllers\Api\ScenarioController::getImpact
* @see app/Http/Controllers/Api/ScenarioController.php:761
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
* @see app/Http/Controllers/Api/ScenarioController.php:761
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
* @see app/Http/Controllers/Api/ScenarioController.php:761
* @route '/api/strategic-planning/scenarios/{id}/impact'
*/
getImpact.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getImpact.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getImpact
* @see app/Http/Controllers/Api/ScenarioController.php:761
* @route '/api/strategic-planning/scenarios/{id}/impact'
*/
getImpact.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getImpact.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getImpact
* @see app/Http/Controllers/Api/ScenarioController.php:761
* @route '/api/strategic-planning/scenarios/{id}/impact'
*/
const getImpactForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getImpact.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getImpact
* @see app/Http/Controllers/Api/ScenarioController.php:761
* @route '/api/strategic-planning/scenarios/{id}/impact'
*/
getImpactForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getImpact.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getImpact
* @see app/Http/Controllers/Api/ScenarioController.php:761
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
* @see app/Http/Controllers/Api/ScenarioController.php:789
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
* @see app/Http/Controllers/Api/ScenarioController.php:789
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
* @see app/Http/Controllers/Api/ScenarioController.php:789
* @route '/api/strategic-planning/scenarios/{id}/export-financial'
*/
exportFinancial.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportFinancial.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::exportFinancial
* @see app/Http/Controllers/Api/ScenarioController.php:789
* @route '/api/strategic-planning/scenarios/{id}/export-financial'
*/
exportFinancial.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportFinancial.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::exportFinancial
* @see app/Http/Controllers/Api/ScenarioController.php:789
* @route '/api/strategic-planning/scenarios/{id}/export-financial'
*/
const exportFinancialForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportFinancial.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::exportFinancial
* @see app/Http/Controllers/Api/ScenarioController.php:789
* @route '/api/strategic-planning/scenarios/{id}/export-financial'
*/
exportFinancialForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportFinancial.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::exportFinancial
* @see app/Http/Controllers/Api/ScenarioController.php:789
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
* @see app/Http/Controllers/Api/ScenarioController.php:325
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
* @see app/Http/Controllers/Api/ScenarioController.php:325
* @route '/api/strategic-planning/scenarios'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::store
* @see app/Http/Controllers/Api/ScenarioController.php:325
* @route '/api/strategic-planning/scenarios'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::store
* @see app/Http/Controllers/Api/ScenarioController.php:325
* @route '/api/strategic-planning/scenarios'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::store
* @see app/Http/Controllers/Api/ScenarioController.php:325
* @route '/api/strategic-planning/scenarios'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::updateScenario
* @see app/Http/Controllers/Api/ScenarioController.php:375
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
* @see app/Http/Controllers/Api/ScenarioController.php:375
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
* @see app/Http/Controllers/Api/ScenarioController.php:375
* @route '/api/strategic-planning/scenarios/{id}'
*/
updateScenario.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateScenario.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::updateScenario
* @see app/Http/Controllers/Api/ScenarioController.php:375
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
* @see app/Http/Controllers/Api/ScenarioController.php:375
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
* @see app/Http/Controllers/Api/ScenarioController.php:400
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
* @see app/Http/Controllers/Api/ScenarioController.php:400
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
* @see app/Http/Controllers/Api/ScenarioController.php:400
* @route '/api/strategic-planning/scenarios/{template_id}/instantiate-from-template'
*/
instantiateFromTemplate.post = (args: { template_id: string | number } | [template_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: instantiateFromTemplate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::instantiateFromTemplate
* @see app/Http/Controllers/Api/ScenarioController.php:400
* @route '/api/strategic-planning/scenarios/{template_id}/instantiate-from-template'
*/
const instantiateFromTemplateForm = (args: { template_id: string | number } | [template_id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: instantiateFromTemplate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::instantiateFromTemplate
* @see app/Http/Controllers/Api/ScenarioController.php:400
* @route '/api/strategic-planning/scenarios/{template_id}/instantiate-from-template'
*/
instantiateFromTemplateForm.post = (args: { template_id: string | number } | [template_id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: instantiateFromTemplate.url(args, options),
    method: 'post',
})

instantiateFromTemplate.form = instantiateFromTemplateForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::calculateGaps
* @see app/Http/Controllers/Api/ScenarioController.php:437
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
* @see app/Http/Controllers/Api/ScenarioController.php:437
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
* @see app/Http/Controllers/Api/ScenarioController.php:437
* @route '/api/strategic-planning/scenarios/{id}/calculate-gaps'
*/
calculateGaps.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: calculateGaps.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::calculateGaps
* @see app/Http/Controllers/Api/ScenarioController.php:437
* @route '/api/strategic-planning/scenarios/{id}/calculate-gaps'
*/
const calculateGapsForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: calculateGaps.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::calculateGaps
* @see app/Http/Controllers/Api/ScenarioController.php:437
* @route '/api/strategic-planning/scenarios/{id}/calculate-gaps'
*/
calculateGapsForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: calculateGaps.url(args, options),
    method: 'post',
})

calculateGaps.form = calculateGapsForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::refreshSuggestedStrategies
* @see app/Http/Controllers/Api/ScenarioController.php:453
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
* @see app/Http/Controllers/Api/ScenarioController.php:453
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
* @see app/Http/Controllers/Api/ScenarioController.php:453
* @route '/api/strategic-planning/scenarios/{id}/refresh-suggested-strategies'
*/
refreshSuggestedStrategies.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: refreshSuggestedStrategies.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::refreshSuggestedStrategies
* @see app/Http/Controllers/Api/ScenarioController.php:453
* @route '/api/strategic-planning/scenarios/{id}/refresh-suggested-strategies'
*/
const refreshSuggestedStrategiesForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: refreshSuggestedStrategies.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::refreshSuggestedStrategies
* @see app/Http/Controllers/Api/ScenarioController.php:453
* @route '/api/strategic-planning/scenarios/{id}/refresh-suggested-strategies'
*/
refreshSuggestedStrategiesForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: refreshSuggestedStrategies.url(args, options),
    method: 'post',
})

refreshSuggestedStrategies.form = refreshSuggestedStrategiesForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::finalizeScenario
* @see app/Http/Controllers/Api/ScenarioController.php:572
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
* @see app/Http/Controllers/Api/ScenarioController.php:572
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
* @see app/Http/Controllers/Api/ScenarioController.php:572
* @route '/api/strategic-planning/scenarios/{id}/finalize'
*/
finalizeScenario.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: finalizeScenario.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::finalizeScenario
* @see app/Http/Controllers/Api/ScenarioController.php:572
* @route '/api/strategic-planning/scenarios/{id}/finalize'
*/
const finalizeScenarioForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: finalizeScenario.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::finalizeScenario
* @see app/Http/Controllers/Api/ScenarioController.php:572
* @route '/api/strategic-planning/scenarios/{id}/finalize'
*/
finalizeScenarioForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: finalizeScenario.url(args, options),
    method: 'post',
})

finalizeScenario.form = finalizeScenarioForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::compareVersions
* @see app/Http/Controllers/Api/ScenarioController.php:610
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
* @see app/Http/Controllers/Api/ScenarioController.php:610
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
* @see app/Http/Controllers/Api/ScenarioController.php:610
* @route '/api/strategic-planning/scenarios/{id}/compare-versions'
*/
compareVersions.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: compareVersions.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::compareVersions
* @see app/Http/Controllers/Api/ScenarioController.php:610
* @route '/api/strategic-planning/scenarios/{id}/compare-versions'
*/
compareVersions.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: compareVersions.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::compareVersions
* @see app/Http/Controllers/Api/ScenarioController.php:610
* @route '/api/strategic-planning/scenarios/{id}/compare-versions'
*/
const compareVersionsForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: compareVersions.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::compareVersions
* @see app/Http/Controllers/Api/ScenarioController.php:610
* @route '/api/strategic-planning/scenarios/{id}/compare-versions'
*/
compareVersionsForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: compareVersions.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::compareVersions
* @see app/Http/Controllers/Api/ScenarioController.php:610
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
* @see app/Http/Controllers/Api/ScenarioController.php:673
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
* @see app/Http/Controllers/Api/ScenarioController.php:673
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
* @see app/Http/Controllers/Api/ScenarioController.php:673
* @route '/api/strategic-planning/scenarios/{id}/summary'
*/
summarize.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: summarize.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::summarize
* @see app/Http/Controllers/Api/ScenarioController.php:673
* @route '/api/strategic-planning/scenarios/{id}/summary'
*/
summarize.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: summarize.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::summarize
* @see app/Http/Controllers/Api/ScenarioController.php:673
* @route '/api/strategic-planning/scenarios/{id}/summary'
*/
const summarizeForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: summarize.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::summarize
* @see app/Http/Controllers/Api/ScenarioController.php:673
* @route '/api/strategic-planning/scenarios/{id}/summary'
*/
summarizeForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: summarize.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::summarize
* @see app/Http/Controllers/Api/ScenarioController.php:673
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
* @see \App\Http\Controllers\Api\ScenarioController::orchestrate
* @see app/Http/Controllers/Api/ScenarioController.php:657
* @route '/api/strategic-planning/scenarios/{id}/orchestrate'
*/
export const orchestrate = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: orchestrate.url(args, options),
    method: 'post',
})

orchestrate.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/{id}/orchestrate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioController::orchestrate
* @see app/Http/Controllers/Api/ScenarioController.php:657
* @route '/api/strategic-planning/scenarios/{id}/orchestrate'
*/
orchestrate.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return orchestrate.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioController::orchestrate
* @see app/Http/Controllers/Api/ScenarioController.php:657
* @route '/api/strategic-planning/scenarios/{id}/orchestrate'
*/
orchestrate.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: orchestrate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::orchestrate
* @see app/Http/Controllers/Api/ScenarioController.php:657
* @route '/api/strategic-planning/scenarios/{id}/orchestrate'
*/
const orchestrateForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: orchestrate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::orchestrate
* @see app/Http/Controllers/Api/ScenarioController.php:657
* @route '/api/strategic-planning/scenarios/{id}/orchestrate'
*/
orchestrateForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: orchestrate.url(args, options),
    method: 'post',
})

orchestrate.form = orchestrateForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::getIQ
* @see app/Http/Controllers/Api/ScenarioController.php:36
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
* @see app/Http/Controllers/Api/ScenarioController.php:36
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
* @see app/Http/Controllers/Api/ScenarioController.php:36
* @route '/scenarios/{id}/iq'
*/
getIQ.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getIQ.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getIQ
* @see app/Http/Controllers/Api/ScenarioController.php:36
* @route '/scenarios/{id}/iq'
*/
getIQ.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getIQ.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getIQ
* @see app/Http/Controllers/Api/ScenarioController.php:36
* @route '/scenarios/{id}/iq'
*/
const getIQForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getIQ.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getIQ
* @see app/Http/Controllers/Api/ScenarioController.php:36
* @route '/scenarios/{id}/iq'
*/
getIQForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getIQ.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::getIQ
* @see app/Http/Controllers/Api/ScenarioController.php:36
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
* @see app/Http/Controllers/Api/ScenarioController.php:86
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
* @see app/Http/Controllers/Api/ScenarioController.php:86
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
* @see app/Http/Controllers/Api/ScenarioController.php:86
* @route '/scenarios/{id}/roles/{roleId}/derive-skills'
*/
deriveSkills.post = (args: { id: string | number, roleId: string | number } | [id: string | number, roleId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deriveSkills.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::deriveSkills
* @see app/Http/Controllers/Api/ScenarioController.php:86
* @route '/scenarios/{id}/roles/{roleId}/derive-skills'
*/
const deriveSkillsForm = (args: { id: string | number, roleId: string | number } | [id: string | number, roleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deriveSkills.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::deriveSkills
* @see app/Http/Controllers/Api/ScenarioController.php:86
* @route '/scenarios/{id}/roles/{roleId}/derive-skills'
*/
deriveSkillsForm.post = (args: { id: string | number, roleId: string | number } | [id: string | number, roleId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deriveSkills.url(args, options),
    method: 'post',
})

deriveSkills.form = deriveSkillsForm

/**
* @see \App\Http\Controllers\Api\ScenarioController::deriveAllSkills
* @see app/Http/Controllers/Api/ScenarioController.php:100
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
* @see app/Http/Controllers/Api/ScenarioController.php:100
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
* @see app/Http/Controllers/Api/ScenarioController.php:100
* @route '/scenarios/{id}/derive-all-skills'
*/
deriveAllSkills.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deriveAllSkills.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::deriveAllSkills
* @see app/Http/Controllers/Api/ScenarioController.php:100
* @route '/scenarios/{id}/derive-all-skills'
*/
const deriveAllSkillsForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deriveAllSkills.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioController::deriveAllSkills
* @see app/Http/Controllers/Api/ScenarioController.php:100
* @route '/scenarios/{id}/derive-all-skills'
*/
deriveAllSkillsForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deriveAllSkills.url(args, options),
    method: 'post',
})

deriveAllSkills.form = deriveAllSkillsForm

const ScenarioController = { listScenarios, showScenario, getCapabilityTree, getImpact, exportFinancial, store, updateScenario, instantiateFromTemplate, calculateGaps, refreshSuggestedStrategies, finalizeScenario, compareVersions, summarize, orchestrate, getIQ, getCompetencyGaps, deriveSkills, deriveAllSkills }

export default ScenarioController