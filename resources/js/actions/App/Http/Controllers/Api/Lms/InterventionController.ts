import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::index
* @see app/Http/Controllers/Api/Lms/InterventionController.php:17
* @route '/api/lms/interventions'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/lms/interventions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::index
* @see app/Http/Controllers/Api/Lms/InterventionController.php:17
* @route '/api/lms/interventions'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::index
* @see app/Http/Controllers/Api/Lms/InterventionController.php:17
* @route '/api/lms/interventions'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::index
* @see app/Http/Controllers/Api/Lms/InterventionController.php:17
* @route '/api/lms/interventions'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::index
* @see app/Http/Controllers/Api/Lms/InterventionController.php:17
* @route '/api/lms/interventions'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::index
* @see app/Http/Controllers/Api/Lms/InterventionController.php:17
* @route '/api/lms/interventions'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::index
* @see app/Http/Controllers/Api/Lms/InterventionController.php:17
* @route '/api/lms/interventions'
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
* @see \App\Http\Controllers\Api\Lms\InterventionController::store
* @see app/Http/Controllers/Api/Lms/InterventionController.php:50
* @route '/api/lms/interventions'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/lms/interventions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::store
* @see app/Http/Controllers/Api/Lms/InterventionController.php:50
* @route '/api/lms/interventions'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::store
* @see app/Http/Controllers/Api/Lms/InterventionController.php:50
* @route '/api/lms/interventions'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::store
* @see app/Http/Controllers/Api/Lms/InterventionController.php:50
* @route '/api/lms/interventions'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::store
* @see app/Http/Controllers/Api/Lms/InterventionController.php:50
* @route '/api/lms/interventions'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::reset
* @see app/Http/Controllers/Api/Lms/InterventionController.php:96
* @route '/api/lms/interventions/reset'
*/
export const reset = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reset.url(options),
    method: 'post',
})

reset.definition = {
    methods: ["post"],
    url: '/api/lms/interventions/reset',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::reset
* @see app/Http/Controllers/Api/Lms/InterventionController.php:96
* @route '/api/lms/interventions/reset'
*/
reset.url = (options?: RouteQueryOptions) => {
    return reset.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::reset
* @see app/Http/Controllers/Api/Lms/InterventionController.php:96
* @route '/api/lms/interventions/reset'
*/
reset.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reset.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::reset
* @see app/Http/Controllers/Api/Lms/InterventionController.php:96
* @route '/api/lms/interventions/reset'
*/
const resetForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reset.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::reset
* @see app/Http/Controllers/Api/Lms/InterventionController.php:96
* @route '/api/lms/interventions/reset'
*/
resetForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reset.url(options),
    method: 'post',
})

reset.form = resetForm

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::complete
* @see app/Http/Controllers/Api/Lms/InterventionController.php:119
* @route '/api/lms/interventions/{enrollmentId}/complete'
*/
export const complete = (args: { enrollmentId: string | number } | [enrollmentId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: complete.url(args, options),
    method: 'post',
})

complete.definition = {
    methods: ["post"],
    url: '/api/lms/interventions/{enrollmentId}/complete',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::complete
* @see app/Http/Controllers/Api/Lms/InterventionController.php:119
* @route '/api/lms/interventions/{enrollmentId}/complete'
*/
complete.url = (args: { enrollmentId: string | number } | [enrollmentId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { enrollmentId: args }
    }

    if (Array.isArray(args)) {
        args = {
            enrollmentId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        enrollmentId: args.enrollmentId,
    }

    return complete.definition.url
            .replace('{enrollmentId}', parsedArgs.enrollmentId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::complete
* @see app/Http/Controllers/Api/Lms/InterventionController.php:119
* @route '/api/lms/interventions/{enrollmentId}/complete'
*/
complete.post = (args: { enrollmentId: string | number } | [enrollmentId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: complete.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::complete
* @see app/Http/Controllers/Api/Lms/InterventionController.php:119
* @route '/api/lms/interventions/{enrollmentId}/complete'
*/
const completeForm = (args: { enrollmentId: string | number } | [enrollmentId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: complete.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::complete
* @see app/Http/Controllers/Api/Lms/InterventionController.php:119
* @route '/api/lms/interventions/{enrollmentId}/complete'
*/
completeForm.post = (args: { enrollmentId: string | number } | [enrollmentId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: complete.url(args, options),
    method: 'post',
})

complete.form = completeForm

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::preferences
* @see app/Http/Controllers/Api/Lms/InterventionController.php:150
* @route '/api/lms/preferences'
*/
export const preferences = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: preferences.url(options),
    method: 'get',
})

preferences.definition = {
    methods: ["get","head"],
    url: '/api/lms/preferences',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::preferences
* @see app/Http/Controllers/Api/Lms/InterventionController.php:150
* @route '/api/lms/preferences'
*/
preferences.url = (options?: RouteQueryOptions) => {
    return preferences.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::preferences
* @see app/Http/Controllers/Api/Lms/InterventionController.php:150
* @route '/api/lms/preferences'
*/
preferences.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: preferences.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::preferences
* @see app/Http/Controllers/Api/Lms/InterventionController.php:150
* @route '/api/lms/preferences'
*/
preferences.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: preferences.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::preferences
* @see app/Http/Controllers/Api/Lms/InterventionController.php:150
* @route '/api/lms/preferences'
*/
const preferencesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: preferences.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::preferences
* @see app/Http/Controllers/Api/Lms/InterventionController.php:150
* @route '/api/lms/preferences'
*/
preferencesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: preferences.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::preferences
* @see app/Http/Controllers/Api/Lms/InterventionController.php:150
* @route '/api/lms/preferences'
*/
preferencesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: preferences.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

preferences.form = preferencesForm

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::updatePreferences
* @see app/Http/Controllers/Api/Lms/InterventionController.php:161
* @route '/api/lms/preferences'
*/
export const updatePreferences = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updatePreferences.url(options),
    method: 'patch',
})

updatePreferences.definition = {
    methods: ["patch"],
    url: '/api/lms/preferences',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::updatePreferences
* @see app/Http/Controllers/Api/Lms/InterventionController.php:161
* @route '/api/lms/preferences'
*/
updatePreferences.url = (options?: RouteQueryOptions) => {
    return updatePreferences.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::updatePreferences
* @see app/Http/Controllers/Api/Lms/InterventionController.php:161
* @route '/api/lms/preferences'
*/
updatePreferences.patch = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updatePreferences.url(options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::updatePreferences
* @see app/Http/Controllers/Api/Lms/InterventionController.php:161
* @route '/api/lms/preferences'
*/
const updatePreferencesForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updatePreferences.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\InterventionController::updatePreferences
* @see app/Http/Controllers/Api/Lms/InterventionController.php:161
* @route '/api/lms/preferences'
*/
updatePreferencesForm.patch = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updatePreferences.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updatePreferences.form = updatePreferencesForm

const InterventionController = { index, store, reset, complete, preferences, updatePreferences }

export default InterventionController