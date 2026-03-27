import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Deployment\VerificationConfigurationController::show
* @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:12
* @route '/deployment/verification-config'
*/
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/deployment/verification-config',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Deployment\VerificationConfigurationController::show
* @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:12
* @route '/deployment/verification-config'
*/
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationConfigurationController::show
* @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:12
* @route '/deployment/verification-config'
*/
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationConfigurationController::show
* @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:12
* @route '/deployment/verification-config'
*/
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationConfigurationController::store
* @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:21
* @route '/deployment/verification-config'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/deployment/verification-config',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Deployment\VerificationConfigurationController::store
* @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:21
* @route '/deployment/verification-config'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationConfigurationController::store
* @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:21
* @route '/deployment/verification-config'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationConfigurationController::status
* @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:52
* @route '/api/deployment/verification-status'
*/
export const status = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status.url(options),
    method: 'get',
})

status.definition = {
    methods: ["get","head"],
    url: '/api/deployment/verification-status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Deployment\VerificationConfigurationController::status
* @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:52
* @route '/api/deployment/verification-status'
*/
status.url = (options?: RouteQueryOptions) => {
    return status.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Deployment\VerificationConfigurationController::status
* @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:52
* @route '/api/deployment/verification-status'
*/
status.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Deployment\VerificationConfigurationController::status
* @see app/Http/Controllers/Deployment/VerificationConfigurationController.php:52
* @route '/api/deployment/verification-status'
*/
status.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: status.url(options),
    method: 'head',
})

const VerificationConfigurationController = { show, store, status }

export default VerificationConfigurationController