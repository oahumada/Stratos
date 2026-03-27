import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
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

const verificationConfig = {
    store: Object.assign(store, store),
}

export default verificationConfig