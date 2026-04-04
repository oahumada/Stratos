import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\ReportController::completion
* @see app/Http/Controllers/Api/Lms/ReportController.php:19
* @route '/api/lms/reports/completion'
*/
export const completion = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: completion.url(options),
    method: 'get',
})

completion.definition = {
    methods: ["get","head"],
    url: '/api/lms/reports/completion',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::completion
* @see app/Http/Controllers/Api/Lms/ReportController.php:19
* @route '/api/lms/reports/completion'
*/
completion.url = (options?: RouteQueryOptions) => {
    return completion.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::completion
* @see app/Http/Controllers/Api/Lms/ReportController.php:19
* @route '/api/lms/reports/completion'
*/
completion.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: completion.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::completion
* @see app/Http/Controllers/Api/Lms/ReportController.php:19
* @route '/api/lms/reports/completion'
*/
completion.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: completion.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::completion
* @see app/Http/Controllers/Api/Lms/ReportController.php:19
* @route '/api/lms/reports/completion'
*/
const completionForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: completion.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::completion
* @see app/Http/Controllers/Api/Lms/ReportController.php:19
* @route '/api/lms/reports/completion'
*/
completionForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: completion.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::completion
* @see app/Http/Controllers/Api/Lms/ReportController.php:19
* @route '/api/lms/reports/completion'
*/
completionForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: completion.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

completion.form = completionForm

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::compliance
* @see app/Http/Controllers/Api/Lms/ReportController.php:33
* @route '/api/lms/reports/compliance'
*/
export const compliance = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: compliance.url(options),
    method: 'get',
})

compliance.definition = {
    methods: ["get","head"],
    url: '/api/lms/reports/compliance',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::compliance
* @see app/Http/Controllers/Api/Lms/ReportController.php:33
* @route '/api/lms/reports/compliance'
*/
compliance.url = (options?: RouteQueryOptions) => {
    return compliance.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::compliance
* @see app/Http/Controllers/Api/Lms/ReportController.php:33
* @route '/api/lms/reports/compliance'
*/
compliance.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: compliance.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::compliance
* @see app/Http/Controllers/Api/Lms/ReportController.php:33
* @route '/api/lms/reports/compliance'
*/
compliance.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: compliance.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::compliance
* @see app/Http/Controllers/Api/Lms/ReportController.php:33
* @route '/api/lms/reports/compliance'
*/
const complianceForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: compliance.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::compliance
* @see app/Http/Controllers/Api/Lms/ReportController.php:33
* @route '/api/lms/reports/compliance'
*/
complianceForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: compliance.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::compliance
* @see app/Http/Controllers/Api/Lms/ReportController.php:33
* @route '/api/lms/reports/compliance'
*/
complianceForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: compliance.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

compliance.form = complianceForm

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::timeToComplete
* @see app/Http/Controllers/Api/Lms/ReportController.php:46
* @route '/api/lms/reports/time-to-complete'
*/
export const timeToComplete = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: timeToComplete.url(options),
    method: 'get',
})

timeToComplete.definition = {
    methods: ["get","head"],
    url: '/api/lms/reports/time-to-complete',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::timeToComplete
* @see app/Http/Controllers/Api/Lms/ReportController.php:46
* @route '/api/lms/reports/time-to-complete'
*/
timeToComplete.url = (options?: RouteQueryOptions) => {
    return timeToComplete.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::timeToComplete
* @see app/Http/Controllers/Api/Lms/ReportController.php:46
* @route '/api/lms/reports/time-to-complete'
*/
timeToComplete.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: timeToComplete.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::timeToComplete
* @see app/Http/Controllers/Api/Lms/ReportController.php:46
* @route '/api/lms/reports/time-to-complete'
*/
timeToComplete.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: timeToComplete.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::timeToComplete
* @see app/Http/Controllers/Api/Lms/ReportController.php:46
* @route '/api/lms/reports/time-to-complete'
*/
const timeToCompleteForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: timeToComplete.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::timeToComplete
* @see app/Http/Controllers/Api/Lms/ReportController.php:46
* @route '/api/lms/reports/time-to-complete'
*/
timeToCompleteForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: timeToComplete.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::timeToComplete
* @see app/Http/Controllers/Api/Lms/ReportController.php:46
* @route '/api/lms/reports/time-to-complete'
*/
timeToCompleteForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: timeToComplete.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

timeToComplete.form = timeToCompleteForm

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::engagement
* @see app/Http/Controllers/Api/Lms/ReportController.php:59
* @route '/api/lms/reports/engagement'
*/
export const engagement = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: engagement.url(options),
    method: 'get',
})

engagement.definition = {
    methods: ["get","head"],
    url: '/api/lms/reports/engagement',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::engagement
* @see app/Http/Controllers/Api/Lms/ReportController.php:59
* @route '/api/lms/reports/engagement'
*/
engagement.url = (options?: RouteQueryOptions) => {
    return engagement.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::engagement
* @see app/Http/Controllers/Api/Lms/ReportController.php:59
* @route '/api/lms/reports/engagement'
*/
engagement.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: engagement.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::engagement
* @see app/Http/Controllers/Api/Lms/ReportController.php:59
* @route '/api/lms/reports/engagement'
*/
engagement.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: engagement.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::engagement
* @see app/Http/Controllers/Api/Lms/ReportController.php:59
* @route '/api/lms/reports/engagement'
*/
const engagementForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: engagement.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::engagement
* @see app/Http/Controllers/Api/Lms/ReportController.php:59
* @route '/api/lms/reports/engagement'
*/
engagementForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: engagement.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::engagement
* @see app/Http/Controllers/Api/Lms/ReportController.php:59
* @route '/api/lms/reports/engagement'
*/
engagementForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: engagement.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

engagement.form = engagementForm

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::exportMethod
* @see app/Http/Controllers/Api/Lms/ReportController.php:72
* @route '/api/lms/reports/export/{type}'
*/
export const exportMethod = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(args, options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/api/lms/reports/export/{type}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::exportMethod
* @see app/Http/Controllers/Api/Lms/ReportController.php:72
* @route '/api/lms/reports/export/{type}'
*/
exportMethod.url = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { type: args }
    }

    if (Array.isArray(args)) {
        args = {
            type: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        type: args.type,
    }

    return exportMethod.definition.url
            .replace('{type}', parsedArgs.type.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::exportMethod
* @see app/Http/Controllers/Api/Lms/ReportController.php:72
* @route '/api/lms/reports/export/{type}'
*/
exportMethod.get = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::exportMethod
* @see app/Http/Controllers/Api/Lms/ReportController.php:72
* @route '/api/lms/reports/export/{type}'
*/
exportMethod.head = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::exportMethod
* @see app/Http/Controllers/Api/Lms/ReportController.php:72
* @route '/api/lms/reports/export/{type}'
*/
const exportMethodForm = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::exportMethod
* @see app/Http/Controllers/Api/Lms/ReportController.php:72
* @route '/api/lms/reports/export/{type}'
*/
exportMethodForm.get = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::exportMethod
* @see app/Http/Controllers/Api/Lms/ReportController.php:72
* @route '/api/lms/reports/export/{type}'
*/
exportMethodForm.head = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

exportMethod.form = exportMethodForm

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::exportPdf
* @see app/Http/Controllers/Api/Lms/ReportController.php:101
* @route '/api/lms/reports/pdf'
*/
export const exportPdf = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportPdf.url(options),
    method: 'get',
})

exportPdf.definition = {
    methods: ["get","head"],
    url: '/api/lms/reports/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::exportPdf
* @see app/Http/Controllers/Api/Lms/ReportController.php:101
* @route '/api/lms/reports/pdf'
*/
exportPdf.url = (options?: RouteQueryOptions) => {
    return exportPdf.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::exportPdf
* @see app/Http/Controllers/Api/Lms/ReportController.php:101
* @route '/api/lms/reports/pdf'
*/
exportPdf.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportPdf.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::exportPdf
* @see app/Http/Controllers/Api/Lms/ReportController.php:101
* @route '/api/lms/reports/pdf'
*/
exportPdf.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportPdf.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::exportPdf
* @see app/Http/Controllers/Api/Lms/ReportController.php:101
* @route '/api/lms/reports/pdf'
*/
const exportPdfForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportPdf.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::exportPdf
* @see app/Http/Controllers/Api/Lms/ReportController.php:101
* @route '/api/lms/reports/pdf'
*/
exportPdfForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportPdf.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ReportController::exportPdf
* @see app/Http/Controllers/Api/Lms/ReportController.php:101
* @route '/api/lms/reports/pdf'
*/
exportPdfForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportPdf.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

exportPdf.form = exportPdfForm

const ReportController = { completion, compliance, timeToComplete, engagement, exportMethod, exportPdf, export: exportMethod }

export default ReportController