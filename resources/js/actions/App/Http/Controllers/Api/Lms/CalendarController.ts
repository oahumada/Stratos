import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::index
* @see app/Http/Controllers/Api/Lms/CalendarController.php:18
* @route '/api/lms/calendar'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/lms/calendar',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::index
* @see app/Http/Controllers/Api/Lms/CalendarController.php:18
* @route '/api/lms/calendar'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::index
* @see app/Http/Controllers/Api/Lms/CalendarController.php:18
* @route '/api/lms/calendar'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::index
* @see app/Http/Controllers/Api/Lms/CalendarController.php:18
* @route '/api/lms/calendar'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::index
* @see app/Http/Controllers/Api/Lms/CalendarController.php:18
* @route '/api/lms/calendar'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::index
* @see app/Http/Controllers/Api/Lms/CalendarController.php:18
* @route '/api/lms/calendar'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::index
* @see app/Http/Controllers/Api/Lms/CalendarController.php:18
* @route '/api/lms/calendar'
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
* @see \App\Http\Controllers\Api\Lms\CalendarController::store
* @see app/Http/Controllers/Api/Lms/CalendarController.php:31
* @route '/api/lms/calendar'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/lms/calendar',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::store
* @see app/Http/Controllers/Api/Lms/CalendarController.php:31
* @route '/api/lms/calendar'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::store
* @see app/Http/Controllers/Api/Lms/CalendarController.php:31
* @route '/api/lms/calendar'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::store
* @see app/Http/Controllers/Api/Lms/CalendarController.php:31
* @route '/api/lms/calendar'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::store
* @see app/Http/Controllers/Api/Lms/CalendarController.php:31
* @route '/api/lms/calendar'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::destroy
* @see app/Http/Controllers/Api/Lms/CalendarController.php:59
* @route '/api/lms/calendar/{calendarEvent}'
*/
export const destroy = (args: { calendarEvent: string | number } | [calendarEvent: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/lms/calendar/{calendarEvent}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::destroy
* @see app/Http/Controllers/Api/Lms/CalendarController.php:59
* @route '/api/lms/calendar/{calendarEvent}'
*/
destroy.url = (args: { calendarEvent: string | number } | [calendarEvent: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { calendarEvent: args }
    }

    if (Array.isArray(args)) {
        args = {
            calendarEvent: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        calendarEvent: args.calendarEvent,
    }

    return destroy.definition.url
            .replace('{calendarEvent}', parsedArgs.calendarEvent.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::destroy
* @see app/Http/Controllers/Api/Lms/CalendarController.php:59
* @route '/api/lms/calendar/{calendarEvent}'
*/
destroy.delete = (args: { calendarEvent: string | number } | [calendarEvent: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::destroy
* @see app/Http/Controllers/Api/Lms/CalendarController.php:59
* @route '/api/lms/calendar/{calendarEvent}'
*/
const destroyForm = (args: { calendarEvent: string | number } | [calendarEvent: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::destroy
* @see app/Http/Controllers/Api/Lms/CalendarController.php:59
* @route '/api/lms/calendar/{calendarEvent}'
*/
destroyForm.delete = (args: { calendarEvent: string | number } | [calendarEvent: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::ical
* @see app/Http/Controllers/Api/Lms/CalendarController.php:71
* @route '/api/lms/calendar/ical'
*/
export const ical = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ical.url(options),
    method: 'get',
})

ical.definition = {
    methods: ["get","head"],
    url: '/api/lms/calendar/ical',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::ical
* @see app/Http/Controllers/Api/Lms/CalendarController.php:71
* @route '/api/lms/calendar/ical'
*/
ical.url = (options?: RouteQueryOptions) => {
    return ical.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::ical
* @see app/Http/Controllers/Api/Lms/CalendarController.php:71
* @route '/api/lms/calendar/ical'
*/
ical.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ical.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::ical
* @see app/Http/Controllers/Api/Lms/CalendarController.php:71
* @route '/api/lms/calendar/ical'
*/
ical.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ical.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::ical
* @see app/Http/Controllers/Api/Lms/CalendarController.php:71
* @route '/api/lms/calendar/ical'
*/
const icalForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ical.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::ical
* @see app/Http/Controllers/Api/Lms/CalendarController.php:71
* @route '/api/lms/calendar/ical'
*/
icalForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ical.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::ical
* @see app/Http/Controllers/Api/Lms/CalendarController.php:71
* @route '/api/lms/calendar/ical'
*/
icalForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ical.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ical.form = icalForm

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::syncCompliance
* @see app/Http/Controllers/Api/Lms/CalendarController.php:84
* @route '/api/lms/calendar/sync-compliance'
*/
export const syncCompliance = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: syncCompliance.url(options),
    method: 'post',
})

syncCompliance.definition = {
    methods: ["post"],
    url: '/api/lms/calendar/sync-compliance',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::syncCompliance
* @see app/Http/Controllers/Api/Lms/CalendarController.php:84
* @route '/api/lms/calendar/sync-compliance'
*/
syncCompliance.url = (options?: RouteQueryOptions) => {
    return syncCompliance.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::syncCompliance
* @see app/Http/Controllers/Api/Lms/CalendarController.php:84
* @route '/api/lms/calendar/sync-compliance'
*/
syncCompliance.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: syncCompliance.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::syncCompliance
* @see app/Http/Controllers/Api/Lms/CalendarController.php:84
* @route '/api/lms/calendar/sync-compliance'
*/
const syncComplianceForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: syncCompliance.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CalendarController::syncCompliance
* @see app/Http/Controllers/Api/Lms/CalendarController.php:84
* @route '/api/lms/calendar/sync-compliance'
*/
syncComplianceForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: syncCompliance.url(options),
    method: 'post',
})

syncCompliance.form = syncComplianceForm

const CalendarController = { index, store, destroy, ical, syncCompliance }

export default CalendarController