import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../wayfinder'
/**
* @see \ScenarioController::orchestrate
* @see [unknown]:0
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
* @see \ScenarioController::orchestrate
* @see [unknown]:0
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
* @see \ScenarioController::orchestrate
* @see [unknown]:0
* @route '/api/strategic-planning/scenarios/{id}/orchestrate'
*/
orchestrate.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: orchestrate.url(args, options),
    method: 'post',
})

/**
* @see \ScenarioController::orchestrate
* @see [unknown]:0
* @route '/api/strategic-planning/scenarios/{id}/orchestrate'
*/
const orchestrateForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: orchestrate.url(args, options),
    method: 'post',
})

/**
* @see \ScenarioController::orchestrate
* @see [unknown]:0
* @route '/api/strategic-planning/scenarios/{id}/orchestrate'
*/
orchestrateForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: orchestrate.url(args, options),
    method: 'post',
})

orchestrate.form = orchestrateForm

const ScenarioController = { orchestrate }

export default ScenarioController