import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\VideoPlayerController::getTracking
* @see app/Http/Controllers/Api/Lms/VideoPlayerController.php:18
* @route '/api/lms/lessons/{lesson}/video/tracking'
*/
export const getTracking = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getTracking.url(args, options),
    method: 'get',
})

getTracking.definition = {
    methods: ["get","head"],
    url: '/api/lms/lessons/{lesson}/video/tracking',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\VideoPlayerController::getTracking
* @see app/Http/Controllers/Api/Lms/VideoPlayerController.php:18
* @route '/api/lms/lessons/{lesson}/video/tracking'
*/
getTracking.url = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { lesson: args }
    }

    if (Array.isArray(args)) {
        args = {
            lesson: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        lesson: args.lesson,
    }

    return getTracking.definition.url
            .replace('{lesson}', parsedArgs.lesson.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\VideoPlayerController::getTracking
* @see app/Http/Controllers/Api/Lms/VideoPlayerController.php:18
* @route '/api/lms/lessons/{lesson}/video/tracking'
*/
getTracking.get = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getTracking.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\VideoPlayerController::getTracking
* @see app/Http/Controllers/Api/Lms/VideoPlayerController.php:18
* @route '/api/lms/lessons/{lesson}/video/tracking'
*/
getTracking.head = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getTracking.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\VideoPlayerController::getTracking
* @see app/Http/Controllers/Api/Lms/VideoPlayerController.php:18
* @route '/api/lms/lessons/{lesson}/video/tracking'
*/
const getTrackingForm = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getTracking.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\VideoPlayerController::getTracking
* @see app/Http/Controllers/Api/Lms/VideoPlayerController.php:18
* @route '/api/lms/lessons/{lesson}/video/tracking'
*/
getTrackingForm.get = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getTracking.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\VideoPlayerController::getTracking
* @see app/Http/Controllers/Api/Lms/VideoPlayerController.php:18
* @route '/api/lms/lessons/{lesson}/video/tracking'
*/
getTrackingForm.head = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getTracking.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getTracking.form = getTrackingForm

/**
* @see \App\Http\Controllers\Api\Lms\VideoPlayerController::updateProgress
* @see app/Http/Controllers/Api/Lms/VideoPlayerController.php:59
* @route '/api/lms/lessons/{lesson}/video/progress'
*/
export const updateProgress = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateProgress.url(args, options),
    method: 'post',
})

updateProgress.definition = {
    methods: ["post"],
    url: '/api/lms/lessons/{lesson}/video/progress',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\VideoPlayerController::updateProgress
* @see app/Http/Controllers/Api/Lms/VideoPlayerController.php:59
* @route '/api/lms/lessons/{lesson}/video/progress'
*/
updateProgress.url = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { lesson: args }
    }

    if (Array.isArray(args)) {
        args = {
            lesson: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        lesson: args.lesson,
    }

    return updateProgress.definition.url
            .replace('{lesson}', parsedArgs.lesson.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\VideoPlayerController::updateProgress
* @see app/Http/Controllers/Api/Lms/VideoPlayerController.php:59
* @route '/api/lms/lessons/{lesson}/video/progress'
*/
updateProgress.post = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateProgress.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\VideoPlayerController::updateProgress
* @see app/Http/Controllers/Api/Lms/VideoPlayerController.php:59
* @route '/api/lms/lessons/{lesson}/video/progress'
*/
const updateProgressForm = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateProgress.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\VideoPlayerController::updateProgress
* @see app/Http/Controllers/Api/Lms/VideoPlayerController.php:59
* @route '/api/lms/lessons/{lesson}/video/progress'
*/
updateProgressForm.post = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateProgress.url(args, options),
    method: 'post',
})

updateProgress.form = updateProgressForm

/**
* @see \App\Http\Controllers\Api\Lms\VideoPlayerController::stats
* @see app/Http/Controllers/Api/Lms/VideoPlayerController.php:88
* @route '/api/lms/lessons/{lesson}/video/stats'
*/
export const stats = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: stats.url(args, options),
    method: 'get',
})

stats.definition = {
    methods: ["get","head"],
    url: '/api/lms/lessons/{lesson}/video/stats',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\VideoPlayerController::stats
* @see app/Http/Controllers/Api/Lms/VideoPlayerController.php:88
* @route '/api/lms/lessons/{lesson}/video/stats'
*/
stats.url = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { lesson: args }
    }

    if (Array.isArray(args)) {
        args = {
            lesson: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        lesson: args.lesson,
    }

    return stats.definition.url
            .replace('{lesson}', parsedArgs.lesson.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\VideoPlayerController::stats
* @see app/Http/Controllers/Api/Lms/VideoPlayerController.php:88
* @route '/api/lms/lessons/{lesson}/video/stats'
*/
stats.get = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: stats.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\VideoPlayerController::stats
* @see app/Http/Controllers/Api/Lms/VideoPlayerController.php:88
* @route '/api/lms/lessons/{lesson}/video/stats'
*/
stats.head = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: stats.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\VideoPlayerController::stats
* @see app/Http/Controllers/Api/Lms/VideoPlayerController.php:88
* @route '/api/lms/lessons/{lesson}/video/stats'
*/
const statsForm = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: stats.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\VideoPlayerController::stats
* @see app/Http/Controllers/Api/Lms/VideoPlayerController.php:88
* @route '/api/lms/lessons/{lesson}/video/stats'
*/
statsForm.get = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: stats.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\VideoPlayerController::stats
* @see app/Http/Controllers/Api/Lms/VideoPlayerController.php:88
* @route '/api/lms/lessons/{lesson}/video/stats'
*/
statsForm.head = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: stats.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

stats.form = statsForm

const VideoPlayerController = { getTracking, updateProgress, stats }

export default VideoPlayerController