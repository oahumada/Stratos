import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\LtiController::platforms
* @see app/Http/Controllers/Api/Lms/LtiController.php:16
* @route '/api/lms/lti/platforms'
*/
export const platforms = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: platforms.url(options),
    method: 'get',
})

platforms.definition = {
    methods: ["get","head"],
    url: '/api/lms/lti/platforms',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\LtiController::platforms
* @see app/Http/Controllers/Api/Lms/LtiController.php:16
* @route '/api/lms/lti/platforms'
*/
platforms.url = (options?: RouteQueryOptions) => {
    return platforms.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\LtiController::platforms
* @see app/Http/Controllers/Api/Lms/LtiController.php:16
* @route '/api/lms/lti/platforms'
*/
platforms.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: platforms.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LtiController::platforms
* @see app/Http/Controllers/Api/Lms/LtiController.php:16
* @route '/api/lms/lti/platforms'
*/
platforms.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: platforms.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\LtiController::platforms
* @see app/Http/Controllers/Api/Lms/LtiController.php:16
* @route '/api/lms/lti/platforms'
*/
const platformsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: platforms.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LtiController::platforms
* @see app/Http/Controllers/Api/Lms/LtiController.php:16
* @route '/api/lms/lti/platforms'
*/
platformsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: platforms.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LtiController::platforms
* @see app/Http/Controllers/Api/Lms/LtiController.php:16
* @route '/api/lms/lti/platforms'
*/
platformsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: platforms.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

platforms.form = platformsForm

/**
* @see \App\Http\Controllers\Api\Lms\LtiController::registerPlatform
* @see app/Http/Controllers/Api/Lms/LtiController.php:23
* @route '/api/lms/lti/platforms'
*/
export const registerPlatform = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: registerPlatform.url(options),
    method: 'post',
})

registerPlatform.definition = {
    methods: ["post"],
    url: '/api/lms/lti/platforms',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\LtiController::registerPlatform
* @see app/Http/Controllers/Api/Lms/LtiController.php:23
* @route '/api/lms/lti/platforms'
*/
registerPlatform.url = (options?: RouteQueryOptions) => {
    return registerPlatform.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\LtiController::registerPlatform
* @see app/Http/Controllers/Api/Lms/LtiController.php:23
* @route '/api/lms/lti/platforms'
*/
registerPlatform.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: registerPlatform.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\LtiController::registerPlatform
* @see app/Http/Controllers/Api/Lms/LtiController.php:23
* @route '/api/lms/lti/platforms'
*/
const registerPlatformForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: registerPlatform.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\LtiController::registerPlatform
* @see app/Http/Controllers/Api/Lms/LtiController.php:23
* @route '/api/lms/lti/platforms'
*/
registerPlatformForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: registerPlatform.url(options),
    method: 'post',
})

registerPlatform.form = registerPlatformForm

/**
* @see \App\Http\Controllers\Api\Lms\LtiController::launch
* @see app/Http/Controllers/Api/Lms/LtiController.php:41
* @route '/api/lms/lti/launch'
*/
export const launch = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: launch.url(options),
    method: 'post',
})

launch.definition = {
    methods: ["post"],
    url: '/api/lms/lti/launch',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\LtiController::launch
* @see app/Http/Controllers/Api/Lms/LtiController.php:41
* @route '/api/lms/lti/launch'
*/
launch.url = (options?: RouteQueryOptions) => {
    return launch.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\LtiController::launch
* @see app/Http/Controllers/Api/Lms/LtiController.php:41
* @route '/api/lms/lti/launch'
*/
launch.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: launch.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\LtiController::launch
* @see app/Http/Controllers/Api/Lms/LtiController.php:41
* @route '/api/lms/lti/launch'
*/
const launchForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: launch.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\LtiController::launch
* @see app/Http/Controllers/Api/Lms/LtiController.php:41
* @route '/api/lms/lti/launch'
*/
launchForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: launch.url(options),
    method: 'post',
})

launch.form = launchForm

const LtiController = { platforms, registerPlatform, launch }

export default LtiController