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
* @see \App\Http\Controllers\Api\RoleDesignerController::generateSkillBlueprint
* @see app/Http/Controllers/Api/RoleDesignerController.php:0
* @route '/api/strategic-planning/roles/generate-skill-blueprint'
*/
export const generateSkillBlueprint = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateSkillBlueprint.url(options),
    method: 'post',
})

generateSkillBlueprint.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/roles/generate-skill-blueprint',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::generateSkillBlueprint
* @see app/Http/Controllers/Api/RoleDesignerController.php:0
* @route '/api/strategic-planning/roles/generate-skill-blueprint'
*/
generateSkillBlueprint.url = (options?: RouteQueryOptions) => {
    return generateSkillBlueprint.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::generateSkillBlueprint
* @see app/Http/Controllers/Api/RoleDesignerController.php:0
* @route '/api/strategic-planning/roles/generate-skill-blueprint'
*/
generateSkillBlueprint.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateSkillBlueprint.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::generateSkillBlueprint
* @see app/Http/Controllers/Api/RoleDesignerController.php:0
* @route '/api/strategic-planning/roles/generate-skill-blueprint'
*/
const generateSkillBlueprintForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generateSkillBlueprint.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::generateSkillBlueprint
* @see app/Http/Controllers/Api/RoleDesignerController.php:0
* @route '/api/strategic-planning/roles/generate-skill-blueprint'
*/
generateSkillBlueprintForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generateSkillBlueprint.url(options),
    method: 'post',
})

generateSkillBlueprint.form = generateSkillBlueprintForm

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

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::materializeCompetencies
* @see app/Http/Controllers/Api/RoleDesignerController.php:58
* @route '/api/strategic-planning/roles/{id}/materialize-competencies'
*/
export const materializeCompetencies = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: materializeCompetencies.url(args, options),
    method: 'post',
})

materializeCompetencies.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/roles/{id}/materialize-competencies',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::materializeCompetencies
* @see app/Http/Controllers/Api/RoleDesignerController.php:58
* @route '/api/strategic-planning/roles/{id}/materialize-competencies'
*/
materializeCompetencies.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return materializeCompetencies.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::materializeCompetencies
* @see app/Http/Controllers/Api/RoleDesignerController.php:58
* @route '/api/strategic-planning/roles/{id}/materialize-competencies'
*/
materializeCompetencies.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: materializeCompetencies.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::materializeCompetencies
* @see app/Http/Controllers/Api/RoleDesignerController.php:58
* @route '/api/strategic-planning/roles/{id}/materialize-competencies'
*/
const materializeCompetenciesForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: materializeCompetencies.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::materializeCompetencies
* @see app/Http/Controllers/Api/RoleDesignerController.php:58
* @route '/api/strategic-planning/roles/{id}/materialize-competencies'
*/
materializeCompetenciesForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: materializeCompetencies.url(args, options),
    method: 'post',
})

materializeCompetencies.form = materializeCompetenciesForm

const RoleDesignerController = { analyzePreview, generateSkillBlueprint, design, materializeCompetencies }

export default RoleDesignerController