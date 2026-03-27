import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::submitForApproval
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:35
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
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:35
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
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:35
* @route '/api/scenarios/{id}/submit-approval'
*/
submitForApproval.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submitForApproval.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::submitForApproval
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:35
* @route '/api/scenarios/{id}/submit-approval'
*/
const submitForApprovalForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: submitForApproval.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::submitForApproval
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:35
* @route '/api/scenarios/{id}/submit-approval'
*/
submitForApprovalForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: submitForApproval.url(args, options),
    method: 'post',
})

submitForApproval.form = submitForApprovalForm

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::approve
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:68
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
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:68
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
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:68
* @route '/api/approval-requests/{id}/approve'
*/
approve.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::approve
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:68
* @route '/api/approval-requests/{id}/approve'
*/
const approveForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::approve
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:68
* @route '/api/approval-requests/{id}/approve'
*/
approveForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve.url(args, options),
    method: 'post',
})

approve.form = approveForm

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::reject
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:106
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
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:106
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
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:106
* @route '/api/approval-requests/{id}/reject'
*/
reject.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::reject
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:106
* @route '/api/approval-requests/{id}/reject'
*/
const rejectForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reject.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::reject
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:106
* @route '/api/approval-requests/{id}/reject'
*/
rejectForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reject.url(args, options),
    method: 'post',
})

reject.form = rejectForm

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::getApprovalMatrix
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:143
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
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:143
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
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:143
* @route '/api/scenarios/{id}/approval-matrix'
*/
getApprovalMatrix.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getApprovalMatrix.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::getApprovalMatrix
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:143
* @route '/api/scenarios/{id}/approval-matrix'
*/
getApprovalMatrix.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getApprovalMatrix.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::getApprovalMatrix
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:143
* @route '/api/scenarios/{id}/approval-matrix'
*/
const getApprovalMatrixForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getApprovalMatrix.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::getApprovalMatrix
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:143
* @route '/api/scenarios/{id}/approval-matrix'
*/
getApprovalMatrixForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getApprovalMatrix.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::getApprovalMatrix
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:143
* @route '/api/scenarios/{id}/approval-matrix'
*/
getApprovalMatrixForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getApprovalMatrix.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getApprovalMatrix.form = getApprovalMatrixForm

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::activate
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:186
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
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:186
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
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:186
* @route '/api/scenarios/{id}/activate'
*/
activate.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: activate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::activate
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:186
* @route '/api/scenarios/{id}/activate'
*/
const activateForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: activate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::activate
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:186
* @route '/api/scenarios/{id}/activate'
*/
activateForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: activate.url(args, options),
    method: 'post',
})

activate.form = activateForm

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::getExecutionPlan
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:209
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
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:209
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
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:209
* @route '/api/scenarios/{id}/execution-plan'
*/
getExecutionPlan.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getExecutionPlan.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::getExecutionPlan
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:209
* @route '/api/scenarios/{id}/execution-plan'
*/
getExecutionPlan.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getExecutionPlan.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::getExecutionPlan
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:209
* @route '/api/scenarios/{id}/execution-plan'
*/
const getExecutionPlanForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getExecutionPlan.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::getExecutionPlan
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:209
* @route '/api/scenarios/{id}/execution-plan'
*/
getExecutionPlanForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getExecutionPlan.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioApprovalController::getExecutionPlan
* @see app/Http/Controllers/Api/ScenarioApprovalController.php:209
* @route '/api/scenarios/{id}/execution-plan'
*/
getExecutionPlanForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getExecutionPlan.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getExecutionPlan.form = getExecutionPlanForm

const ScenarioApprovalController = { submitForApproval, approve, reject, getApprovalMatrix, activate, getExecutionPlan }

export default ScenarioApprovalController