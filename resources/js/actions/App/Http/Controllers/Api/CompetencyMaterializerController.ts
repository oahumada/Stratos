import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\CompetencyMaterializerController::generateBlueprint
* @see app/Http/Controllers/Api/CompetencyMaterializerController.php:23
* @route '/api/strategic-planning/competencies/{id}/generate-blueprint'
*/
export const generateBlueprint = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateBlueprint.url(args, options),
    method: 'post',
})

generateBlueprint.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/competencies/{id}/generate-blueprint',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\CompetencyMaterializerController::generateBlueprint
* @see app/Http/Controllers/Api/CompetencyMaterializerController.php:23
* @route '/api/strategic-planning/competencies/{id}/generate-blueprint'
*/
generateBlueprint.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return generateBlueprint.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CompetencyMaterializerController::generateBlueprint
* @see app/Http/Controllers/Api/CompetencyMaterializerController.php:23
* @route '/api/strategic-planning/competencies/{id}/generate-blueprint'
*/
generateBlueprint.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateBlueprint.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\CompetencyMaterializerController::materialize
* @see app/Http/Controllers/Api/CompetencyMaterializerController.php:44
* @route '/api/strategic-planning/competencies/{id}/materialize'
*/
export const materialize = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: materialize.url(args, options),
    method: 'post',
})

materialize.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/competencies/{id}/materialize',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\CompetencyMaterializerController::materialize
* @see app/Http/Controllers/Api/CompetencyMaterializerController.php:44
* @route '/api/strategic-planning/competencies/{id}/materialize'
*/
materialize.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return materialize.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CompetencyMaterializerController::materialize
* @see app/Http/Controllers/Api/CompetencyMaterializerController.php:44
* @route '/api/strategic-planning/competencies/{id}/materialize'
*/
materialize.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: materialize.url(args, options),
    method: 'post',
})

const CompetencyMaterializerController = { generateBlueprint, materialize }

export default CompetencyMaterializerController