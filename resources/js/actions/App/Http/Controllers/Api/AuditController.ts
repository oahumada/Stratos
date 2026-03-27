import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\AuditController::index
* @see app/Http/Controllers/Api/AuditController.php:22
* @route '/api/admin/audit-logs'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/admin/audit-logs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AuditController::index
* @see app/Http/Controllers/Api/AuditController.php:22
* @route '/api/admin/audit-logs'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AuditController::index
* @see app/Http/Controllers/Api/AuditController.php:22
* @route '/api/admin/audit-logs'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AuditController::index
* @see app/Http/Controllers/Api/AuditController.php:22
* @route '/api/admin/audit-logs'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AuditController::index
* @see app/Http/Controllers/Api/AuditController.php:22
* @route '/api/admin/audit-logs'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AuditController::index
* @see app/Http/Controllers/Api/AuditController.php:22
* @route '/api/admin/audit-logs'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AuditController::index
* @see app/Http/Controllers/Api/AuditController.php:22
* @route '/api/admin/audit-logs'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Api\AuditController::heatmap
* @see app/Http/Controllers/Api/AuditController.php:59
* @route '/api/admin/audit-logs/heatmap'
*/
export const heatmap = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: heatmap.url(options),
    method: 'get',
})

heatmap.definition = {
    methods: ["get","head"],
    url: '/api/admin/audit-logs/heatmap',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AuditController::heatmap
* @see app/Http/Controllers/Api/AuditController.php:59
* @route '/api/admin/audit-logs/heatmap'
*/
heatmap.url = (options?: RouteQueryOptions) => {
    return heatmap.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AuditController::heatmap
* @see app/Http/Controllers/Api/AuditController.php:59
* @route '/api/admin/audit-logs/heatmap'
*/
heatmap.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: heatmap.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AuditController::heatmap
* @see app/Http/Controllers/Api/AuditController.php:59
* @route '/api/admin/audit-logs/heatmap'
*/
heatmap.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: heatmap.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AuditController::heatmap
* @see app/Http/Controllers/Api/AuditController.php:59
* @route '/api/admin/audit-logs/heatmap'
*/
const heatmapForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: heatmap.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AuditController::heatmap
* @see app/Http/Controllers/Api/AuditController.php:59
* @route '/api/admin/audit-logs/heatmap'
*/
heatmapForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: heatmap.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AuditController::heatmap
* @see app/Http/Controllers/Api/AuditController.php:59
* @route '/api/admin/audit-logs/heatmap'
*/
heatmapForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: heatmap.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

heatmap.form = heatmapForm

/**
* @see \App\Http\Controllers\Api\AuditController::exportMethod
* @see app/Http/Controllers/Api/AuditController.php:72
* @route '/api/admin/audit-logs/export'
*/
export const exportMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/api/admin/audit-logs/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AuditController::exportMethod
* @see app/Http/Controllers/Api/AuditController.php:72
* @route '/api/admin/audit-logs/export'
*/
exportMethod.url = (options?: RouteQueryOptions) => {
    return exportMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AuditController::exportMethod
* @see app/Http/Controllers/Api/AuditController.php:72
* @route '/api/admin/audit-logs/export'
*/
exportMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AuditController::exportMethod
* @see app/Http/Controllers/Api/AuditController.php:72
* @route '/api/admin/audit-logs/export'
*/
exportMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AuditController::exportMethod
* @see app/Http/Controllers/Api/AuditController.php:72
* @route '/api/admin/audit-logs/export'
*/
const exportMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AuditController::exportMethod
* @see app/Http/Controllers/Api/AuditController.php:72
* @route '/api/admin/audit-logs/export'
*/
exportMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AuditController::exportMethod
* @see app/Http/Controllers/Api/AuditController.php:72
* @route '/api/admin/audit-logs/export'
*/
exportMethodForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

exportMethod.form = exportMethodForm

/**
* @see \App\Http\Controllers\Api\AuditController::entityTimeline
* @see app/Http/Controllers/Api/AuditController.php:94
* @route '/api/admin/audit-logs/{entityType}/{entityId}/timeline'
*/
export const entityTimeline = (args: { entityType: string | number, entityId: string | number } | [entityType: string | number, entityId: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: entityTimeline.url(args, options),
    method: 'get',
})

entityTimeline.definition = {
    methods: ["get","head"],
    url: '/api/admin/audit-logs/{entityType}/{entityId}/timeline',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AuditController::entityTimeline
* @see app/Http/Controllers/Api/AuditController.php:94
* @route '/api/admin/audit-logs/{entityType}/{entityId}/timeline'
*/
entityTimeline.url = (args: { entityType: string | number, entityId: string | number } | [entityType: string | number, entityId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            entityType: args[0],
            entityId: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        entityType: args.entityType,
        entityId: args.entityId,
    }

    return entityTimeline.definition.url
            .replace('{entityType}', parsedArgs.entityType.toString())
            .replace('{entityId}', parsedArgs.entityId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AuditController::entityTimeline
* @see app/Http/Controllers/Api/AuditController.php:94
* @route '/api/admin/audit-logs/{entityType}/{entityId}/timeline'
*/
entityTimeline.get = (args: { entityType: string | number, entityId: string | number } | [entityType: string | number, entityId: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: entityTimeline.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AuditController::entityTimeline
* @see app/Http/Controllers/Api/AuditController.php:94
* @route '/api/admin/audit-logs/{entityType}/{entityId}/timeline'
*/
entityTimeline.head = (args: { entityType: string | number, entityId: string | number } | [entityType: string | number, entityId: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: entityTimeline.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AuditController::entityTimeline
* @see app/Http/Controllers/Api/AuditController.php:94
* @route '/api/admin/audit-logs/{entityType}/{entityId}/timeline'
*/
const entityTimelineForm = (args: { entityType: string | number, entityId: string | number } | [entityType: string | number, entityId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: entityTimeline.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AuditController::entityTimeline
* @see app/Http/Controllers/Api/AuditController.php:94
* @route '/api/admin/audit-logs/{entityType}/{entityId}/timeline'
*/
entityTimelineForm.get = (args: { entityType: string | number, entityId: string | number } | [entityType: string | number, entityId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: entityTimeline.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AuditController::entityTimeline
* @see app/Http/Controllers/Api/AuditController.php:94
* @route '/api/admin/audit-logs/{entityType}/{entityId}/timeline'
*/
entityTimelineForm.head = (args: { entityType: string | number, entityId: string | number } | [entityType: string | number, entityId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: entityTimeline.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

entityTimeline.form = entityTimelineForm

/**
* @see \App\Http\Controllers\Api\AuditController::userActivity
* @see app/Http/Controllers/Api/AuditController.php:104
* @route '/api/admin/audit-logs/users/{userId}/activity'
*/
export const userActivity = (args: { userId: string | number } | [userId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: userActivity.url(args, options),
    method: 'get',
})

userActivity.definition = {
    methods: ["get","head"],
    url: '/api/admin/audit-logs/users/{userId}/activity',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AuditController::userActivity
* @see app/Http/Controllers/Api/AuditController.php:104
* @route '/api/admin/audit-logs/users/{userId}/activity'
*/
userActivity.url = (args: { userId: string | number } | [userId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { userId: args }
    }

    if (Array.isArray(args)) {
        args = {
            userId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        userId: args.userId,
    }

    return userActivity.definition.url
            .replace('{userId}', parsedArgs.userId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AuditController::userActivity
* @see app/Http/Controllers/Api/AuditController.php:104
* @route '/api/admin/audit-logs/users/{userId}/activity'
*/
userActivity.get = (args: { userId: string | number } | [userId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: userActivity.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AuditController::userActivity
* @see app/Http/Controllers/Api/AuditController.php:104
* @route '/api/admin/audit-logs/users/{userId}/activity'
*/
userActivity.head = (args: { userId: string | number } | [userId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: userActivity.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AuditController::userActivity
* @see app/Http/Controllers/Api/AuditController.php:104
* @route '/api/admin/audit-logs/users/{userId}/activity'
*/
const userActivityForm = (args: { userId: string | number } | [userId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: userActivity.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AuditController::userActivity
* @see app/Http/Controllers/Api/AuditController.php:104
* @route '/api/admin/audit-logs/users/{userId}/activity'
*/
userActivityForm.get = (args: { userId: string | number } | [userId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: userActivity.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AuditController::userActivity
* @see app/Http/Controllers/Api/AuditController.php:104
* @route '/api/admin/audit-logs/users/{userId}/activity'
*/
userActivityForm.head = (args: { userId: string | number } | [userId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: userActivity.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

userActivity.form = userActivityForm

const AuditController = { index, heatmap, exportMethod, entityTimeline, userActivity, export: exportMethod }

export default AuditController