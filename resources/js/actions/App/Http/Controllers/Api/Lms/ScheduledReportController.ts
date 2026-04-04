import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\ScheduledReportController::index
* @see app/Http/Controllers/Api/Lms/ScheduledReportController.php:18
* @route '/api/lms/reports/scheduled'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/lms/reports/scheduled',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\ScheduledReportController::index
* @see app/Http/Controllers/Api/Lms/ScheduledReportController.php:18
* @route '/api/lms/reports/scheduled'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\ScheduledReportController::index
* @see app/Http/Controllers/Api/Lms/ScheduledReportController.php:18
* @route '/api/lms/reports/scheduled'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScheduledReportController::index
* @see app/Http/Controllers/Api/Lms/ScheduledReportController.php:18
* @route '/api/lms/reports/scheduled'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScheduledReportController::index
* @see app/Http/Controllers/Api/Lms/ScheduledReportController.php:18
* @route '/api/lms/reports/scheduled'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScheduledReportController::index
* @see app/Http/Controllers/Api/Lms/ScheduledReportController.php:18
* @route '/api/lms/reports/scheduled'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScheduledReportController::index
* @see app/Http/Controllers/Api/Lms/ScheduledReportController.php:18
* @route '/api/lms/reports/scheduled'
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
* @see \App\Http\Controllers\Api\Lms\ScheduledReportController::store
* @see app/Http/Controllers/Api/Lms/ScheduledReportController.php:31
* @route '/api/lms/reports/scheduled'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/lms/reports/scheduled',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\ScheduledReportController::store
* @see app/Http/Controllers/Api/Lms/ScheduledReportController.php:31
* @route '/api/lms/reports/scheduled'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\ScheduledReportController::store
* @see app/Http/Controllers/Api/Lms/ScheduledReportController.php:31
* @route '/api/lms/reports/scheduled'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScheduledReportController::store
* @see app/Http/Controllers/Api/Lms/ScheduledReportController.php:31
* @route '/api/lms/reports/scheduled'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScheduledReportController::store
* @see app/Http/Controllers/Api/Lms/ScheduledReportController.php:31
* @route '/api/lms/reports/scheduled'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\Lms\ScheduledReportController::update
* @see app/Http/Controllers/Api/Lms/ScheduledReportController.php:57
* @route '/api/lms/reports/scheduled/{scheduledReport}'
*/
export const update = (args: { scheduledReport: string | number } | [scheduledReport: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/lms/reports/scheduled/{scheduledReport}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\Lms\ScheduledReportController::update
* @see app/Http/Controllers/Api/Lms/ScheduledReportController.php:57
* @route '/api/lms/reports/scheduled/{scheduledReport}'
*/
update.url = (args: { scheduledReport: string | number } | [scheduledReport: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scheduledReport: args }
    }

    if (Array.isArray(args)) {
        args = {
            scheduledReport: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scheduledReport: args.scheduledReport,
    }

    return update.definition.url
            .replace('{scheduledReport}', parsedArgs.scheduledReport.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\ScheduledReportController::update
* @see app/Http/Controllers/Api/Lms/ScheduledReportController.php:57
* @route '/api/lms/reports/scheduled/{scheduledReport}'
*/
update.put = (args: { scheduledReport: string | number } | [scheduledReport: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScheduledReportController::update
* @see app/Http/Controllers/Api/Lms/ScheduledReportController.php:57
* @route '/api/lms/reports/scheduled/{scheduledReport}'
*/
const updateForm = (args: { scheduledReport: string | number } | [scheduledReport: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScheduledReportController::update
* @see app/Http/Controllers/Api/Lms/ScheduledReportController.php:57
* @route '/api/lms/reports/scheduled/{scheduledReport}'
*/
updateForm.put = (args: { scheduledReport: string | number } | [scheduledReport: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\Api\Lms\ScheduledReportController::destroy
* @see app/Http/Controllers/Api/Lms/ScheduledReportController.php:73
* @route '/api/lms/reports/scheduled/{scheduledReport}'
*/
export const destroy = (args: { scheduledReport: string | number } | [scheduledReport: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/lms/reports/scheduled/{scheduledReport}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\Lms\ScheduledReportController::destroy
* @see app/Http/Controllers/Api/Lms/ScheduledReportController.php:73
* @route '/api/lms/reports/scheduled/{scheduledReport}'
*/
destroy.url = (args: { scheduledReport: string | number } | [scheduledReport: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scheduledReport: args }
    }

    if (Array.isArray(args)) {
        args = {
            scheduledReport: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scheduledReport: args.scheduledReport,
    }

    return destroy.definition.url
            .replace('{scheduledReport}', parsedArgs.scheduledReport.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\ScheduledReportController::destroy
* @see app/Http/Controllers/Api/Lms/ScheduledReportController.php:73
* @route '/api/lms/reports/scheduled/{scheduledReport}'
*/
destroy.delete = (args: { scheduledReport: string | number } | [scheduledReport: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScheduledReportController::destroy
* @see app/Http/Controllers/Api/Lms/ScheduledReportController.php:73
* @route '/api/lms/reports/scheduled/{scheduledReport}'
*/
const destroyForm = (args: { scheduledReport: string | number } | [scheduledReport: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScheduledReportController::destroy
* @see app/Http/Controllers/Api/Lms/ScheduledReportController.php:73
* @route '/api/lms/reports/scheduled/{scheduledReport}'
*/
destroyForm.delete = (args: { scheduledReport: string | number } | [scheduledReport: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const ScheduledReportController = { index, store, update, destroy }

export default ScheduledReportController