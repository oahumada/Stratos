import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\DevelopmentActionController::updateStatus
* @see app/Http/Controllers/Api/DevelopmentActionController.php:23
* @route '/api/development-actions/{id}/status'
*/
export const updateStatus = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateStatus.url(args, options),
    method: 'patch',
})

updateStatus.definition = {
    methods: ["patch"],
    url: '/api/development-actions/{id}/status',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\DevelopmentActionController::updateStatus
* @see app/Http/Controllers/Api/DevelopmentActionController.php:23
* @route '/api/development-actions/{id}/status'
*/
updateStatus.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return updateStatus.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\DevelopmentActionController::updateStatus
* @see app/Http/Controllers/Api/DevelopmentActionController.php:23
* @route '/api/development-actions/{id}/status'
*/
updateStatus.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateStatus.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\DevelopmentActionController::launchLms
* @see app/Http/Controllers/Api/DevelopmentActionController.php:55
* @route '/api/development-actions/{id}/launch-lms'
*/
export const launchLms = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: launchLms.url(args, options),
    method: 'post',
})

launchLms.definition = {
    methods: ["post"],
    url: '/api/development-actions/{id}/launch-lms',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\DevelopmentActionController::launchLms
* @see app/Http/Controllers/Api/DevelopmentActionController.php:55
* @route '/api/development-actions/{id}/launch-lms'
*/
launchLms.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return launchLms.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\DevelopmentActionController::launchLms
* @see app/Http/Controllers/Api/DevelopmentActionController.php:55
* @route '/api/development-actions/{id}/launch-lms'
*/
launchLms.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: launchLms.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\DevelopmentActionController::syncLms
* @see app/Http/Controllers/Api/DevelopmentActionController.php:73
* @route '/api/development-actions/{id}/sync-lms'
*/
export const syncLms = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: syncLms.url(args, options),
    method: 'post',
})

syncLms.definition = {
    methods: ["post"],
    url: '/api/development-actions/{id}/sync-lms',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\DevelopmentActionController::syncLms
* @see app/Http/Controllers/Api/DevelopmentActionController.php:73
* @route '/api/development-actions/{id}/sync-lms'
*/
syncLms.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return syncLms.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\DevelopmentActionController::syncLms
* @see app/Http/Controllers/Api/DevelopmentActionController.php:73
* @route '/api/development-actions/{id}/sync-lms'
*/
syncLms.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: syncLms.url(args, options),
    method: 'post',
})

const DevelopmentActionController = { updateStatus, launchLms, syncLms }

export default DevelopmentActionController