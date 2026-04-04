import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::upload
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:17
* @route '/api/lms/scorm/upload'
*/
export const upload = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: upload.url(options),
    method: 'post',
})

upload.definition = {
    methods: ["post"],
    url: '/api/lms/scorm/upload',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::upload
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:17
* @route '/api/lms/scorm/upload'
*/
upload.url = (options?: RouteQueryOptions) => {
    return upload.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::upload
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:17
* @route '/api/lms/scorm/upload'
*/
upload.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: upload.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::upload
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:17
* @route '/api/lms/scorm/upload'
*/
const uploadForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: upload.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::upload
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:17
* @route '/api/lms/scorm/upload'
*/
uploadForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: upload.url(options),
    method: 'post',
})

upload.form = uploadForm

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::index
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:44
* @route '/api/lms/scorm/packages'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/lms/scorm/packages',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::index
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:44
* @route '/api/lms/scorm/packages'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::index
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:44
* @route '/api/lms/scorm/packages'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::index
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:44
* @route '/api/lms/scorm/packages'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::index
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:44
* @route '/api/lms/scorm/packages'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::index
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:44
* @route '/api/lms/scorm/packages'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::index
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:44
* @route '/api/lms/scorm/packages'
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
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::launch
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:55
* @route '/api/lms/scorm/{id}/launch'
*/
export const launch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: launch.url(args, options),
    method: 'get',
})

launch.definition = {
    methods: ["get","head"],
    url: '/api/lms/scorm/{id}/launch',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::launch
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:55
* @route '/api/lms/scorm/{id}/launch'
*/
launch.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return launch.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::launch
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:55
* @route '/api/lms/scorm/{id}/launch'
*/
launch.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: launch.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::launch
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:55
* @route '/api/lms/scorm/{id}/launch'
*/
launch.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: launch.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::launch
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:55
* @route '/api/lms/scorm/{id}/launch'
*/
const launchForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: launch.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::launch
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:55
* @route '/api/lms/scorm/{id}/launch'
*/
launchForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: launch.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::launch
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:55
* @route '/api/lms/scorm/{id}/launch'
*/
launchForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: launch.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

launch.form = launchForm

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::saveCmi
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:65
* @route '/api/lms/scorm/{id}/cmi'
*/
export const saveCmi = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveCmi.url(args, options),
    method: 'post',
})

saveCmi.definition = {
    methods: ["post"],
    url: '/api/lms/scorm/{id}/cmi',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::saveCmi
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:65
* @route '/api/lms/scorm/{id}/cmi'
*/
saveCmi.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return saveCmi.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::saveCmi
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:65
* @route '/api/lms/scorm/{id}/cmi'
*/
saveCmi.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveCmi.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::saveCmi
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:65
* @route '/api/lms/scorm/{id}/cmi'
*/
const saveCmiForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: saveCmi.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::saveCmi
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:65
* @route '/api/lms/scorm/{id}/cmi'
*/
saveCmiForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: saveCmi.url(args, options),
    method: 'post',
})

saveCmi.form = saveCmiForm

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::tracking
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:80
* @route '/api/lms/scorm/{id}/tracking'
*/
export const tracking = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: tracking.url(args, options),
    method: 'get',
})

tracking.definition = {
    methods: ["get","head"],
    url: '/api/lms/scorm/{id}/tracking',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::tracking
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:80
* @route '/api/lms/scorm/{id}/tracking'
*/
tracking.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return tracking.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::tracking
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:80
* @route '/api/lms/scorm/{id}/tracking'
*/
tracking.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: tracking.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::tracking
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:80
* @route '/api/lms/scorm/{id}/tracking'
*/
tracking.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: tracking.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::tracking
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:80
* @route '/api/lms/scorm/{id}/tracking'
*/
const trackingForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: tracking.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::tracking
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:80
* @route '/api/lms/scorm/{id}/tracking'
*/
trackingForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: tracking.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::tracking
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:80
* @route '/api/lms/scorm/{id}/tracking'
*/
trackingForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: tracking.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

tracking.form = trackingForm

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::destroy
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:90
* @route '/api/lms/scorm/{id}'
*/
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/lms/scorm/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::destroy
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:90
* @route '/api/lms/scorm/{id}'
*/
destroy.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::destroy
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:90
* @route '/api/lms/scorm/{id}'
*/
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::destroy
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:90
* @route '/api/lms/scorm/{id}'
*/
const destroyForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\ScormPlayerController::destroy
* @see app/Http/Controllers/Api/Lms/ScormPlayerController.php:90
* @route '/api/lms/scorm/{id}'
*/
destroyForm.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const ScormPlayerController = { upload, index, launch, saveCmi, tracking, destroy }

export default ScormPlayerController