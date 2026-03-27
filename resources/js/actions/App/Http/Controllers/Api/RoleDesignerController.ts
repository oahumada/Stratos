import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\RoleDesignerController::getApprovalDetails
* @see app/Http/Controllers/Api/RoleDesignerController.php:105
* @route '/api/approvals/{token}'
*/
export const getApprovalDetails = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getApprovalDetails.url(args, options),
    method: 'get',
})

getApprovalDetails.definition = {
    methods: ["get","head"],
    url: '/api/approvals/{token}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::getApprovalDetails
* @see app/Http/Controllers/Api/RoleDesignerController.php:105
* @route '/api/approvals/{token}'
*/
getApprovalDetails.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getApprovalDetails.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::getApprovalDetails
* @see app/Http/Controllers/Api/RoleDesignerController.php:105
* @route '/api/approvals/{token}'
*/
getApprovalDetails.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getApprovalDetails.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::getApprovalDetails
* @see app/Http/Controllers/Api/RoleDesignerController.php:105
* @route '/api/approvals/{token}'
*/
getApprovalDetails.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getApprovalDetails.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::approve
* @see app/Http/Controllers/Api/RoleDesignerController.php:143
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
* @see app/Http/Controllers/Api/RoleDesignerController.php:143
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
* @see app/Http/Controllers/Api/RoleDesignerController.php:143
* @route '/api/approvals/{token}/approve'
*/
approve.post = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::analyzePreview
* @see app/Http/Controllers/Api/RoleDesignerController.php:38
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
* @see app/Http/Controllers/Api/RoleDesignerController.php:38
* @route '/api/strategic-planning/roles/analyze-preview'
*/
analyzePreview.url = (options?: RouteQueryOptions) => {
    return analyzePreview.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::analyzePreview
* @see app/Http/Controllers/Api/RoleDesignerController.php:38
* @route '/api/strategic-planning/roles/analyze-preview'
*/
analyzePreview.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyzePreview.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::generateSkillBlueprint
* @see app/Http/Controllers/Api/RoleDesignerController.php:172
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
* @see app/Http/Controllers/Api/RoleDesignerController.php:172
* @route '/api/strategic-planning/roles/generate-skill-blueprint'
*/
generateSkillBlueprint.url = (options?: RouteQueryOptions) => {
    return generateSkillBlueprint.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::generateSkillBlueprint
* @see app/Http/Controllers/Api/RoleDesignerController.php:172
* @route '/api/strategic-planning/roles/generate-skill-blueprint'
*/
generateSkillBlueprint.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateSkillBlueprint.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::design
* @see app/Http/Controllers/Api/RoleDesignerController.php:23
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
* @see app/Http/Controllers/Api/RoleDesignerController.php:23
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
* @see app/Http/Controllers/Api/RoleDesignerController.php:23
* @route '/api/strategic-planning/roles/{id}/design'
*/
design.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: design.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::materializeCompetencies
* @see app/Http/Controllers/Api/RoleDesignerController.php:60
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
* @see app/Http/Controllers/Api/RoleDesignerController.php:60
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
* @see app/Http/Controllers/Api/RoleDesignerController.php:60
* @route '/api/strategic-planning/roles/{id}/materialize-competencies'
*/
materializeCompetencies.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: materializeCompetencies.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::requestApproval
* @see app/Http/Controllers/Api/RoleDesignerController.php:74
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
* @see app/Http/Controllers/Api/RoleDesignerController.php:74
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
* @see app/Http/Controllers/Api/RoleDesignerController.php:74
* @route '/api/strategic-planning/roles/{id}/request-approval'
*/
requestApproval.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: requestApproval.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::requestCompetencyApproval
* @see app/Http/Controllers/Api/RoleDesignerController.php:129
* @route '/api/strategic-planning/competencies/{id}/request-approval'
*/
export const requestCompetencyApproval = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: requestCompetencyApproval.url(args, options),
    method: 'post',
})

requestCompetencyApproval.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/competencies/{id}/request-approval',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::requestCompetencyApproval
* @see app/Http/Controllers/Api/RoleDesignerController.php:129
* @route '/api/strategic-planning/competencies/{id}/request-approval'
*/
requestCompetencyApproval.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return requestCompetencyApproval.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::requestCompetencyApproval
* @see app/Http/Controllers/Api/RoleDesignerController.php:129
* @route '/api/strategic-planning/competencies/{id}/request-approval'
*/
requestCompetencyApproval.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: requestCompetencyApproval.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::showApprovalRequest
* @see app/Http/Controllers/Api/RoleDesignerController.php:88
* @route '/approve/role/{token}'
*/
const showApprovalRequest254d24a1d75ba575129d55878c80dc74 = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showApprovalRequest254d24a1d75ba575129d55878c80dc74.url(args, options),
    method: 'get',
})

showApprovalRequest254d24a1d75ba575129d55878c80dc74.definition = {
    methods: ["get","head"],
    url: '/approve/role/{token}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::showApprovalRequest
* @see app/Http/Controllers/Api/RoleDesignerController.php:88
* @route '/approve/role/{token}'
*/
showApprovalRequest254d24a1d75ba575129d55878c80dc74.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return showApprovalRequest254d24a1d75ba575129d55878c80dc74.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::showApprovalRequest
* @see app/Http/Controllers/Api/RoleDesignerController.php:88
* @route '/approve/role/{token}'
*/
showApprovalRequest254d24a1d75ba575129d55878c80dc74.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showApprovalRequest254d24a1d75ba575129d55878c80dc74.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::showApprovalRequest
* @see app/Http/Controllers/Api/RoleDesignerController.php:88
* @route '/approve/role/{token}'
*/
showApprovalRequest254d24a1d75ba575129d55878c80dc74.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showApprovalRequest254d24a1d75ba575129d55878c80dc74.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::showApprovalRequest
* @see app/Http/Controllers/Api/RoleDesignerController.php:88
* @route '/approve/competency/{token}'
*/
const showApprovalRequestaf6ddcd1ab18a4bf817880b5d807cdf4 = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showApprovalRequestaf6ddcd1ab18a4bf817880b5d807cdf4.url(args, options),
    method: 'get',
})

showApprovalRequestaf6ddcd1ab18a4bf817880b5d807cdf4.definition = {
    methods: ["get","head"],
    url: '/approve/competency/{token}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::showApprovalRequest
* @see app/Http/Controllers/Api/RoleDesignerController.php:88
* @route '/approve/competency/{token}'
*/
showApprovalRequestaf6ddcd1ab18a4bf817880b5d807cdf4.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return showApprovalRequestaf6ddcd1ab18a4bf817880b5d807cdf4.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::showApprovalRequest
* @see app/Http/Controllers/Api/RoleDesignerController.php:88
* @route '/approve/competency/{token}'
*/
showApprovalRequestaf6ddcd1ab18a4bf817880b5d807cdf4.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showApprovalRequestaf6ddcd1ab18a4bf817880b5d807cdf4.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::showApprovalRequest
* @see app/Http/Controllers/Api/RoleDesignerController.php:88
* @route '/approve/competency/{token}'
*/
showApprovalRequestaf6ddcd1ab18a4bf817880b5d807cdf4.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showApprovalRequestaf6ddcd1ab18a4bf817880b5d807cdf4.url(args, options),
    method: 'head',
})

export const showApprovalRequest = {
    '/approve/role/{token}': showApprovalRequest254d24a1d75ba575129d55878c80dc74,
    '/approve/competency/{token}': showApprovalRequestaf6ddcd1ab18a4bf817880b5d807cdf4,
}

const RoleDesignerController = { getApprovalDetails, approve, analyzePreview, generateSkillBlueprint, design, materializeCompetencies, requestApproval, requestCompetencyApproval, showApprovalRequest }

export default RoleDesignerController