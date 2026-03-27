import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::submitForApproval
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:42
* @route '/api/scenarios/{id}/submit-approval'
*/
export const submitForApproval = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submitForApproval.url(args, options),
    method: 'post',
})

submitForApproval.definition = {
    methods: ["post"],
    url: '/api/scenarios/{id}/submit-approval',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::submitForApproval
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:42
* @route '/api/scenarios/{id}/submit-approval'
*/
submitForApproval.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return submitForApproval.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::submitForApproval
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:42
* @route '/api/scenarios/{id}/submit-approval'
*/
submitForApproval.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submitForApproval.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::approve
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:85
* @route '/api/approval-requests/{id}/approve'
*/
export const approve = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

approve.definition = {
    methods: ["post"],
    url: '/api/approval-requests/{id}/approve',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::approve
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:85
* @route '/api/approval-requests/{id}/approve'
*/
approve.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return approve.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::approve
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:85
* @route '/api/approval-requests/{id}/approve'
*/
approve.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::reject
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:127
* @route '/api/approval-requests/{id}/reject'
*/
export const reject = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject.url(args, options),
    method: 'post',
})

reject.definition = {
    methods: ["post"],
    url: '/api/approval-requests/{id}/reject',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::reject
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:127
* @route '/api/approval-requests/{id}/reject'
*/
reject.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return reject.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::reject
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:127
* @route '/api/approval-requests/{id}/reject'
*/
reject.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::getApprovalMatrix
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:171
* @route '/api/scenarios/{id}/approval-matrix'
*/
export const getApprovalMatrix = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getApprovalMatrix.url(args, options),
    method: 'get',
})

getApprovalMatrix.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{id}/approval-matrix',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::getApprovalMatrix
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:171
* @route '/api/scenarios/{id}/approval-matrix'
*/
getApprovalMatrix.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getApprovalMatrix.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::getApprovalMatrix
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:171
* @route '/api/scenarios/{id}/approval-matrix'
*/
getApprovalMatrix.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getApprovalMatrix.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::getApprovalMatrix
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:171
* @route '/api/scenarios/{id}/approval-matrix'
*/
getApprovalMatrix.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getApprovalMatrix.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::activate
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:214
* @route '/api/scenarios/{id}/activate'
*/
export const activate = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: activate.url(args, options),
    method: 'post',
})

activate.definition = {
    methods: ["post"],
    url: '/api/scenarios/{id}/activate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::activate
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:214
* @route '/api/scenarios/{id}/activate'
*/
activate.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return activate.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::activate
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:214
* @route '/api/scenarios/{id}/activate'
*/
activate.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: activate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::getExecutionPlan
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:257
* @route '/api/scenarios/{id}/execution-plan'
*/
export const getExecutionPlan = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getExecutionPlan.url(args, options),
    method: 'get',
})

getExecutionPlan.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{id}/execution-plan',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::getExecutionPlan
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:257
* @route '/api/scenarios/{id}/execution-plan'
*/
getExecutionPlan.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getExecutionPlan.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::getExecutionPlan
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:257
* @route '/api/scenarios/{id}/execution-plan'
*/
getExecutionPlan.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getExecutionPlan.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::getExecutionPlan
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:257
* @route '/api/scenarios/{id}/execution-plan'
*/
getExecutionPlan.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getExecutionPlan.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::resendNotification
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:333
* @route '/api/approval-requests/{id}/resend-notification'
*/
export const resendNotification = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resendNotification.url(args, options),
    method: 'post',
})

resendNotification.definition = {
    methods: ["post"],
    url: '/api/approval-requests/{id}/resend-notification',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::resendNotification
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:333
* @route '/api/approval-requests/{id}/resend-notification'
*/
resendNotification.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return resendNotification.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::resendNotification
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:333
* @route '/api/approval-requests/{id}/resend-notification'
*/
resendNotification.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resendNotification.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::emailPreview
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:383
* @route '/api/approval-requests/{id}/email-preview'
*/
export const emailPreview = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: emailPreview.url(args, options),
    method: 'post',
})

emailPreview.definition = {
    methods: ["post"],
    url: '/api/approval-requests/{id}/email-preview',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::emailPreview
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:383
* @route '/api/approval-requests/{id}/email-preview'
*/
emailPreview.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return emailPreview.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::emailPreview
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:383
* @route '/api/approval-requests/{id}/email-preview'
*/
emailPreview.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: emailPreview.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::approvalsSummary
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:440
* @route '/api/approvals-summary'
*/
export const approvalsSummary = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: approvalsSummary.url(options),
    method: 'get',
})

approvalsSummary.definition = {
    methods: ["get","head"],
    url: '/api/approvals-summary',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::approvalsSummary
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:440
* @route '/api/approvals-summary'
*/
approvalsSummary.url = (options?: RouteQueryOptions) => {
    return approvalsSummary.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::approvalsSummary
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:440
* @route '/api/approvals-summary'
*/
approvalsSummary.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: approvalsSummary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::approvalsSummary
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:440
* @route '/api/approvals-summary'
*/
approvalsSummary.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: approvalsSummary.url(options),
    method: 'head',
})

const ScenarioApprovalController = { submitForApproval, approve, reject, getApprovalMatrix, activate, getExecutionPlan, resendNotification, emailPreview, approvalsSummary }

export default ScenarioApprovalController