import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::dashboard
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:19
* @route '/api/lms/compliance/dashboard'
*/
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/api/lms/compliance/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::dashboard
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:19
* @route '/api/lms/compliance/dashboard'
*/
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::dashboard
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:19
* @route '/api/lms/compliance/dashboard'
*/
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::dashboard
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:19
* @route '/api/lms/compliance/dashboard'
*/
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::dashboard
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:19
* @route '/api/lms/compliance/dashboard'
*/
const dashboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::dashboard
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:19
* @route '/api/lms/compliance/dashboard'
*/
dashboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::dashboard
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:19
* @route '/api/lms/compliance/dashboard'
*/
dashboardForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

dashboard.form = dashboardForm

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::records
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:32
* @route '/api/lms/compliance/records'
*/
export const records = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: records.url(options),
    method: 'get',
})

records.definition = {
    methods: ["get","head"],
    url: '/api/lms/compliance/records',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::records
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:32
* @route '/api/lms/compliance/records'
*/
records.url = (options?: RouteQueryOptions) => {
    return records.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::records
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:32
* @route '/api/lms/compliance/records'
*/
records.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: records.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::records
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:32
* @route '/api/lms/compliance/records'
*/
records.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: records.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::records
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:32
* @route '/api/lms/compliance/records'
*/
const recordsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: records.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::records
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:32
* @route '/api/lms/compliance/records'
*/
recordsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: records.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::records
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:32
* @route '/api/lms/compliance/records'
*/
recordsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: records.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

records.form = recordsForm

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::check
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:58
* @route '/api/lms/compliance/check'
*/
export const check = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: check.url(options),
    method: 'post',
})

check.definition = {
    methods: ["post"],
    url: '/api/lms/compliance/check',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::check
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:58
* @route '/api/lms/compliance/check'
*/
check.url = (options?: RouteQueryOptions) => {
    return check.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::check
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:58
* @route '/api/lms/compliance/check'
*/
check.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: check.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::check
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:58
* @route '/api/lms/compliance/check'
*/
const checkForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: check.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::check
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:58
* @route '/api/lms/compliance/check'
*/
checkForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: check.url(options),
    method: 'post',
})

check.form = checkForm

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::exportMethod
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:78
* @route '/api/lms/compliance/export'
*/
export const exportMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/api/lms/compliance/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::exportMethod
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:78
* @route '/api/lms/compliance/export'
*/
exportMethod.url = (options?: RouteQueryOptions) => {
    return exportMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::exportMethod
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:78
* @route '/api/lms/compliance/export'
*/
exportMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::exportMethod
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:78
* @route '/api/lms/compliance/export'
*/
exportMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::exportMethod
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:78
* @route '/api/lms/compliance/export'
*/
const exportMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::exportMethod
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:78
* @route '/api/lms/compliance/export'
*/
exportMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ComplianceController::exportMethod
* @see app/Http/Controllers/Api/Lms/ComplianceController.php:78
* @route '/api/lms/compliance/export'
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

const ComplianceController = { dashboard, records, check, exportMethod, export: exportMethod }

export default ComplianceController