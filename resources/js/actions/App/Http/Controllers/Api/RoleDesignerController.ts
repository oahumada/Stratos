import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\RoleDesignerController::showApprovalRequest
* @see app/Http/Controllers/Api/RoleDesignerController.php:87
* @route '/api/approvals/{token}'
*/
export const showApprovalRequest = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showApprovalRequest.url(args, options),
    method: 'get',
})

showApprovalRequest.definition = {
    methods: ["get","head"],
    url: '/api/approvals/{token}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::showApprovalRequest
* @see app/Http/Controllers/Api/RoleDesignerController.php:87
* @route '/api/approvals/{token}'
*/
showApprovalRequest.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { token: args }
    }

    if (Array.isArray(args)) {
        args = {
            token: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        token: args.token,
    }

    return showApprovalRequest.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::showApprovalRequest
* @see app/Http/Controllers/Api/RoleDesignerController.php:87
* @route '/api/approvals/{token}'
*/
showApprovalRequest.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showApprovalRequest.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::showApprovalRequest
* @see app/Http/Controllers/Api/RoleDesignerController.php:87
* @route '/api/approvals/{token}'
*/
showApprovalRequest.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showApprovalRequest.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::showApprovalRequest
* @see app/Http/Controllers/Api/RoleDesignerController.php:87
* @route '/api/approvals/{token}'
*/
const showApprovalRequestForm = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showApprovalRequest.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::showApprovalRequest
* @see app/Http/Controllers/Api/RoleDesignerController.php:87
* @route '/api/approvals/{token}'
*/
showApprovalRequestForm.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showApprovalRequest.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::showApprovalRequest
* @see app/Http/Controllers/Api/RoleDesignerController.php:87
* @route '/api/approvals/{token}'
*/
showApprovalRequestForm.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showApprovalRequest.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

showApprovalRequest.form = showApprovalRequestForm

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::approve
* @see app/Http/Controllers/Api/RoleDesignerController.php:102
* @route '/api/approvals/{token}/approve'
*/
export const approve = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

approve.definition = {
    methods: ["post"],
    url: '/api/approvals/{token}/approve',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::approve
* @see app/Http/Controllers/Api/RoleDesignerController.php:102
* @route '/api/approvals/{token}/approve'
*/
approve.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { token: args }
    }

    if (Array.isArray(args)) {
        args = {
            token: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        token: args.token,
    }

    return approve.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::approve
* @see app/Http/Controllers/Api/RoleDesignerController.php:102
* @route '/api/approvals/{token}/approve'
*/
approve.post = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::approve
* @see app/Http/Controllers/Api/RoleDesignerController.php:102
* @route '/api/approvals/{token}/approve'
*/
const approveForm = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::approve
* @see app/Http/Controllers/Api/RoleDesignerController.php:102
* @route '/api/approvals/{token}/approve'
*/
approveForm.post = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve.url(args, options),
    method: 'post',
})

approve.form = approveForm

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::analyzePreview
* @see app/Http/Controllers/Api/RoleDesignerController.php:37
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
* @see app/Http/Controllers/Api/RoleDesignerController.php:37
* @route '/api/strategic-planning/roles/analyze-preview'
*/
analyzePreview.url = (options?: RouteQueryOptions) => {
    return analyzePreview.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::analyzePreview
* @see app/Http/Controllers/Api/RoleDesignerController.php:37
* @route '/api/strategic-planning/roles/analyze-preview'
*/
analyzePreview.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyzePreview.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::analyzePreview
* @see app/Http/Controllers/Api/RoleDesignerController.php:37
* @route '/api/strategic-planning/roles/analyze-preview'
*/
const analyzePreviewForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: analyzePreview.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::analyzePreview
* @see app/Http/Controllers/Api/RoleDesignerController.php:37
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
* @see app/Http/Controllers/Api/RoleDesignerController.php:22
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
* @see app/Http/Controllers/Api/RoleDesignerController.php:22
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
* @see app/Http/Controllers/Api/RoleDesignerController.php:22
* @route '/api/strategic-planning/roles/{id}/design'
*/
design.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: design.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::design
* @see app/Http/Controllers/Api/RoleDesignerController.php:22
* @route '/api/strategic-planning/roles/{id}/design'
*/
const designForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: design.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::design
* @see app/Http/Controllers/Api/RoleDesignerController.php:22
* @route '/api/strategic-planning/roles/{id}/design'
*/
designForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: design.url(args, options),
    method: 'post',
})

design.form = designForm

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::materializeCompetencies
* @see app/Http/Controllers/Api/RoleDesignerController.php:59
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
* @see app/Http/Controllers/Api/RoleDesignerController.php:59
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
* @see app/Http/Controllers/Api/RoleDesignerController.php:59
* @route '/api/strategic-planning/roles/{id}/materialize-competencies'
*/
materializeCompetencies.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: materializeCompetencies.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::materializeCompetencies
* @see app/Http/Controllers/Api/RoleDesignerController.php:59
* @route '/api/strategic-planning/roles/{id}/materialize-competencies'
*/
const materializeCompetenciesForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: materializeCompetencies.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::materializeCompetencies
* @see app/Http/Controllers/Api/RoleDesignerController.php:59
* @route '/api/strategic-planning/roles/{id}/materialize-competencies'
*/
materializeCompetenciesForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: materializeCompetencies.url(args, options),
    method: 'post',
})

materializeCompetencies.form = materializeCompetenciesForm

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::requestApproval
* @see app/Http/Controllers/Api/RoleDesignerController.php:73
* @route '/api/strategic-planning/roles/{id}/request-approval'
*/
export const requestApproval = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: requestApproval.url(args, options),
    method: 'post',
})

requestApproval.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/roles/{id}/request-approval',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::requestApproval
* @see app/Http/Controllers/Api/RoleDesignerController.php:73
* @route '/api/strategic-planning/roles/{id}/request-approval'
*/
requestApproval.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return requestApproval.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::requestApproval
* @see app/Http/Controllers/Api/RoleDesignerController.php:73
* @route '/api/strategic-planning/roles/{id}/request-approval'
*/
requestApproval.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: requestApproval.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::requestApproval
* @see app/Http/Controllers/Api/RoleDesignerController.php:73
* @route '/api/strategic-planning/roles/{id}/request-approval'
*/
const requestApprovalForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: requestApproval.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::requestApproval
* @see app/Http/Controllers/Api/RoleDesignerController.php:73
* @route '/api/strategic-planning/roles/{id}/request-approval'
*/
requestApprovalForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: requestApproval.url(args, options),
    method: 'post',
})

requestApproval.form = requestApprovalForm

const RoleDesignerController = { showApprovalRequest, approve, analyzePreview, generateSkillBlueprint, design, materializeCompetencies, requestApproval }

export default RoleDesignerController