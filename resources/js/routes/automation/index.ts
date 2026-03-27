import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
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
* @see \App\Http\Controllers\Api\AutomationController::listWorkflows
* @see app/Http/Controllers/Api/AutomationController.php:88
* @route '/api/automation/workflows/available'
*/
export const listWorkflows = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listWorkflows.url(options),
    method: 'get',
})

listWorkflows.definition = {
    methods: ["get","head"],
    url: '/api/automation/workflows/available',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AutomationController::listWorkflows
* @see app/Http/Controllers/Api/AutomationController.php:88
* @route '/api/automation/workflows/available'
*/
listWorkflows.url = (options?: RouteQueryOptions) => {
    return listWorkflows.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AutomationController::listWorkflows
* @see app/Http/Controllers/Api/AutomationController.php:88
* @route '/api/automation/workflows/available'
*/
listWorkflows.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listWorkflows.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::listWorkflows
* @see app/Http/Controllers/Api/AutomationController.php:88
* @route '/api/automation/workflows/available'
*/
listWorkflows.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: listWorkflows.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::executionStatus
* @see app/Http/Controllers/Api/AutomationController.php:103
* @route '/api/automation/executions/{executionId}'
*/
export const executionStatus = (args: { executionId: string | number } | [executionId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: executionStatus.url(args, options),
    method: 'get',
})

executionStatus.definition = {
    methods: ["get","head"],
    url: '/api/automation/executions/{executionId}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AutomationController::executionStatus
* @see app/Http/Controllers/Api/AutomationController.php:103
* @route '/api/automation/executions/{executionId}'
*/
executionStatus.url = (args: { executionId: string | number } | [executionId: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return executionStatus.definition.url
            .replace('{executionId}', parsedArgs.executionId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AutomationController::executionStatus
* @see app/Http/Controllers/Api/AutomationController.php:103
* @route '/api/automation/executions/{executionId}'
*/
executionStatus.get = (args: { executionId: string | number } | [executionId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: executionStatus.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::executionStatus
* @see app/Http/Controllers/Api/AutomationController.php:103
* @route '/api/automation/executions/{executionId}'
*/
executionStatus.head = (args: { executionId: string | number } | [executionId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: executionStatus.url(args, options),
    method: 'head',
})

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
* @see \App\Http\Controllers\Api\AutomationController::webhookStats
* @see app/Http/Controllers/Api/AutomationController.php:263
* @route '/api/automation/webhooks/{webhookId}/stats'
*/
export const webhookStats = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: webhookStats.url(args, options),
    method: 'get',
})

webhookStats.definition = {
    methods: ["get","head"],
    url: '/api/automation/webhooks/{webhookId}/stats',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AutomationController::webhookStats
* @see app/Http/Controllers/Api/AutomationController.php:263
* @route '/api/automation/webhooks/{webhookId}/stats'
*/
webhookStats.url = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return webhookStats.definition.url
            .replace('{webhookId}', parsedArgs.webhookId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AutomationController::webhookStats
* @see app/Http/Controllers/Api/AutomationController.php:263
* @route '/api/automation/webhooks/{webhookId}/stats'
*/
webhookStats.get = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: webhookStats.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::webhookStats
* @see app/Http/Controllers/Api/AutomationController.php:263
* @route '/api/automation/webhooks/{webhookId}/stats'
*/
webhookStats.head = (args: { webhookId: string | number } | [webhookId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: webhookStats.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::remediate
* @see app/Http/Controllers/Api/AutomationController.php:279
* @route '/api/automation/remediate'
*/
export const remediate = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: remediate.url(options),
    method: 'post',
})

remediate.definition = {
    methods: ["post"],
    url: '/api/automation/remediate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AutomationController::remediate
* @see app/Http/Controllers/Api/AutomationController.php:279
* @route '/api/automation/remediate'
*/
remediate.url = (options?: RouteQueryOptions) => {
    return remediate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AutomationController::remediate
* @see app/Http/Controllers/Api/AutomationController.php:279
* @route '/api/automation/remediate'
*/
remediate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: remediate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::remediationHistory
* @see app/Http/Controllers/Api/AutomationController.php:303
* @route '/api/automation/remediation-history'
*/
export const remediationHistory = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: remediationHistory.url(options),
    method: 'get',
})

remediationHistory.definition = {
    methods: ["get","head"],
    url: '/api/automation/remediation-history',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AutomationController::remediationHistory
* @see app/Http/Controllers/Api/AutomationController.php:303
* @route '/api/automation/remediation-history'
*/
remediationHistory.url = (options?: RouteQueryOptions) => {
    return remediationHistory.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AutomationController::remediationHistory
* @see app/Http/Controllers/Api/AutomationController.php:303
* @route '/api/automation/remediation-history'
*/
remediationHistory.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: remediationHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::remediationHistory
* @see app/Http/Controllers/Api/AutomationController.php:303
* @route '/api/automation/remediation-history'
*/
remediationHistory.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: remediationHistory.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::status
* @see app/Http/Controllers/Api/AutomationController.php:320
* @route '/api/automation/status'
*/
export const status = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status.url(options),
    method: 'get',
})

status.definition = {
    methods: ["get","head"],
    url: '/api/automation/status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AutomationController::status
* @see app/Http/Controllers/Api/AutomationController.php:320
* @route '/api/automation/status'
*/
status.url = (options?: RouteQueryOptions) => {
    return status.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AutomationController::status
* @see app/Http/Controllers/Api/AutomationController.php:320
* @route '/api/automation/status'
*/
status.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::status
* @see app/Http/Controllers/Api/AutomationController.php:320
* @route '/api/automation/status'
*/
status.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: status.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AutomationController::toggleStatus
* @see app/Http/Controllers/Api/AutomationController.php:338
* @route '/api/automation/status'
*/
export const toggleStatus = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggleStatus.url(options),
    method: 'post',
})

toggleStatus.definition = {
    methods: ["post"],
    url: '/api/automation/status',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AutomationController::toggleStatus
* @see app/Http/Controllers/Api/AutomationController.php:338
* @route '/api/automation/status'
*/
toggleStatus.url = (options?: RouteQueryOptions) => {
    return toggleStatus.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AutomationController::toggleStatus
* @see app/Http/Controllers/Api/AutomationController.php:338
* @route '/api/automation/status'
*/
toggleStatus.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggleStatus.url(options),
    method: 'post',
})

const automation = {
    evaluate: Object.assign(evaluate, evaluate),
    triggerWorkflow: Object.assign(triggerWorkflow, triggerWorkflow),
    listWorkflows: Object.assign(listWorkflows, listWorkflows),
    executionStatus: Object.assign(executionStatus, executionStatus),
    cancelExecution: Object.assign(cancelExecution, cancelExecution),
    retryExecution: Object.assign(retryExecution, retryExecution),
    listWebhooks: Object.assign(listWebhooks, listWebhooks),
    registerWebhook: Object.assign(registerWebhook, registerWebhook),
    updateWebhook: Object.assign(updateWebhook, updateWebhook),
    deleteWebhook: Object.assign(deleteWebhook, deleteWebhook),
    testWebhook: Object.assign(testWebhook, testWebhook),
    webhookStats: Object.assign(webhookStats, webhookStats),
    remediate: Object.assign(remediate, remediate),
    remediationHistory: Object.assign(remediationHistory, remediationHistory),
    status: Object.assign(status, status),
    toggleStatus: Object.assign(toggleStatus, toggleStatus),
}

export default automation