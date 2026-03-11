import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\TransformCompetencyController::transform
* @see app/Http/Controllers/TransformCompetencyController.php:12
* @route '/api/competencies/{competencyId}/transform'
*/
export const transform = (args: { competencyId: string | number } | [competencyId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: transform.url(args, options),
    method: 'post',
})

transform.definition = {
    methods: ["post"],
    url: '/api/competencies/{competencyId}/transform',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TransformCompetencyController::transform
* @see app/Http/Controllers/TransformCompetencyController.php:12
* @route '/api/competencies/{competencyId}/transform'
*/
transform.url = (args: { competencyId: string | number } | [competencyId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { competencyId: args }
    }

    if (Array.isArray(args)) {
        args = {
            competencyId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competencyId: args.competencyId,
    }

    return transform.definition.url
            .replace('{competencyId}', parsedArgs.competencyId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransformCompetencyController::transform
* @see app/Http/Controllers/TransformCompetencyController.php:12
* @route '/api/competencies/{competencyId}/transform'
*/
transform.post = (args: { competencyId: string | number } | [competencyId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: transform.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TransformCompetencyController::transform
* @see app/Http/Controllers/TransformCompetencyController.php:12
* @route '/api/competencies/{competencyId}/transform'
*/
const transformForm = (args: { competencyId: string | number } | [competencyId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: transform.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TransformCompetencyController::transform
* @see app/Http/Controllers/TransformCompetencyController.php:12
* @route '/api/competencies/{competencyId}/transform'
*/
transformForm.post = (args: { competencyId: string | number } | [competencyId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: transform.url(args, options),
    method: 'post',
})

transform.form = transformForm

const TransformCompetencyController = { transform }

export default TransformCompetencyController