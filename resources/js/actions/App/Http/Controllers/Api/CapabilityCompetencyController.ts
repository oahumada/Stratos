import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\CapabilityCompetencyController::store
* @see app/Http/Controllers/Api/CapabilityCompetencyController.php:28
* @route '/api/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies'
*/
export const store = (args: { scenarioId: string | number, capabilityId: string | number } | [scenarioId: string | number, capabilityId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\CapabilityCompetencyController::store
* @see app/Http/Controllers/Api/CapabilityCompetencyController.php:28
* @route '/api/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies'
*/
store.url = (args: { scenarioId: string | number, capabilityId: string | number } | [scenarioId: string | number, capabilityId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            scenarioId: args[0],
            capabilityId: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenarioId: args.scenarioId,
        capabilityId: args.capabilityId,
    }

    return store.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace('{capabilityId}', parsedArgs.capabilityId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CapabilityCompetencyController::store
* @see app/Http/Controllers/Api/CapabilityCompetencyController.php:28
* @route '/api/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies'
*/
store.post = (args: { scenarioId: string | number, capabilityId: string | number } | [scenarioId: string | number, capabilityId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\CapabilityCompetencyController::store
* @see app/Http/Controllers/Api/CapabilityCompetencyController.php:28
* @route '/api/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies'
*/
const storeForm = (args: { scenarioId: string | number, capabilityId: string | number } | [scenarioId: string | number, capabilityId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\CapabilityCompetencyController::store
* @see app/Http/Controllers/Api/CapabilityCompetencyController.php:28
* @route '/api/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies'
*/
storeForm.post = (args: { scenarioId: string | number, capabilityId: string | number } | [scenarioId: string | number, capabilityId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\CapabilityCompetencyController::update
* @see app/Http/Controllers/Api/CapabilityCompetencyController.php:124
* @route '/api/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies/{competencyId}'
*/
export const update = (args: { scenarioId: string | number, capabilityId: string | number, competencyId: string | number } | [scenarioId: string | number, capabilityId: string | number, competencyId: string | number ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/api/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies/{competencyId}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\CapabilityCompetencyController::update
* @see app/Http/Controllers/Api/CapabilityCompetencyController.php:124
* @route '/api/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies/{competencyId}'
*/
update.url = (args: { scenarioId: string | number, capabilityId: string | number, competencyId: string | number } | [scenarioId: string | number, capabilityId: string | number, competencyId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            scenarioId: args[0],
            capabilityId: args[1],
            competencyId: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenarioId: args.scenarioId,
        capabilityId: args.capabilityId,
        competencyId: args.competencyId,
    }

    return update.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace('{capabilityId}', parsedArgs.capabilityId.toString())
            .replace('{competencyId}', parsedArgs.competencyId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CapabilityCompetencyController::update
* @see app/Http/Controllers/Api/CapabilityCompetencyController.php:124
* @route '/api/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies/{competencyId}'
*/
update.patch = (args: { scenarioId: string | number, capabilityId: string | number, competencyId: string | number } | [scenarioId: string | number, capabilityId: string | number, competencyId: string | number ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\CapabilityCompetencyController::update
* @see app/Http/Controllers/Api/CapabilityCompetencyController.php:124
* @route '/api/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies/{competencyId}'
*/
const updateForm = (args: { scenarioId: string | number, capabilityId: string | number, competencyId: string | number } | [scenarioId: string | number, capabilityId: string | number, competencyId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\CapabilityCompetencyController::update
* @see app/Http/Controllers/Api/CapabilityCompetencyController.php:124
* @route '/api/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies/{competencyId}'
*/
updateForm.patch = (args: { scenarioId: string | number, capabilityId: string | number, competencyId: string | number } | [scenarioId: string | number, capabilityId: string | number, competencyId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\Api\CapabilityCompetencyController::destroy
* @see app/Http/Controllers/Api/CapabilityCompetencyController.php:176
* @route '/api/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies/{competencyId}'
*/
export const destroy = (args: { scenarioId: string | number, capabilityId: string | number, competencyId: string | number } | [scenarioId: string | number, capabilityId: string | number, competencyId: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies/{competencyId}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\CapabilityCompetencyController::destroy
* @see app/Http/Controllers/Api/CapabilityCompetencyController.php:176
* @route '/api/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies/{competencyId}'
*/
destroy.url = (args: { scenarioId: string | number, capabilityId: string | number, competencyId: string | number } | [scenarioId: string | number, capabilityId: string | number, competencyId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            scenarioId: args[0],
            capabilityId: args[1],
            competencyId: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenarioId: args.scenarioId,
        capabilityId: args.capabilityId,
        competencyId: args.competencyId,
    }

    return destroy.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace('{capabilityId}', parsedArgs.capabilityId.toString())
            .replace('{competencyId}', parsedArgs.competencyId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CapabilityCompetencyController::destroy
* @see app/Http/Controllers/Api/CapabilityCompetencyController.php:176
* @route '/api/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies/{competencyId}'
*/
destroy.delete = (args: { scenarioId: string | number, capabilityId: string | number, competencyId: string | number } | [scenarioId: string | number, capabilityId: string | number, competencyId: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\CapabilityCompetencyController::destroy
* @see app/Http/Controllers/Api/CapabilityCompetencyController.php:176
* @route '/api/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies/{competencyId}'
*/
const destroyForm = (args: { scenarioId: string | number, capabilityId: string | number, competencyId: string | number } | [scenarioId: string | number, capabilityId: string | number, competencyId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\CapabilityCompetencyController::destroy
* @see app/Http/Controllers/Api/CapabilityCompetencyController.php:176
* @route '/api/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies/{competencyId}'
*/
destroyForm.delete = (args: { scenarioId: string | number, capabilityId: string | number, competencyId: string | number } | [scenarioId: string | number, capabilityId: string | number, competencyId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const CapabilityCompetencyController = { store, update, destroy }

export default CapabilityCompetencyController