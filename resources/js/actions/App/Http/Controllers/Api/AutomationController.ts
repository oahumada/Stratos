import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\AutomationController::evaluate
* @see app/Http/Controllers/Api/AutomationController.php:41
* @route '/api/automation/evaluate'
*/
export const evaluate = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: evaluate.url(options),
    method: 'get',
})

evaluate.definition = {
    methods: ["get","head"],
    url: '/api/automation/evaluate',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AutomationController::evaluate
* @see app/Http/Controllers/Api/AutomationController.php:41
* @route '/api/automation/evaluate'
*/
evaluate.url = (options?: RouteQueryOptions) => {
    return evaluate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AutomationController::evaluate
* @see app/Http/Controllers/Api/AutomationController.php:41
* @route '/api/automation/evaluate'
*/
evaluate.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: evaluate.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::evaluate
* @see app/Http/Controllers/Api/AutomationController.php:41
* @route '/api/automation/evaluate'
*/
evaluate.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: evaluate.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::evaluate
* @see app/Http/Controllers/Api/AutomationController.php:41
* @route '/api/automation/evaluate'
*/
const evaluateForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: evaluate.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::evaluate
* @see app/Http/Controllers/Api/AutomationController.php:41
* @route '/api/automation/evaluate'
*/
evaluateForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: evaluate.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::evaluate
* @see app/Http/Controllers/Api/AutomationController.php:41
* @route '/api/automation/evaluate'
*/
evaluateForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: evaluate.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

evaluate.form = evaluateForm

/**
* @see \App\Http\Controllers\Api\AutomationController::triggerWorkflow
* @see app/Http/Controllers/Api/AutomationController.php:65
* @route '/api/automation/workflows/{code}/trigger'
*/
export const triggerWorkflow = (args: { code: string | number } | [code: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: triggerWorkflow.url(args, options),
    method: 'post',
})

triggerWorkflow.definition = {
    methods: ["post"],
    url: '/api/automation/workflows/{code}/trigger',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AutomationController::triggerWorkflow
* @see app/Http/Controllers/Api/AutomationController.php:65
* @route '/api/automation/workflows/{code}/trigger'
*/
triggerWorkflow.url = (args: { code: string | number } | [code: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { code: args }
    }

    if (Array.isArray(args)) {
        args = {
            code: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        code: args.code,
    }

    return triggerWorkflow.definition.url
            .replace('{code}', parsedArgs.code.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AutomationController::triggerWorkflow
* @see app/Http/Controllers/Api/AutomationController.php:65
* @route '/api/automation/workflows/{code}/trigger'
*/
triggerWorkflow.post = (args: { code: string | number } | [code: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: triggerWorkflow.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::triggerWorkflow
* @see app/Http/Controllers/Api/AutomationController.php:65
* @route '/api/automation/workflows/{code}/trigger'
*/
const triggerWorkflowForm = (args: { code: string | number } | [code: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: triggerWorkflow.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::triggerWorkflow
* @see app/Http/Controllers/Api/AutomationController.php:65
* @route '/api/automation/workflows/{code}/trigger'
*/
triggerWorkflowForm.post = (args: { code: string | number } | [code: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: triggerWorkflow.url(args, options),
    method: 'post',
})

triggerWorkflow.form = triggerWorkflowForm

/**
* @see \App\Http\Controllers\Api\AutomationController::listAvailableWorkflows
* @see app/Http/Controllers/Api/AutomationController.php:88
* @route '/api/automation/workflows/available'
*/
export const listAvailableWorkflows = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listAvailableWorkflows.url(options),
    method: 'get',
})

listAvailableWorkflows.definition = {
    methods: ["get","head"],
    url: '/api/automation/workflows/available',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AutomationController::listAvailableWorkflows
* @see app/Http/Controllers/Api/AutomationController.php:88
* @route '/api/automation/workflows/available'
*/
listAvailableWorkflows.url = (options?: RouteQueryOptions) => {
    return listAvailableWorkflows.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AutomationController::listAvailableWorkflows
* @see app/Http/Controllers/Api/AutomationController.php:88
* @route '/api/automation/workflows/available'
*/
listAvailableWorkflows.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listAvailableWorkflows.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::listAvailableWorkflows
* @see app/Http/Controllers/Api/AutomationController.php:88
* @route '/api/automation/workflows/available'
*/
listAvailableWorkflows.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: listAvailableWorkflows.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::listAvailableWorkflows
* @see app/Http/Controllers/Api/AutomationController.php:88
* @route '/api/automation/workflows/available'
*/
const listAvailableWorkflowsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listAvailableWorkflows.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::listAvailableWorkflows
* @see app/Http/Controllers/Api/AutomationController.php:88
* @route '/api/automation/workflows/available'
*/
listAvailableWorkflowsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listAvailableWorkflows.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::listAvailableWorkflows
* @see app/Http/Controllers/Api/AutomationController.php:88
* @route '/api/automation/workflows/available'
*/
listAvailableWorkflowsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listAvailableWorkflows.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

listAvailableWorkflows.form = listAvailableWorkflowsForm

/**
* @see \App\Http\Controllers\Api\AutomationController::getExecutionStatus
* @see app/Http/Controllers/Api/AutomationController.php:103
* @route '/api/automation/executions/{executionId}'
*/
export const getExecutionStatus = (args: { executionId: string | number } | [executionId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getExecutionStatus.url(args, options),
    method: 'get',
})

getExecutionStatus.definition = {
    methods: ["get","head"],
    url: '/api/automation/executions/{executionId}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AutomationController::getExecutionStatus
* @see app/Http/Controllers/Api/AutomationController.php:103
* @route '/api/automation/executions/{executionId}'
*/
getExecutionStatus.url = (args: { executionId: string | number } | [executionId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { executionId: args }
    }

    if (Array.isArray(args)) {
        args = {
            executionId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        executionId: args.executionId,
    }

    return getExecutionStatus.definition.url
            .replace('{executionId}', parsedArgs.executionId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AutomationController::getExecutionStatus
* @see app/Http/Controllers/Api/AutomationController.php:103
* @route '/api/automation/executions/{executionId}'
*/
getExecutionStatus.get = (args: { executionId: string | number } | [executionId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getExecutionStatus.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::getExecutionStatus
* @see app/Http/Controllers/Api/AutomationController.php:103
* @route '/api/automation/executions/{executionId}'
*/
getExecutionStatus.head = (args: { executionId: string | number } | [executionId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getExecutionStatus.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::getExecutionStatus
* @see app/Http/Controllers/Api/AutomationController.php:103
* @route '/api/automation/executions/{executionId}'
*/
const getExecutionStatusForm = (args: { executionId: string | number } | [executionId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getExecutionStatus.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::getExecutionStatus
* @see app/Http/Controllers/Api/AutomationController.php:103
* @route '/api/automation/executions/{executionId}'
*/
getExecutionStatusForm.get = (args: { executionId: string | number } | [executionId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getExecutionStatus.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::getExecutionStatus
* @see app/Http/Controllers/Api/AutomationController.php:103
* @route '/api/automation/executions/{executionId}'
*/
getExecutionStatusForm.head = (args: { executionId: string | number } | [executionId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getExecutionStatus.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getExecutionStatus.form = getExecutionStatusForm

/**
* @see \App\Http\Controllers\Api\AutomationController::cancelExecution
* @see app/Http/Controllers/Api/AutomationController.php:114
* @route '/api/automation/executions/{executionId}'
*/
export const cancelExecution = (args: { executionId: string | number } | [executionId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: cancelExecution.url(args, options),
    method: 'delete',
})

cancelExecution.definition = {
    methods: ["delete"],
    url: '/api/automation/executions/{executionId}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\AutomationController::cancelExecution
* @see app/Http/Controllers/Api/AutomationController.php:114
* @route '/api/automation/executions/{executionId}'
*/
cancelExecution.url = (args: { executionId: string | number } | [executionId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { executionId: args }
    }

    if (Array.isArray(args)) {
        args = {
            executionId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        executionId: args.executionId,
    }

    return cancelExecution.definition.url
            .replace('{executionId}', parsedArgs.executionId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AutomationController::cancelExecution
* @see app/Http/Controllers/Api/AutomationController.php:114
* @route '/api/automation/executions/{executionId}'
*/
cancelExecution.delete = (args: { executionId: string | number } | [executionId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: cancelExecution.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::cancelExecution
* @see app/Http/Controllers/Api/AutomationController.php:114
* @route '/api/automation/executions/{executionId}'
*/
const cancelExecutionForm = (args: { executionId: string | number } | [executionId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancelExecution.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::cancelExecution
* @see app/Http/Controllers/Api/AutomationController.php:114
* @route '/api/automation/executions/{executionId}'
*/
cancelExecutionForm.delete = (args: { executionId: string | number } | [executionId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancelExecution.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

cancelExecution.form = cancelExecutionForm

/**
* @see \App\Http\Controllers\Api\AutomationController::retryExecution
* @see app/Http/Controllers/Api/AutomationController.php:125
* @route '/api/automation/executions/{executionId}/retry'
*/
export const retryExecution = (args: { executionId: string | number } | [executionId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: retryExecution.url(args, options),
    method: 'post',
})

retryExecution.definition = {
    methods: ["post"],
    url: '/api/automation/executions/{executionId}/retry',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AutomationController::retryExecution
* @see app/Http/Controllers/Api/AutomationController.php:125
* @route '/api/automation/executions/{executionId}/retry'
*/
retryExecution.url = (args: { executionId: string | number } | [executionId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { executionId: args }
    }

    if (Array.isArray(args)) {
        args = {
            executionId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        executionId: args.executionId,
    }

    return retryExecution.definition.url
            .replace('{executionId}', parsedArgs.executionId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AutomationController::retryExecution
* @see app/Http/Controllers/Api/AutomationController.php:125
* @route '/api/automation/executions/{executionId}/retry'
*/
retryExecution.post = (args: { executionId: string | number } | [executionId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: retryExecution.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::retryExecution
* @see app/Http/Controllers/Api/AutomationController.php:125
* @route '/api/automation/executions/{executionId}/retry'
*/
const retryExecutionForm = (args: { executionId: string | number } | [executionId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: retryExecution.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::retryExecution
* @see app/Http/Controllers/Api/AutomationController.php:125
* @route '/api/automation/executions/{executionId}/retry'
*/
retryExecutionForm.post = (args: { executionId: string | number } | [executionId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: retryExecution.url(args, options),
    method: 'post',
})

retryExecution.form = retryExecutionForm

/**
* @see \App\Http\Controllers\Api\AutomationController::listWebhooks
* @see app/Http/Controllers/Api/AutomationController.php:143
* @route '/api/automation/webhooks'
*/
export const listWebhooks = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listWebhooks.url(options),
    method: 'get',
})

listWebhooks.definition = {
    methods: ["get","head"],
    url: '/api/automation/webhooks',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AutomationController::listWebhooks
* @see app/Http/Controllers/Api/AutomationController.php:143
* @route '/api/automation/webhooks'
*/
listWebhooks.url = (options?: RouteQueryOptions) => {
    return listWebhooks.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AutomationController::listWebhooks
* @see app/Http/Controllers/Api/AutomationController.php:143
* @route '/api/automation/webhooks'
*/
listWebhooks.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listWebhooks.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::listWebhooks
* @see app/Http/Controllers/Api/AutomationController.php:143
* @route '/api/automation/webhooks'
*/
listWebhooks.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: listWebhooks.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::listWebhooks
* @see app/Http/Controllers/Api/AutomationController.php:143
* @route '/api/automation/webhooks'
*/
const listWebhooksForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listWebhooks.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::listWebhooks
* @see app/Http/Controllers/Api/AutomationController.php:143
* @route '/api/automation/webhooks'
*/
listWebhooksForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listWebhooks.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::listWebhooks
* @see app/Http/Controllers/Api/AutomationController.php:143
* @route '/api/automation/webhooks'
*/
listWebhooksForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listWebhooks.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

listWebhooks.form = listWebhooksForm

/**
* @see \App\Http\Controllers\Api\AutomationController::registerWebhook
* @see app/Http/Controllers/Api/AutomationController.php:163
* @route '/api/automation/webhooks'
*/
export const registerWebhook = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: registerWebhook.url(options),
    method: 'post',
})

registerWebhook.definition = {
    methods: ["post"],
    url: '/api/automation/webhooks',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AutomationController::registerWebhook
* @see app/Http/Controllers/Api/AutomationController.php:163
* @route '/api/automation/webhooks'
*/
registerWebhook.url = (options?: RouteQueryOptions) => {
    return registerWebhook.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AutomationController::registerWebhook
* @see app/Http/Controllers/Api/AutomationController.php:163
* @route '/api/automation/webhooks'
*/
registerWebhook.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: registerWebhook.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::registerWebhook
* @see app/Http/Controllers/Api/AutomationController.php:163
* @route '/api/automation/webhooks'
*/
const registerWebhookForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: registerWebhook.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::registerWebhook
* @see app/Http/Controllers/Api/AutomationController.php:163
* @route '/api/automation/webhooks'
*/
registerWebhookForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: registerWebhook.url(options),
    method: 'post',
})

registerWebhook.form = registerWebhookForm

/**
* @see \App\Http\Controllers\Api\AutomationController::updateWebhook
* @see app/Http/Controllers/Api/AutomationController.php:198
* @route '/api/automation/webhooks/{webhookId}'
*/
export const updateWebhook = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateWebhook.url(args, options),
    method: 'patch',
})

updateWebhook.definition = {
    methods: ["patch"],
    url: '/api/automation/webhooks/{webhookId}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\AutomationController::updateWebhook
* @see app/Http/Controllers/Api/AutomationController.php:198
* @route '/api/automation/webhooks/{webhookId}'
*/
updateWebhook.url = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { webhookId: args }
    }

    if (Array.isArray(args)) {
        args = {
            webhookId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        webhookId: args.webhookId,
    }

    return updateWebhook.definition.url
            .replace('{webhookId}', parsedArgs.webhookId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AutomationController::updateWebhook
* @see app/Http/Controllers/Api/AutomationController.php:198
* @route '/api/automation/webhooks/{webhookId}'
*/
updateWebhook.patch = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateWebhook.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::updateWebhook
* @see app/Http/Controllers/Api/AutomationController.php:198
* @route '/api/automation/webhooks/{webhookId}'
*/
const updateWebhookForm = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateWebhook.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::updateWebhook
* @see app/Http/Controllers/Api/AutomationController.php:198
* @route '/api/automation/webhooks/{webhookId}'
*/
updateWebhookForm.patch = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateWebhook.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateWebhook.form = updateWebhookForm

/**
* @see \App\Http\Controllers\Api\AutomationController::deleteWebhook
* @see app/Http/Controllers/Api/AutomationController.php:227
* @route '/api/automation/webhooks/{webhookId}'
*/
export const deleteWebhook = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteWebhook.url(args, options),
    method: 'delete',
})

deleteWebhook.definition = {
    methods: ["delete"],
    url: '/api/automation/webhooks/{webhookId}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\AutomationController::deleteWebhook
* @see app/Http/Controllers/Api/AutomationController.php:227
* @route '/api/automation/webhooks/{webhookId}'
*/
deleteWebhook.url = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { webhookId: args }
    }

    if (Array.isArray(args)) {
        args = {
            webhookId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        webhookId: args.webhookId,
    }

    return deleteWebhook.definition.url
            .replace('{webhookId}', parsedArgs.webhookId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AutomationController::deleteWebhook
* @see app/Http/Controllers/Api/AutomationController.php:227
* @route '/api/automation/webhooks/{webhookId}'
*/
deleteWebhook.delete = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteWebhook.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::deleteWebhook
* @see app/Http/Controllers/Api/AutomationController.php:227
* @route '/api/automation/webhooks/{webhookId}'
*/
const deleteWebhookForm = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deleteWebhook.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::deleteWebhook
* @see app/Http/Controllers/Api/AutomationController.php:227
* @route '/api/automation/webhooks/{webhookId}'
*/
deleteWebhookForm.delete = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deleteWebhook.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

deleteWebhook.form = deleteWebhookForm

/**
* @see \App\Http\Controllers\Api\AutomationController::testWebhook
* @see app/Http/Controllers/Api/AutomationController.php:243
* @route '/api/automation/webhooks/{webhookId}/test'
*/
export const testWebhook = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: testWebhook.url(args, options),
    method: 'post',
})

testWebhook.definition = {
    methods: ["post"],
    url: '/api/automation/webhooks/{webhookId}/test',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AutomationController::testWebhook
* @see app/Http/Controllers/Api/AutomationController.php:243
* @route '/api/automation/webhooks/{webhookId}/test'
*/
testWebhook.url = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { webhookId: args }
    }

    if (Array.isArray(args)) {
        args = {
            webhookId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        webhookId: args.webhookId,
    }

    return testWebhook.definition.url
            .replace('{webhookId}', parsedArgs.webhookId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AutomationController::testWebhook
* @see app/Http/Controllers/Api/AutomationController.php:243
* @route '/api/automation/webhooks/{webhookId}/test'
*/
testWebhook.post = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: testWebhook.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::testWebhook
* @see app/Http/Controllers/Api/AutomationController.php:243
* @route '/api/automation/webhooks/{webhookId}/test'
*/
const testWebhookForm = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: testWebhook.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::testWebhook
* @see app/Http/Controllers/Api/AutomationController.php:243
* @route '/api/automation/webhooks/{webhookId}/test'
*/
testWebhookForm.post = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: testWebhook.url(args, options),
    method: 'post',
})

testWebhook.form = testWebhookForm

/**
* @see \App\Http\Controllers\Api\AutomationController::getWebhookStats
* @see app/Http/Controllers/Api/AutomationController.php:263
* @route '/api/automation/webhooks/{webhookId}/stats'
*/
export const getWebhookStats = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getWebhookStats.url(args, options),
    method: 'get',
})

getWebhookStats.definition = {
    methods: ["get","head"],
    url: '/api/automation/webhooks/{webhookId}/stats',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AutomationController::getWebhookStats
* @see app/Http/Controllers/Api/AutomationController.php:263
* @route '/api/automation/webhooks/{webhookId}/stats'
*/
getWebhookStats.url = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { webhookId: args }
    }

    if (Array.isArray(args)) {
        args = {
            webhookId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        webhookId: args.webhookId,
    }

    return getWebhookStats.definition.url
            .replace('{webhookId}', parsedArgs.webhookId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AutomationController::getWebhookStats
* @see app/Http/Controllers/Api/AutomationController.php:263
* @route '/api/automation/webhooks/{webhookId}/stats'
*/
getWebhookStats.get = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getWebhookStats.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::getWebhookStats
* @see app/Http/Controllers/Api/AutomationController.php:263
* @route '/api/automation/webhooks/{webhookId}/stats'
*/
getWebhookStats.head = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getWebhookStats.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::getWebhookStats
* @see app/Http/Controllers/Api/AutomationController.php:263
* @route '/api/automation/webhooks/{webhookId}/stats'
*/
const getWebhookStatsForm = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getWebhookStats.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::getWebhookStats
* @see app/Http/Controllers/Api/AutomationController.php:263
* @route '/api/automation/webhooks/{webhookId}/stats'
*/
getWebhookStatsForm.get = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getWebhookStats.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::getWebhookStats
* @see app/Http/Controllers/Api/AutomationController.php:263
* @route '/api/automation/webhooks/{webhookId}/stats'
*/
getWebhookStatsForm.head = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getWebhookStats.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getWebhookStats.form = getWebhookStatsForm

/**
* @see \App\Http\Controllers\Api\AutomationController::remediateAnomaly
* @see app/Http/Controllers/Api/AutomationController.php:279
* @route '/api/automation/remediate'
*/
export const remediateAnomaly = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: remediateAnomaly.url(options),
    method: 'post',
})

remediateAnomaly.definition = {
    methods: ["post"],
    url: '/api/automation/remediate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AutomationController::remediateAnomaly
* @see app/Http/Controllers/Api/AutomationController.php:279
* @route '/api/automation/remediate'
*/
remediateAnomaly.url = (options?: RouteQueryOptions) => {
    return remediateAnomaly.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AutomationController::remediateAnomaly
* @see app/Http/Controllers/Api/AutomationController.php:279
* @route '/api/automation/remediate'
*/
remediateAnomaly.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: remediateAnomaly.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::remediateAnomaly
* @see app/Http/Controllers/Api/AutomationController.php:279
* @route '/api/automation/remediate'
*/
const remediateAnomalyForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: remediateAnomaly.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::remediateAnomaly
* @see app/Http/Controllers/Api/AutomationController.php:279
* @route '/api/automation/remediate'
*/
remediateAnomalyForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: remediateAnomaly.url(options),
    method: 'post',
})

remediateAnomaly.form = remediateAnomalyForm

/**
* @see \App\Http\Controllers\Api\AutomationController::getRemediationHistory
* @see app/Http/Controllers/Api/AutomationController.php:303
* @route '/api/automation/remediation-history'
*/
export const getRemediationHistory = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getRemediationHistory.url(options),
    method: 'get',
})

getRemediationHistory.definition = {
    methods: ["get","head"],
    url: '/api/automation/remediation-history',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AutomationController::getRemediationHistory
* @see app/Http/Controllers/Api/AutomationController.php:303
* @route '/api/automation/remediation-history'
*/
getRemediationHistory.url = (options?: RouteQueryOptions) => {
    return getRemediationHistory.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AutomationController::getRemediationHistory
* @see app/Http/Controllers/Api/AutomationController.php:303
* @route '/api/automation/remediation-history'
*/
getRemediationHistory.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getRemediationHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::getRemediationHistory
* @see app/Http/Controllers/Api/AutomationController.php:303
* @route '/api/automation/remediation-history'
*/
getRemediationHistory.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getRemediationHistory.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::getRemediationHistory
* @see app/Http/Controllers/Api/AutomationController.php:303
* @route '/api/automation/remediation-history'
*/
const getRemediationHistoryForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getRemediationHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::getRemediationHistory
* @see app/Http/Controllers/Api/AutomationController.php:303
* @route '/api/automation/remediation-history'
*/
getRemediationHistoryForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getRemediationHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::getRemediationHistory
* @see app/Http/Controllers/Api/AutomationController.php:303
* @route '/api/automation/remediation-history'
*/
getRemediationHistoryForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getRemediationHistory.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getRemediationHistory.form = getRemediationHistoryForm

/**
* @see \App\Http\Controllers\Api\AutomationController::getAutomationStatus
* @see app/Http/Controllers/Api/AutomationController.php:320
* @route '/api/automation/status'
*/
export const getAutomationStatus = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getAutomationStatus.url(options),
    method: 'get',
})

getAutomationStatus.definition = {
    methods: ["get","head"],
    url: '/api/automation/status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AutomationController::getAutomationStatus
* @see app/Http/Controllers/Api/AutomationController.php:320
* @route '/api/automation/status'
*/
getAutomationStatus.url = (options?: RouteQueryOptions) => {
    return getAutomationStatus.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AutomationController::getAutomationStatus
* @see app/Http/Controllers/Api/AutomationController.php:320
* @route '/api/automation/status'
*/
getAutomationStatus.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getAutomationStatus.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::getAutomationStatus
* @see app/Http/Controllers/Api/AutomationController.php:320
* @route '/api/automation/status'
*/
getAutomationStatus.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getAutomationStatus.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::getAutomationStatus
* @see app/Http/Controllers/Api/AutomationController.php:320
* @route '/api/automation/status'
*/
const getAutomationStatusForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getAutomationStatus.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::getAutomationStatus
* @see app/Http/Controllers/Api/AutomationController.php:320
* @route '/api/automation/status'
*/
getAutomationStatusForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getAutomationStatus.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::getAutomationStatus
* @see app/Http/Controllers/Api/AutomationController.php:320
* @route '/api/automation/status'
*/
getAutomationStatusForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getAutomationStatus.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getAutomationStatus.form = getAutomationStatusForm

/**
* @see \App\Http\Controllers\Api\AutomationController::toggleAutomationStatus
* @see app/Http/Controllers/Api/AutomationController.php:338
* @route '/api/automation/status'
*/
export const toggleAutomationStatus = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggleAutomationStatus.url(options),
    method: 'post',
})

toggleAutomationStatus.definition = {
    methods: ["post"],
    url: '/api/automation/status',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AutomationController::toggleAutomationStatus
* @see app/Http/Controllers/Api/AutomationController.php:338
* @route '/api/automation/status'
*/
toggleAutomationStatus.url = (options?: RouteQueryOptions) => {
    return toggleAutomationStatus.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AutomationController::toggleAutomationStatus
* @see app/Http/Controllers/Api/AutomationController.php:338
* @route '/api/automation/status'
*/
toggleAutomationStatus.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggleAutomationStatus.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::toggleAutomationStatus
* @see app/Http/Controllers/Api/AutomationController.php:338
* @route '/api/automation/status'
*/
const toggleAutomationStatusForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggleAutomationStatus.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::toggleAutomationStatus
* @see app/Http/Controllers/Api/AutomationController.php:338
* @route '/api/automation/status'
*/
toggleAutomationStatusForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggleAutomationStatus.url(options),
    method: 'post',
})

toggleAutomationStatus.form = toggleAutomationStatusForm

const AutomationController = { evaluate, triggerWorkflow, listAvailableWorkflows, getExecutionStatus, cancelExecution, retryExecution, listWebhooks, registerWebhook, updateWebhook, deleteWebhook, testWebhook, getWebhookStats, remediateAnomaly, getRemediationHistory, getAutomationStatus, toggleAutomationStatus }

export default AutomationController