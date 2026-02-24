import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\RoleDesignerController::design
* @see app/Http/Controllers/Api/RoleDesignerController.php:21
* @route '/api/strategic-planning/roles/{id}/design'
*/
export const design = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: design.url(args, options),
    method: 'post',
})

design.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/roles/{id}/design',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::design
* @see app/Http/Controllers/Api/RoleDesignerController.php:21
* @route '/api/strategic-planning/roles/{id}/design'
*/
design.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return design.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::design
* @see app/Http/Controllers/Api/RoleDesignerController.php:21
* @route '/api/strategic-planning/roles/{id}/design'
*/
design.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: design.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::design
* @see app/Http/Controllers/Api/RoleDesignerController.php:21
* @route '/api/strategic-planning/roles/{id}/design'
*/
const designForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: design.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::design
* @see app/Http/Controllers/Api/RoleDesignerController.php:21
* @route '/api/strategic-planning/roles/{id}/design'
*/
designForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: design.url(args, options),
    method: 'post',
})

design.form = designForm

const RoleDesignerController = { design }

export default RoleDesignerController