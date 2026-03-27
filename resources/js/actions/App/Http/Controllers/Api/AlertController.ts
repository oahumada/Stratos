import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\AlertController::indexThresholds
* @see app/Http/Controllers/Api/AlertController.php:23
* @route '/api/alerts/thresholds'
*/
export const indexThresholds = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexThresholds.url(options),
    method: 'get',
})

indexThresholds.definition = {
    methods: ["get","head"],
    url: '/api/alerts/thresholds',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AlertController::indexThresholds
* @see app/Http/Controllers/Api/AlertController.php:23
* @route '/api/alerts/thresholds'
*/
indexThresholds.url = (options?: RouteQueryOptions) => {
    return indexThresholds.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::indexThresholds
* @see app/Http/Controllers/Api/AlertController.php:23
* @route '/api/alerts/thresholds'
*/
indexThresholds.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexThresholds.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::indexThresholds
* @see app/Http/Controllers/Api/AlertController.php:23
* @route '/api/alerts/thresholds'
*/
indexThresholds.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: indexThresholds.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AlertController::indexThresholds
* @see app/Http/Controllers/Api/AlertController.php:23
* @route '/api/alerts/thresholds'
*/
const indexThresholdsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexThresholds.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::indexThresholds
* @see app/Http/Controllers/Api/AlertController.php:23
* @route '/api/alerts/thresholds'
*/
indexThresholdsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexThresholds.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::indexThresholds
* @see app/Http/Controllers/Api/AlertController.php:23
* @route '/api/alerts/thresholds'
*/
indexThresholdsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexThresholds.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

indexThresholds.form = indexThresholdsForm

/**
* @see \App\Http\Controllers\Api\AlertController::storeThreshold
* @see app/Http/Controllers/Api/AlertController.php:40
* @route '/api/alerts/thresholds'
*/
export const storeThreshold = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeThreshold.url(options),
    method: 'post',
})

storeThreshold.definition = {
    methods: ["post"],
    url: '/api/alerts/thresholds',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AlertController::storeThreshold
* @see app/Http/Controllers/Api/AlertController.php:40
* @route '/api/alerts/thresholds'
*/
storeThreshold.url = (options?: RouteQueryOptions) => {
    return storeThreshold.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::storeThreshold
* @see app/Http/Controllers/Api/AlertController.php:40
* @route '/api/alerts/thresholds'
*/
storeThreshold.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeThreshold.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AlertController::storeThreshold
* @see app/Http/Controllers/Api/AlertController.php:40
* @route '/api/alerts/thresholds'
*/
const storeThresholdForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeThreshold.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AlertController::storeThreshold
* @see app/Http/Controllers/Api/AlertController.php:40
* @route '/api/alerts/thresholds'
*/
storeThresholdForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeThreshold.url(options),
    method: 'post',
})

storeThreshold.form = storeThresholdForm

/**
* @see \App\Http\Controllers\Api\AlertController::showThreshold
* @see app/Http/Controllers/Api/AlertController.php:57
* @route '/api/alerts/thresholds/{threshold}'
*/
export const showThreshold = (args: { threshold: string | number | { id: string | number } } | [threshold: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showThreshold.url(args, options),
    method: 'get',
})

showThreshold.definition = {
    methods: ["get","head"],
    url: '/api/alerts/thresholds/{threshold}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AlertController::showThreshold
* @see app/Http/Controllers/Api/AlertController.php:57
* @route '/api/alerts/thresholds/{threshold}'
*/
showThreshold.url = (args: { threshold: string | number | { id: string | number } } | [threshold: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { threshold: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { threshold: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            threshold: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        threshold: typeof args.threshold === 'object'
        ? args.threshold.id
        : args.threshold,
    }

    return showThreshold.definition.url
            .replace('{threshold}', parsedArgs.threshold.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::showThreshold
* @see app/Http/Controllers/Api/AlertController.php:57
* @route '/api/alerts/thresholds/{threshold}'
*/
showThreshold.get = (args: { threshold: string | number | { id: string | number } } | [threshold: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showThreshold.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::showThreshold
* @see app/Http/Controllers/Api/AlertController.php:57
* @route '/api/alerts/thresholds/{threshold}'
*/
showThreshold.head = (args: { threshold: string | number | { id: string | number } } | [threshold: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showThreshold.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AlertController::showThreshold
* @see app/Http/Controllers/Api/AlertController.php:57
* @route '/api/alerts/thresholds/{threshold}'
*/
const showThresholdForm = (args: { threshold: string | number | { id: string | number } } | [threshold: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showThreshold.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::showThreshold
* @see app/Http/Controllers/Api/AlertController.php:57
* @route '/api/alerts/thresholds/{threshold}'
*/
showThresholdForm.get = (args: { threshold: string | number | { id: string | number } } | [threshold: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showThreshold.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::showThreshold
* @see app/Http/Controllers/Api/AlertController.php:57
* @route '/api/alerts/thresholds/{threshold}'
*/
showThresholdForm.head = (args: { threshold: string | number | { id: string | number } } | [threshold: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showThreshold.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

showThreshold.form = showThresholdForm

/**
* @see \App\Http\Controllers\Api\AlertController::updateThreshold
* @see app/Http/Controllers/Api/AlertController.php:73
* @route '/api/alerts/thresholds/{threshold}'
*/
export const updateThreshold = (args: { threshold: string | number | { id: string | number } } | [threshold: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateThreshold.url(args, options),
    method: 'patch',
})

updateThreshold.definition = {
    methods: ["patch"],
    url: '/api/alerts/thresholds/{threshold}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\AlertController::updateThreshold
* @see app/Http/Controllers/Api/AlertController.php:73
* @route '/api/alerts/thresholds/{threshold}'
*/
updateThreshold.url = (args: { threshold: string | number | { id: string | number } } | [threshold: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { threshold: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { threshold: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            threshold: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        threshold: typeof args.threshold === 'object'
        ? args.threshold.id
        : args.threshold,
    }

    return updateThreshold.definition.url
            .replace('{threshold}', parsedArgs.threshold.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::updateThreshold
* @see app/Http/Controllers/Api/AlertController.php:73
* @route '/api/alerts/thresholds/{threshold}'
*/
updateThreshold.patch = (args: { threshold: string | number | { id: string | number } } | [threshold: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateThreshold.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\AlertController::updateThreshold
* @see app/Http/Controllers/Api/AlertController.php:73
* @route '/api/alerts/thresholds/{threshold}'
*/
const updateThresholdForm = (args: { threshold: string | number | { id: string | number } } | [threshold: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateThreshold.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AlertController::updateThreshold
* @see app/Http/Controllers/Api/AlertController.php:73
* @route '/api/alerts/thresholds/{threshold}'
*/
updateThresholdForm.patch = (args: { threshold: string | number | { id: string | number } } | [threshold: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateThreshold.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateThreshold.form = updateThresholdForm

/**
* @see \App\Http\Controllers\Api\AlertController::destroyThreshold
* @see app/Http/Controllers/Api/AlertController.php:93
* @route '/api/alerts/thresholds/{threshold}'
*/
export const destroyThreshold = (args: { threshold: string | number | { id: string | number } } | [threshold: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyThreshold.url(args, options),
    method: 'delete',
})

destroyThreshold.definition = {
    methods: ["delete"],
    url: '/api/alerts/thresholds/{threshold}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\AlertController::destroyThreshold
* @see app/Http/Controllers/Api/AlertController.php:93
* @route '/api/alerts/thresholds/{threshold}'
*/
destroyThreshold.url = (args: { threshold: string | number | { id: string | number } } | [threshold: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { threshold: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { threshold: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            threshold: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        threshold: typeof args.threshold === 'object'
        ? args.threshold.id
        : args.threshold,
    }

    return destroyThreshold.definition.url
            .replace('{threshold}', parsedArgs.threshold.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::destroyThreshold
* @see app/Http/Controllers/Api/AlertController.php:93
* @route '/api/alerts/thresholds/{threshold}'
*/
destroyThreshold.delete = (args: { threshold: string | number | { id: string | number } } | [threshold: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyThreshold.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\AlertController::destroyThreshold
* @see app/Http/Controllers/Api/AlertController.php:93
* @route '/api/alerts/thresholds/{threshold}'
*/
const destroyThresholdForm = (args: { threshold: string | number | { id: string | number } } | [threshold: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyThreshold.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AlertController::destroyThreshold
* @see app/Http/Controllers/Api/AlertController.php:93
* @route '/api/alerts/thresholds/{threshold}'
*/
destroyThresholdForm.delete = (args: { threshold: string | number | { id: string | number } } | [threshold: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyThreshold.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroyThreshold.form = destroyThresholdForm

/**
* @see \App\Http\Controllers\Api\AlertController::indexHistory
* @see app/Http/Controllers/Api/AlertController.php:111
* @route '/api/alerts/history'
*/
export const indexHistory = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexHistory.url(options),
    method: 'get',
})

indexHistory.definition = {
    methods: ["get","head"],
    url: '/api/alerts/history',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AlertController::indexHistory
* @see app/Http/Controllers/Api/AlertController.php:111
* @route '/api/alerts/history'
*/
indexHistory.url = (options?: RouteQueryOptions) => {
    return indexHistory.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::indexHistory
* @see app/Http/Controllers/Api/AlertController.php:111
* @route '/api/alerts/history'
*/
indexHistory.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::indexHistory
* @see app/Http/Controllers/Api/AlertController.php:111
* @route '/api/alerts/history'
*/
indexHistory.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: indexHistory.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AlertController::indexHistory
* @see app/Http/Controllers/Api/AlertController.php:111
* @route '/api/alerts/history'
*/
const indexHistoryForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::indexHistory
* @see app/Http/Controllers/Api/AlertController.php:111
* @route '/api/alerts/history'
*/
indexHistoryForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::indexHistory
* @see app/Http/Controllers/Api/AlertController.php:111
* @route '/api/alerts/history'
*/
indexHistoryForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexHistory.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

indexHistory.form = indexHistoryForm

/**
* @see \App\Http\Controllers\Api\AlertController::showHistory
* @see app/Http/Controllers/Api/AlertController.php:126
* @route '/api/alerts/history/{alert}'
*/
export const showHistory = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showHistory.url(args, options),
    method: 'get',
})

showHistory.definition = {
    methods: ["get","head"],
    url: '/api/alerts/history/{alert}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AlertController::showHistory
* @see app/Http/Controllers/Api/AlertController.php:126
* @route '/api/alerts/history/{alert}'
*/
showHistory.url = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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

    return showHistory.definition.url
            .replace('{alert}', parsedArgs.alert.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::showHistory
* @see app/Http/Controllers/Api/AlertController.php:126
* @route '/api/alerts/history/{alert}'
*/
showHistory.get = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showHistory.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::showHistory
* @see app/Http/Controllers/Api/AlertController.php:126
* @route '/api/alerts/history/{alert}'
*/
showHistory.head = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showHistory.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AlertController::showHistory
* @see app/Http/Controllers/Api/AlertController.php:126
* @route '/api/alerts/history/{alert}'
*/
const showHistoryForm = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showHistory.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::showHistory
* @see app/Http/Controllers/Api/AlertController.php:126
* @route '/api/alerts/history/{alert}'
*/
showHistoryForm.get = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showHistory.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::showHistory
* @see app/Http/Controllers/Api/AlertController.php:126
* @route '/api/alerts/history/{alert}'
*/
showHistoryForm.head = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showHistory.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

showHistory.form = showHistoryForm

/**
* @see \App\Http\Controllers\Api\AlertController::acknowledgeAlert
* @see app/Http/Controllers/Api/AlertController.php:140
* @route '/api/alerts/history/{alert}/acknowledge'
*/
export const acknowledgeAlert = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: acknowledgeAlert.url(args, options),
    method: 'post',
})

acknowledgeAlert.definition = {
    methods: ["post"],
    url: '/api/alerts/history/{alert}/acknowledge',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AlertController::acknowledgeAlert
* @see app/Http/Controllers/Api/AlertController.php:140
* @route '/api/alerts/history/{alert}/acknowledge'
*/
acknowledgeAlert.url = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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

    return acknowledgeAlert.definition.url
            .replace('{alert}', parsedArgs.alert.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::acknowledgeAlert
* @see app/Http/Controllers/Api/AlertController.php:140
* @route '/api/alerts/history/{alert}/acknowledge'
*/
acknowledgeAlert.post = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: acknowledgeAlert.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AlertController::acknowledgeAlert
* @see app/Http/Controllers/Api/AlertController.php:140
* @route '/api/alerts/history/{alert}/acknowledge'
*/
const acknowledgeAlertForm = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: acknowledgeAlert.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AlertController::acknowledgeAlert
* @see app/Http/Controllers/Api/AlertController.php:140
* @route '/api/alerts/history/{alert}/acknowledge'
*/
acknowledgeAlertForm.post = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: acknowledgeAlert.url(args, options),
    method: 'post',
})

acknowledgeAlert.form = acknowledgeAlertForm

/**
* @see \App\Http\Controllers\Api\AlertController::resolveAlert
* @see app/Http/Controllers/Api/AlertController.php:160
* @route '/api/alerts/history/{alert}/resolve'
*/
export const resolveAlert = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resolveAlert.url(args, options),
    method: 'post',
})

resolveAlert.definition = {
    methods: ["post"],
    url: '/api/alerts/history/{alert}/resolve',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AlertController::resolveAlert
* @see app/Http/Controllers/Api/AlertController.php:160
* @route '/api/alerts/history/{alert}/resolve'
*/
resolveAlert.url = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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

    return resolveAlert.definition.url
            .replace('{alert}', parsedArgs.alert.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::resolveAlert
* @see app/Http/Controllers/Api/AlertController.php:160
* @route '/api/alerts/history/{alert}/resolve'
*/
resolveAlert.post = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resolveAlert.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AlertController::resolveAlert
* @see app/Http/Controllers/Api/AlertController.php:160
* @route '/api/alerts/history/{alert}/resolve'
*/
const resolveAlertForm = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resolveAlert.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AlertController::resolveAlert
* @see app/Http/Controllers/Api/AlertController.php:160
* @route '/api/alerts/history/{alert}/resolve'
*/
resolveAlertForm.post = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resolveAlert.url(args, options),
    method: 'post',
})

resolveAlert.form = resolveAlertForm

/**
* @see \App\Http\Controllers\Api\AlertController::muteAlert
* @see app/Http/Controllers/Api/AlertController.php:176
* @route '/api/alerts/history/{alert}/mute'
*/
export const muteAlert = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: muteAlert.url(args, options),
    method: 'post',
})

muteAlert.definition = {
    methods: ["post"],
    url: '/api/alerts/history/{alert}/mute',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AlertController::muteAlert
* @see app/Http/Controllers/Api/AlertController.php:176
* @route '/api/alerts/history/{alert}/mute'
*/
muteAlert.url = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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

    return muteAlert.definition.url
            .replace('{alert}', parsedArgs.alert.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::muteAlert
* @see app/Http/Controllers/Api/AlertController.php:176
* @route '/api/alerts/history/{alert}/mute'
*/
muteAlert.post = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: muteAlert.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AlertController::muteAlert
* @see app/Http/Controllers/Api/AlertController.php:176
* @route '/api/alerts/history/{alert}/mute'
*/
const muteAlertForm = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: muteAlert.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AlertController::muteAlert
* @see app/Http/Controllers/Api/AlertController.php:176
* @route '/api/alerts/history/{alert}/mute'
*/
muteAlertForm.post = (args: { alert: string | number | { id: string | number } } | [alert: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: muteAlert.url(args, options),
    method: 'post',
})

muteAlert.form = muteAlertForm

/**
* @see \App\Http\Controllers\Api\AlertController::getUnacknowledged
* @see app/Http/Controllers/Api/AlertController.php:192
* @route '/api/alerts/unacknowledged'
*/
export const getUnacknowledged = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getUnacknowledged.url(options),
    method: 'get',
})

getUnacknowledged.definition = {
    methods: ["get","head"],
    url: '/api/alerts/unacknowledged',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AlertController::getUnacknowledged
* @see app/Http/Controllers/Api/AlertController.php:192
* @route '/api/alerts/unacknowledged'
*/
getUnacknowledged.url = (options?: RouteQueryOptions) => {
    return getUnacknowledged.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::getUnacknowledged
* @see app/Http/Controllers/Api/AlertController.php:192
* @route '/api/alerts/unacknowledged'
*/
getUnacknowledged.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getUnacknowledged.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::getUnacknowledged
* @see app/Http/Controllers/Api/AlertController.php:192
* @route '/api/alerts/unacknowledged'
*/
getUnacknowledged.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getUnacknowledged.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AlertController::getUnacknowledged
* @see app/Http/Controllers/Api/AlertController.php:192
* @route '/api/alerts/unacknowledged'
*/
const getUnacknowledgedForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getUnacknowledged.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::getUnacknowledged
* @see app/Http/Controllers/Api/AlertController.php:192
* @route '/api/alerts/unacknowledged'
*/
getUnacknowledgedForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getUnacknowledged.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::getUnacknowledged
* @see app/Http/Controllers/Api/AlertController.php:192
* @route '/api/alerts/unacknowledged'
*/
getUnacknowledgedForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getUnacknowledged.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getUnacknowledged.form = getUnacknowledgedForm

/**
* @see \App\Http\Controllers\Api\AlertController::getCritical
* @see app/Http/Controllers/Api/AlertController.php:204
* @route '/api/alerts/critical'
*/
export const getCritical = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCritical.url(options),
    method: 'get',
})

getCritical.definition = {
    methods: ["get","head"],
    url: '/api/alerts/critical',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AlertController::getCritical
* @see app/Http/Controllers/Api/AlertController.php:204
* @route '/api/alerts/critical'
*/
getCritical.url = (options?: RouteQueryOptions) => {
    return getCritical.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::getCritical
* @see app/Http/Controllers/Api/AlertController.php:204
* @route '/api/alerts/critical'
*/
getCritical.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCritical.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::getCritical
* @see app/Http/Controllers/Api/AlertController.php:204
* @route '/api/alerts/critical'
*/
getCritical.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCritical.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AlertController::getCritical
* @see app/Http/Controllers/Api/AlertController.php:204
* @route '/api/alerts/critical'
*/
const getCriticalForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCritical.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::getCritical
* @see app/Http/Controllers/Api/AlertController.php:204
* @route '/api/alerts/critical'
*/
getCriticalForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCritical.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::getCritical
* @see app/Http/Controllers/Api/AlertController.php:204
* @route '/api/alerts/critical'
*/
getCriticalForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCritical.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getCritical.form = getCriticalForm

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
* @see \App\Http\Controllers\Api\AlertController::exportHistory
* @see app/Http/Controllers/Api/AlertController.php:268
* @route '/api/alerts/export/history'
*/
export const exportHistory = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportHistory.url(options),
    method: 'get',
})

exportHistory.definition = {
    methods: ["get","head"],
    url: '/api/alerts/export/history',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AlertController::exportHistory
* @see app/Http/Controllers/Api/AlertController.php:268
* @route '/api/alerts/export/history'
*/
exportHistory.url = (options?: RouteQueryOptions) => {
    return exportHistory.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AlertController::exportHistory
* @see app/Http/Controllers/Api/AlertController.php:268
* @route '/api/alerts/export/history'
*/
exportHistory.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::exportHistory
* @see app/Http/Controllers/Api/AlertController.php:268
* @route '/api/alerts/export/history'
*/
exportHistory.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportHistory.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AlertController::exportHistory
* @see app/Http/Controllers/Api/AlertController.php:268
* @route '/api/alerts/export/history'
*/
const exportHistoryForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::exportHistory
* @see app/Http/Controllers/Api/AlertController.php:268
* @route '/api/alerts/export/history'
*/
exportHistoryForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AlertController::exportHistory
* @see app/Http/Controllers/Api/AlertController.php:268
* @route '/api/alerts/export/history'
*/
exportHistoryForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportHistory.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

exportHistory.form = exportHistoryForm

const AlertController = { indexThresholds, storeThreshold, showThreshold, updateThreshold, destroyThreshold, indexHistory, showHistory, acknowledgeAlert, resolveAlert, muteAlert, getUnacknowledged, getCritical, statistics, bulkAcknowledge, exportHistory }

export default AlertController