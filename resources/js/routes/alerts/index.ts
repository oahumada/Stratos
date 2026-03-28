import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
import thresholds from './thresholds'
import history from './history'
/**
* @see \App\Http\Controllers\Api\AlertController::acknowledge
* @see app/Http/Controllers/Api/AlertController.php:140
* @route '/api/alerts/history/{alert}/acknowledge'
*/
export const acknowledge = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: acknowledge.url(args, options),
    method: 'post',
})

acknowledge.definition = {
    methods: ["post"],
    url: '/api/alerts/history/{alert}/acknowledge',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AlertController::acknowledge
* @see app/Http/Controllers/Api/AlertController.php:140
* @route '/api/alerts/history/{alert}/acknowledge'
*/
acknowledge.url = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { alert: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { alert: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            alert: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        alert: typeof args.alert === 'object'
        ? args.alert.id
        : args.alert,
    }

    return acknowledge.definition.url
            .replace('{alert}', parsedArgs.alert.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::acknowledge
* @see app/Http/Controllers/Api/AlertController.php:140
* @route '/api/alerts/history/{alert}/acknowledge'
*/
acknowledge.post = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: acknowledge.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AlertController::acknowledge
* @see app/Http/Controllers/Api/AlertController.php:140
* @route '/api/alerts/history/{alert}/acknowledge'
*/
const acknowledgeForm = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: acknowledge.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AlertController::acknowledge
* @see app/Http/Controllers/Api/AlertController.php:140
* @route '/api/alerts/history/{alert}/acknowledge'
*/
acknowledgeForm.post = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: acknowledge.url(args, options),
    method: 'post',
})

acknowledge.form = acknowledgeForm

/**
* @see \App\Http\Controllers\Api\AlertController::resolve
* @see app/Http/Controllers/Api/AlertController.php:160
* @route '/api/alerts/history/{alert}/resolve'
*/
export const resolve = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resolve.url(args, options),
    method: 'post',
})

resolve.definition = {
    methods: ["post"],
    url: '/api/alerts/history/{alert}/resolve',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AlertController::resolve
* @see app/Http/Controllers/Api/AlertController.php:160
* @route '/api/alerts/history/{alert}/resolve'
*/
resolve.url = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { alert: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { alert: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            alert: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        alert: typeof args.alert === 'object'
        ? args.alert.id
        : args.alert,
    }

    return resolve.definition.url
            .replace('{alert}', parsedArgs.alert.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::resolve
* @see app/Http/Controllers/Api/AlertController.php:160
* @route '/api/alerts/history/{alert}/resolve'
*/
resolve.post = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resolve.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AlertController::resolve
* @see app/Http/Controllers/Api/AlertController.php:160
* @route '/api/alerts/history/{alert}/resolve'
*/
const resolveForm = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resolve.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AlertController::resolve
* @see app/Http/Controllers/Api/AlertController.php:160
* @route '/api/alerts/history/{alert}/resolve'
*/
resolveForm.post = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resolve.url(args, options),
    method: 'post',
})

resolve.form = resolveForm

/**
* @see \App\Http\Controllers\Api\AlertController::mute
* @see app/Http/Controllers/Api/AlertController.php:176
* @route '/api/alerts/history/{alert}/mute'
*/
export const mute = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: mute.url(args, options),
    method: 'post',
})

mute.definition = {
    methods: ["post"],
    url: '/api/alerts/history/{alert}/mute',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AlertController::mute
* @see app/Http/Controllers/Api/AlertController.php:176
* @route '/api/alerts/history/{alert}/mute'
*/
mute.url = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { alert: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { alert: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            alert: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        alert: typeof args.alert === 'object'
        ? args.alert.id
        : args.alert,
    }

    return mute.definition.url
            .replace('{alert}', parsedArgs.alert.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::mute
* @see app/Http/Controllers/Api/AlertController.php:176
* @route '/api/alerts/history/{alert}/mute'
*/
mute.post = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: mute.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AlertController::mute
* @see app/Http/Controllers/Api/AlertController.php:176
* @route '/api/alerts/history/{alert}/mute'
*/
const muteForm = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: mute.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AlertController::mute
* @see app/Http/Controllers/Api/AlertController.php:176
* @route '/api/alerts/history/{alert}/mute'
*/
muteForm.post = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: mute.url(args, options),
    method: 'post',
})

mute.form = muteForm

/**
* @see \App\Http\Controllers\Api\AlertController::unacknowledged
* @see app/Http/Controllers/Api/AlertController.php:192
* @route '/api/alerts/unacknowledged'
*/
export const unacknowledged = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: unacknowledged.url(options),
    method: 'get',
})

unacknowledged.definition = {
    methods: ["get","head"],
    url: '/api/alerts/unacknowledged',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AlertController::unacknowledged
* @see app/Http/Controllers/Api/AlertController.php:192
* @route '/api/alerts/unacknowledged'
*/
unacknowledged.url = (options?: RouteQueryOptions) => {
    return unacknowledged.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::unacknowledged
* @see app/Http/Controllers/Api/AlertController.php:192
* @route '/api/alerts/unacknowledged'
*/
unacknowledged.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: unacknowledged.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::unacknowledged
* @see app/Http/Controllers/Api/AlertController.php:192
* @route '/api/alerts/unacknowledged'
*/
unacknowledged.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: unacknowledged.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AlertController::unacknowledged
* @see app/Http/Controllers/Api/AlertController.php:192
* @route '/api/alerts/unacknowledged'
*/
const unacknowledgedForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: unacknowledged.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::unacknowledged
* @see app/Http/Controllers/Api/AlertController.php:192
* @route '/api/alerts/unacknowledged'
*/
unacknowledgedForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: unacknowledged.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::unacknowledged
* @see app/Http/Controllers/Api/AlertController.php:192
* @route '/api/alerts/unacknowledged'
*/
unacknowledgedForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: unacknowledged.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

unacknowledged.form = unacknowledgedForm

/**
* @see \App\Http\Controllers\Api\AlertController::critical
* @see app/Http/Controllers/Api/AlertController.php:204
* @route '/api/alerts/critical'
*/
export const critical = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: critical.url(options),
    method: 'get',
})

critical.definition = {
    methods: ["get","head"],
    url: '/api/alerts/critical',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AlertController::critical
* @see app/Http/Controllers/Api/AlertController.php:204
* @route '/api/alerts/critical'
*/
critical.url = (options?: RouteQueryOptions) => {
    return critical.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::critical
* @see app/Http/Controllers/Api/AlertController.php:204
* @route '/api/alerts/critical'
*/
critical.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: critical.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::critical
* @see app/Http/Controllers/Api/AlertController.php:204
* @route '/api/alerts/critical'
*/
critical.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: critical.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AlertController::critical
* @see app/Http/Controllers/Api/AlertController.php:204
* @route '/api/alerts/critical'
*/
const criticalForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: critical.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::critical
* @see app/Http/Controllers/Api/AlertController.php:204
* @route '/api/alerts/critical'
*/
criticalForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: critical.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::critical
* @see app/Http/Controllers/Api/AlertController.php:204
* @route '/api/alerts/critical'
*/
criticalForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: critical.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

critical.form = criticalForm

/**
* @see \App\Http\Controllers\Api\AlertController::statistics
* @see app/Http/Controllers/Api/AlertController.php:216
* @route '/api/alerts/statistics'
*/
export const statistics = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: statistics.url(options),
    method: 'get',
})

statistics.definition = {
    methods: ["get","head"],
    url: '/api/alerts/statistics',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AlertController::statistics
* @see app/Http/Controllers/Api/AlertController.php:216
* @route '/api/alerts/statistics'
*/
statistics.url = (options?: RouteQueryOptions) => {
    return statistics.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::statistics
* @see app/Http/Controllers/Api/AlertController.php:216
* @route '/api/alerts/statistics'
*/
statistics.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: statistics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::statistics
* @see app/Http/Controllers/Api/AlertController.php:216
* @route '/api/alerts/statistics'
*/
statistics.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: statistics.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AlertController::statistics
* @see app/Http/Controllers/Api/AlertController.php:216
* @route '/api/alerts/statistics'
*/
const statisticsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: statistics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::statistics
* @see app/Http/Controllers/Api/AlertController.php:216
* @route '/api/alerts/statistics'
*/
statisticsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: statistics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::statistics
* @see app/Http/Controllers/Api/AlertController.php:216
* @route '/api/alerts/statistics'
*/
statisticsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: statistics.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

statistics.form = statisticsForm

/**
* @see \App\Http\Controllers\Api\AlertController::bulkAcknowledge
* @see app/Http/Controllers/Api/AlertController.php:240
* @route '/api/alerts/bulk-acknowledge'
*/
export const bulkAcknowledge = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: bulkAcknowledge.url(options),
    method: 'post',
})

bulkAcknowledge.definition = {
    methods: ["post"],
    url: '/api/alerts/bulk-acknowledge',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AlertController::bulkAcknowledge
* @see app/Http/Controllers/Api/AlertController.php:240
* @route '/api/alerts/bulk-acknowledge'
*/
bulkAcknowledge.url = (options?: RouteQueryOptions) => {
    return bulkAcknowledge.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::bulkAcknowledge
* @see app/Http/Controllers/Api/AlertController.php:240
* @route '/api/alerts/bulk-acknowledge'
*/
bulkAcknowledge.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: bulkAcknowledge.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AlertController::bulkAcknowledge
* @see app/Http/Controllers/Api/AlertController.php:240
* @route '/api/alerts/bulk-acknowledge'
*/
const bulkAcknowledgeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: bulkAcknowledge.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AlertController::bulkAcknowledge
* @see app/Http/Controllers/Api/AlertController.php:240
* @route '/api/alerts/bulk-acknowledge'
*/
bulkAcknowledgeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: bulkAcknowledge.url(options),
    method: 'post',
})

bulkAcknowledge.form = bulkAcknowledgeForm

/**
* @see \App\Http\Controllers\Api\AlertController::exportMethod
* @see app/Http/Controllers/Api/AlertController.php:268
* @route '/api/alerts/export/history'
*/
export const exportMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/api/alerts/export/history',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AlertController::exportMethod
* @see app/Http/Controllers/Api/AlertController.php:268
* @route '/api/alerts/export/history'
*/
exportMethod.url = (options?: RouteQueryOptions) => {
    return exportMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::exportMethod
* @see app/Http/Controllers/Api/AlertController.php:268
* @route '/api/alerts/export/history'
*/
exportMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::exportMethod
* @see app/Http/Controllers/Api/AlertController.php:268
* @route '/api/alerts/export/history'
*/
exportMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AlertController::exportMethod
* @see app/Http/Controllers/Api/AlertController.php:268
* @route '/api/alerts/export/history'
*/
const exportMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::exportMethod
* @see app/Http/Controllers/Api/AlertController.php:268
* @route '/api/alerts/export/history'
*/
exportMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::exportMethod
* @see app/Http/Controllers/Api/AlertController.php:268
* @route '/api/alerts/export/history'
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

const alerts = {
    thresholds: Object.assign(thresholds, thresholds),
    history: Object.assign(history, history),
    acknowledge: Object.assign(acknowledge, acknowledge),
    resolve: Object.assign(resolve, resolve),
    mute: Object.assign(mute, mute),
    unacknowledged: Object.assign(unacknowledged, unacknowledged),
    critical: Object.assign(critical, critical),
    statistics: Object.assign(statistics, statistics),
    bulkAcknowledge: Object.assign(bulkAcknowledge, bulkAcknowledge),
    export: Object.assign(exportMethod, exportMethod),
}

export default alerts