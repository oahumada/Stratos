import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::index
* @see app/Http/Controllers/Api/Lms/CertificateController.php:19
* @route '/api/lms/certificates'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/lms/certificates',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::index
* @see app/Http/Controllers/Api/Lms/CertificateController.php:19
* @route '/api/lms/certificates'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::index
* @see app/Http/Controllers/Api/Lms/CertificateController.php:19
* @route '/api/lms/certificates'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::index
* @see app/Http/Controllers/Api/Lms/CertificateController.php:19
* @route '/api/lms/certificates'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::index
* @see app/Http/Controllers/Api/Lms/CertificateController.php:19
* @route '/api/lms/certificates'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::index
* @see app/Http/Controllers/Api/Lms/CertificateController.php:19
* @route '/api/lms/certificates'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::index
* @see app/Http/Controllers/Api/Lms/CertificateController.php:19
* @route '/api/lms/certificates'
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
* @see \App\Http\Controllers\Api\Lms\CertificateController::show
* @see app/Http/Controllers/Api/Lms/CertificateController.php:25
* @route '/api/lms/certificates/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/lms/certificates/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::show
* @see app/Http/Controllers/Api/Lms/CertificateController.php:25
* @route '/api/lms/certificates/{id}'
*/
show.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::show
* @see app/Http/Controllers/Api/Lms/CertificateController.php:25
* @route '/api/lms/certificates/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::show
* @see app/Http/Controllers/Api/Lms/CertificateController.php:25
* @route '/api/lms/certificates/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::show
* @see app/Http/Controllers/Api/Lms/CertificateController.php:25
* @route '/api/lms/certificates/{id}'
*/
const showForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::show
* @see app/Http/Controllers/Api/Lms/CertificateController.php:25
* @route '/api/lms/certificates/{id}'
*/
showForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::show
* @see app/Http/Controllers/Api/Lms/CertificateController.php:25
* @route '/api/lms/certificates/{id}'
*/
showForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::download
* @see app/Http/Controllers/Api/Lms/CertificateController.php:31
* @route '/api/lms/certificates/{id}/download'
*/
export const download = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: download.url(args, options),
    method: 'get',
})

download.definition = {
    methods: ["get","head"],
    url: '/api/lms/certificates/{id}/download',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::download
* @see app/Http/Controllers/Api/Lms/CertificateController.php:31
* @route '/api/lms/certificates/{id}/download'
*/
download.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return download.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::download
* @see app/Http/Controllers/Api/Lms/CertificateController.php:31
* @route '/api/lms/certificates/{id}/download'
*/
download.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: download.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::download
* @see app/Http/Controllers/Api/Lms/CertificateController.php:31
* @route '/api/lms/certificates/{id}/download'
*/
download.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: download.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::download
* @see app/Http/Controllers/Api/Lms/CertificateController.php:31
* @route '/api/lms/certificates/{id}/download'
*/
const downloadForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::download
* @see app/Http/Controllers/Api/Lms/CertificateController.php:31
* @route '/api/lms/certificates/{id}/download'
*/
downloadForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::download
* @see app/Http/Controllers/Api/Lms/CertificateController.php:31
* @route '/api/lms/certificates/{id}/download'
*/
downloadForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

download.form = downloadForm

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::verify
* @see app/Http/Controllers/Api/Lms/CertificateController.php:37
* @route '/api/lms/certificates/{id}/verify'
*/
export const verify = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: verify.url(args, options),
    method: 'get',
})

verify.definition = {
    methods: ["get","head"],
    url: '/api/lms/certificates/{id}/verify',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::verify
* @see app/Http/Controllers/Api/Lms/CertificateController.php:37
* @route '/api/lms/certificates/{id}/verify'
*/
verify.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return verify.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::verify
* @see app/Http/Controllers/Api/Lms/CertificateController.php:37
* @route '/api/lms/certificates/{id}/verify'
*/
verify.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: verify.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::verify
* @see app/Http/Controllers/Api/Lms/CertificateController.php:37
* @route '/api/lms/certificates/{id}/verify'
*/
verify.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: verify.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::verify
* @see app/Http/Controllers/Api/Lms/CertificateController.php:37
* @route '/api/lms/certificates/{id}/verify'
*/
const verifyForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::verify
* @see app/Http/Controllers/Api/Lms/CertificateController.php:37
* @route '/api/lms/certificates/{id}/verify'
*/
verifyForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::verify
* @see app/Http/Controllers/Api/Lms/CertificateController.php:37
* @route '/api/lms/certificates/{id}/verify'
*/
verifyForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

verify.form = verifyForm

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::revoke
* @see app/Http/Controllers/Api/Lms/CertificateController.php:44
* @route '/api/lms/certificates/{id}/revoke'
*/
export const revoke = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: revoke.url(args, options),
    method: 'post',
})

revoke.definition = {
    methods: ["post"],
    url: '/api/lms/certificates/{id}/revoke',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::revoke
* @see app/Http/Controllers/Api/Lms/CertificateController.php:44
* @route '/api/lms/certificates/{id}/revoke'
*/
revoke.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return revoke.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::revoke
* @see app/Http/Controllers/Api/Lms/CertificateController.php:44
* @route '/api/lms/certificates/{id}/revoke'
*/
revoke.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: revoke.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::revoke
* @see app/Http/Controllers/Api/Lms/CertificateController.php:44
* @route '/api/lms/certificates/{id}/revoke'
*/
const revokeForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: revoke.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::revoke
* @see app/Http/Controllers/Api/Lms/CertificateController.php:44
* @route '/api/lms/certificates/{id}/revoke'
*/
revokeForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: revoke.url(args, options),
    method: 'post',
})

revoke.form = revokeForm

const CertificateController = { index, show, download, verify, revoke }

export default CertificateController