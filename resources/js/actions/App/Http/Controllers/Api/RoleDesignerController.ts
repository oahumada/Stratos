import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\RoleDesignerController::analyzePreview
* @see app/Http/Controllers/Api/RoleDesignerController.php:36
* @route '/api/strategic-planning/roles/analyze-preview'
*/
export const analyzePreview = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyzePreview.url(options),
    method: 'post',
})

analyzePreview.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/roles/analyze-preview',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::analyzePreview
* @see app/Http/Controllers/Api/RoleDesignerController.php:36
* @route '/api/strategic-planning/roles/analyze-preview'
*/
analyzePreview.url = (options?: RouteQueryOptions) => {
    return analyzePreview.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::analyzePreview
* @see app/Http/Controllers/Api/RoleDesignerController.php:36
* @route '/api/strategic-planning/roles/analyze-preview'
*/
analyzePreview.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyzePreview.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::analyzePreview
* @see app/Http/Controllers/Api/RoleDesignerController.php:36
* @route '/api/strategic-planning/roles/analyze-preview'
*/
const analyzePreviewForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: analyzePreview.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::analyzePreview
* @see app/Http/Controllers/Api/RoleDesignerController.php:36
* @route '/api/strategic-planning/roles/analyze-preview'
*/
analyzePreviewForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: analyzePreview.url(options),
    method: 'post',
})

analyzePreview.form = analyzePreviewForm

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

const RoleDesignerController = { analyzePreview, design }

export default RoleDesignerController